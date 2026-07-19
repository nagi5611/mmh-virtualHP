// public_html/airport/dev/assets/awards-admin.js — 受賞歴管理（WYSIWYG 3TOP + タブ式ポップアップ編集）

/** @type {Array<Record<string, unknown>>} */
let awardItems = [];
/** @type {Set<number>} */
const awardSelectedIds = new Set();
/** @type {unknown} */
let awardSortableInstance = null;

let awardSearchTerm = "";
/** @type {number|null} */
let activeAwardId = null;
/** @type {"ja"|"en"|"zh"|"zh-TW"|"ko"} */
let popupDescLang = "ja";
/** @type {number|null} */
let popupDescAwardId = null;

const DESC_LANGS = [
    { key: "ja", label: "ja" },
    { key: "en", label: "en" },
    { key: "zh", label: "zh" },
    { key: "zh-TW", label: "zh-TW" },
    { key: "ko", label: "ko" },
];
const STATUS_LABELS = {
    won: "受賞",
    finalist: "ファイナリスト",
    first_pass: "1次通過",
    ongoing: "進行中",
    nominated: "ノミネート",
};

function escapeHtml(s) {
    return String(s)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

function isApiSuccess(result) {
    if (!result || typeof result !== "object") return false;
    if (result.error) return false;
    if (result.data !== undefined) return true;
    return Boolean(result.status);
}

function showToast(message, type, durationMs) {
    const el = document.getElementById("toast");
    if (!el) return;
    if (showToast._t) {
        window.clearTimeout(showToast._t);
        showToast._t = null;
    }
    el.textContent = message;
    el.hidden = false;
    el.classList.remove("is-error", "is-success", "is-info");
    if (type === "error") el.classList.add("is-error");
    else if (type === "success") el.classList.add("is-success");
    else if (type === "info") el.classList.add("is-info");
    const ms = typeof durationMs === "number" ? durationMs : 3200;
    if (ms > 0) {
        showToast._t = window.setTimeout(() => {
            el.hidden = true;
            showToast._t = null;
        }, ms);
    }
}

/**
 * @returns {Array<Record<string, unknown>>}
 */
function getHpAwards() {
    return awardItems
        .filter((item) => item.featured && item.published)
        .sort((a, b) => Number(a.order ?? 999999) - Number(b.order ?? 999999));
}

/**
 * @param {number} id
 * @returns {Record<string, unknown>|null}
 */
function getAwardById(id) {
    return awardItems.find((a) => Number(a.id) === Number(id)) || null;
}

/**
 * @returns {Record<string, unknown>|null}
 */
function getActiveAward() {
    if (activeAwardId != null) {
        const item = getAwardById(activeAwardId);
        if (item && item.featured && item.published) return item;
    }
    const hp = getHpAwards();
    return hp[0] || null;
}

/**
 * @param {Record<string, unknown>} item
 */
function buildEditableOrbHtml(item) {
    const id = Number(item.id);
    const result = String((item.result && item.result.ja) || "");
    const org = String((item.organization && item.organization.ja) || "");
    const selectedClass = activeAwardId === id ? " award-orb--selected" : "";

    return `<li class="award-orb${selectedClass}" role="listitem" data-award-id="${id}">
      <div class="award-orb__button award-orb__button--editable">
        <button type="button" class="award-orb__remove" data-remove-orb="${id}" aria-label="この受賞を削除">×</button>
        <p class="award-orb__result">
          <span class="inline-edit" contenteditable="true" spellcheck="false" data-inline-field="result" data-placeholder="受賞名">${escapeHtml(result || "受賞名")}</span>
        </p>
        <p class="award-orb__org">
          <span class="inline-edit" contenteditable="true" spellcheck="false" data-inline-field="organization" data-placeholder="コンテスト名">${escapeHtml(org || "コンテスト名")}</span>
        </p>
      </div>
      <button type="button" class="award-orb__more" data-goto-award="${id}">↑ 詳しく</button>
    </li>`;
}

/**
 * @param {Record<string, unknown>} draft
 */
function buildDetailPreviewHtml(draft) {
    const desc = String(draft.desc || draft.descJa || "");
    const thumb = String(draft.thumbnail || "");
    const frame = draft.thumbnailFrame || window.AwardThumbEditor?.DEFAULT_FRAME;
    const attachments = Array.isArray(draft.attachments) ? draft.attachments : [];

    const thumbBlock = thumb
        ? window.AwardThumbEditor
            ? window.AwardThumbEditor.buildThumbMarkup(`../${thumb}`, frame, true)
            : `<div class="award-detail__thumb-wrap"><img class="award-detail__thumb" src="../${escapeHtml(thumb)}" alt=""></div>`
        : `<div class="award-detail__thumb-wrap"><div class="award-detail__thumb-placeholder">サムネ画像</div></div>`;

    const filesHtml =
        attachments.length === 0
            ? '<li class="award-detail__files-empty">添付ファイル</li>'
            : attachments
                  .map((att) => {
                      const a = /** @type {Record<string, unknown>} */ (att);
                      return `<li><span class="award-detail__file-link award-detail__file-link--static">${escapeHtml(String(a.label || "ファイル"))}</span></li>`;
                  })
                  .join("");

    return `<div class="award-detail__panel award-detail__panel--preview">
      ${thumbBlock}
      <div class="award-detail__body">
        <div class="award-detail__desc-wrap">
          <p class="award-detail__desc">${escapeHtml(desc || "説明テキストを入力…")}</p>
        </div>
        <div class="award-detail__files-wrap">
          <p class="award-detail__files-label">資料</p>
          <ul class="award-detail__files">${filesHtml}</ul>
        </div>
      </div>
    </div>`;
}

function syncDescFromTextarea() {
    const item = getActiveAward();
    const form = document.getElementById("awardPopupForm");
    const descEl = form?.querySelector("[data-popup-desc]");
    if (!item || !(descEl instanceof HTMLTextAreaElement)) return;
    if (!item.description || typeof item.description !== "object") {
        item.description = { ja: "", en: "", zh: "", "zh-TW": "", ko: "" };
    }
    const desc = /** @type {Record<string, string>} */ (item.description);
    desc[popupDescLang] = descEl.value;
}

/**
 * @param {"ja"|"en"|"zh"|"zh-TW"|"ko"} lang
 */
function switchPopupDescLang(lang) {
    syncDescFromTextarea();
    popupDescLang = lang;
    const item = getActiveAward();
    const form = document.getElementById("awardPopupForm");
    const descEl = form?.querySelector("[data-popup-desc]");
    if (descEl instanceof HTMLTextAreaElement && item?.description) {
        const desc = /** @type {Record<string, string>} */ (item.description);
        descEl.value = String(desc[lang] || "");
    }
    form?.querySelectorAll("[data-desc-lang]").forEach((btn) => {
        btn.classList.toggle("is-active", btn.getAttribute("data-desc-lang") === lang);
    });
    refreshPopupPreview();
}

function readPopupDraft() {
    syncDescFromTextarea();
    const item = getActiveAward();
    const desc =
        item?.description && typeof item.description === "object"
            ? String(/** @type {Record<string, string>} */ (item.description)[popupDescLang] || "")
            : "";
    return {
        desc,
        descJa: String((item?.description && item.description.ja) || ""),
        thumbnail: String(item?.thumbnail || ""),
        thumbnailFrame: item?.thumbnailFrame || window.AwardThumbEditor?.DEFAULT_FRAME,
        attachments: Array.isArray(item?.attachments) ? [...item.attachments] : [],
    };
}

function renderHpOrbs() {
    const root = document.getElementById("adminHpOrbs");
    if (!root) return;
    const awards = getHpAwards();
    const orbsHtml = awards.map((item) => buildEditableOrbHtml(item)).join("");
    const addOrb = `<li class="award-orb award-orb--add" role="listitem">
      <button type="button" class="award-orb__add" data-create-hp-award>＋ 受賞を追加</button>
    </li>`;
    root.innerHTML = orbsHtml + addOrb;
    bindHpOrbEditors();
    if (window.AwardsOrbCarousel) {
        const index = awards.findIndex((a) => Number(a.id) === Number(activeAwardId));
        window.AwardsOrbCarousel.init(root, { initialIndex: index >= 0 ? index : 0 });
    }
}

function bindHpOrbEditors() {
    const root = document.getElementById("adminHpOrbs");
    if (!root) return;

    root.querySelectorAll(".inline-edit").forEach((el) => {
        const field = /** @type {HTMLElement} */ (el);
        field.addEventListener("focus", () => {
            const sel = window.getSelection();
            const range = document.createRange();
            range.selectNodeContents(field);
            sel?.removeAllRanges();
            sel?.addRange(range);
        });
        field.addEventListener("keydown", (ev) => {
            if (ev.key === "Enter") {
                ev.preventDefault();
                field.blur();
            }
        });
        field.addEventListener("paste", (ev) => {
            ev.preventDefault();
            const text = ev.clipboardData?.getData("text/plain") || "";
            document.execCommand("insertText", false, text.replace(/\r?\n/g, " "));
        });
        field.addEventListener("blur", () => {
            void saveInlineField(field);
        });
    });

    root.querySelectorAll(".award-orb:not(.award-orb--add)").forEach((orb) => {
        orb.addEventListener("click", (ev) => {
            const t = /** @type {HTMLElement} */ (ev.target);
            if (t.closest(".award-orb__remove, .inline-edit, [data-goto-award]")) return;
            const id = Number(orb.getAttribute("data-award-id"));
            if (!Number.isNaN(id)) setActiveAward(id, { scroll: false });
        });
    });

    root.querySelectorAll("[data-goto-award]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = Number(btn.getAttribute("data-goto-award"));
            if (!Number.isNaN(id)) setActiveAward(id);
        });
    });

    root.querySelectorAll("[data-remove-orb]").forEach((btn) => {
        btn.addEventListener("click", (ev) => {
            ev.stopPropagation();
            const id = Number(btn.getAttribute("data-remove-orb"));
            if (!Number.isNaN(id)) void deleteAwardItem(id);
        });
    });

    root.querySelector("[data-create-hp-award]")?.addEventListener("click", () => void createHpAward());
}

/**
 * @param {HTMLElement} fieldEl
 */
async function saveInlineField(fieldEl) {
    const orb = fieldEl.closest(".award-orb");
    if (!orb) return;
    const awardId = Number(orb.getAttribute("data-award-id"));
    if (!awardId) return;

    const item = awardItems.find((a) => Number(a.id) === awardId);
    if (!item) return;

    const inlineField = fieldEl.getAttribute("data-inline-field");
    const text = fieldEl.textContent?.trim() || "";
    if (!text) {
        showToast("空にはできません", "error");
        renderHpOrbs();
        return;
    }

    const body = {
        id: awardId,
        organization: { ...(item.organization || {}), ja: String((item.organization && item.organization.ja) || "") },
        result: { ...(item.result || {}), ja: String((item.result && item.result.ja) || "") },
        description: item.description || { ja: "", en: "", zh: "", "zh-TW": "", ko: "" },
        thumbnail: String(item.thumbnail || ""),
        attachments: Array.isArray(item.attachments) ? item.attachments : [],
        featured: true,
        published: true,
        year: String(item.year || new Date().getFullYear()),
        status: String(item.status || "nominated"),
    };

    if (inlineField === "result") {
        body.result = { ...body.result, ja: text };
    } else if (inlineField === "organization") {
        body.organization = { ...body.organization, ja: text };
        body.title = { ...(item.title || {}), ja: text };
    } else {
        return;
    }

    try {
        const res = await fetch("api/awards.php", {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body),
        });
        const result = await res.json();
        if (!isApiSuccess(result)) {
            showToast(result.error || "保存に失敗しました", "error");
            return;
        }
        const idx = awardItems.findIndex((a) => Number(a.id) === awardId);
        if (idx >= 0 && result.data) awardItems[idx] = result.data;
        showToast("保存しました", "success", 1800);
    } catch (e) {
        showToast("保存に失敗しました", "error");
        console.error(e);
    }
}

function renderPopupForm() {
    const form = document.getElementById("awardPopupForm");
    if (!form) return;
    const item = getActiveAward();
    updatePopupEditorTitle();

    if (!item) {
        form.innerHTML = `
      <div class="award-popup-empty">
        <p>プレビューで受賞を選択するか、「＋ 受賞を追加」で新規作成してください。</p>
      </div>`;
        refreshPopupPreview();
        return;
    }

    if (Number(item.id) !== popupDescAwardId) {
        popupDescAwardId = Number(item.id);
        popupDescLang = "ja";
    }

    if (!item.description || typeof item.description !== "object") {
        item.description = { ja: "", en: "", zh: "", "zh-TW": "", ko: "" };
    }
    const descObj = /** @type {Record<string, string>} */ (item.description);
    const descValue = String(descObj[popupDescLang] || "");
    const langTabs = DESC_LANGS.map(
        ({ key, label }) =>
            `<button type="button" class="award-desc-editor__lang${popupDescLang === key ? " is-active" : ""}" data-desc-lang="${key}" role="tab" aria-selected="${popupDescLang === key ? "true" : "false"}">${label}</button>`
    ).join("");

    const thumbnail = String(item.thumbnail || "");
    const attachments = Array.isArray(item.attachments) ? item.attachments : [];

    const attachList = attachments
        .map((att, i) => {
            const a = /** @type {Record<string, unknown>} */ (att);
            return `<li class="award-attach-item">
          <span>${escapeHtml(String(a.label || "ファイル"))}</span>
          <button type="button" class="item-btn danger" data-remove-attach="${i}">削除</button>
        </li>`;
        })
        .join("");

    const thumbList = thumbnail
        ? `<ul class="award-attach-list award-thumb-list">
        <li class="award-attach-item">
          <span>${escapeHtml(thumbnail.split("/").pop() || thumbnail)}</span>
          <button type="button" class="item-btn danger" data-remove-thumbnail>削除</button>
        </li>
      </ul>`
        : "";

    form.innerHTML = `
    <div class="award-popup-form-inner" data-award-id="${Number(item.id)}">
      <div class="form-group">
        <label class="form-label">サムネ</label>
        <div class="drop-zone" data-drop="thumbnail" tabindex="0">
          <input type="file" class="drop-zone__input" accept="image/*" hidden>
          <p class="drop-zone__hint">ドラッグ＆ドロップ<br>またはクリックして選択</p>
          ${thumbnail ? `<p class="drop-zone__current">現在: ${escapeHtml(thumbnail)}</p>` : ""}
        </div>
        ${thumbList}
      </div>
      <div class="form-group">
        <label class="form-label" for="popup-desc">説明</label>
        <div class="award-desc-editor" data-desc-editor>
          <div class="award-desc-editor__langs" role="tablist" aria-label="説明の言語">
            ${langTabs}
          </div>
          <div class="award-desc-editor__main">
            <textarea class="form-textarea award-desc-editor__textarea" id="popup-desc" data-popup-desc rows="6" placeholder="ポップアップに表示する説明文…">${escapeHtml(descValue)}</textarea>
            <button type="button" class="btn btn-secondary award-desc-editor__translate" data-desc-translate>翻訳</button>
          </div>
        </div>
        <p class="form-hint">翻訳は日本語（ja）を元に en / zh / zh-TW / ko へ反映します。</p>
      </div>
      <div class="form-group">
        <label class="form-label">添付ファイル</label>
        <div class="drop-zone" data-drop="attachment" tabindex="0">
          <input type="file" class="drop-zone__input" accept=".pdf,.mp4,.webm,.mov,.doc,.docx,.ppt,.pptx" hidden>
          <p class="drop-zone__hint">ドラッグ＆ドロップ<br>またはクリックして選択</p>
        </div>
        <ul class="award-attach-list">${attachList}</ul>
      </div>
      <button type="button" class="btn btn-primary" data-save-popup>説明・添付を保存</button>
    </div>`;

    bindPopupForm();
    refreshPopupPreview();
}

function bindPopupForm() {
    const form = document.getElementById("awardPopupForm");
    if (!form) return;

    const descEl = form.querySelector("[data-popup-desc]");
    descEl?.addEventListener("input", () => {
        syncDescFromTextarea();
        refreshPopupPreview();
    });

    form.querySelectorAll("[data-desc-lang]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const lang = btn.getAttribute("data-desc-lang");
            if (lang === "ja" || lang === "en" || lang === "zh" || lang === "zh-TW" || lang === "ko") {
                switchPopupDescLang(lang);
            }
        });
    });

    form.querySelector("[data-desc-translate]")?.addEventListener("click", () => void handleAwardDescTranslate());

    form.querySelectorAll(".drop-zone").forEach((zone) => {
        const z = /** @type {HTMLElement} */ (zone);
        const input = z.querySelector(".drop-zone__input");
        const kind = z.getAttribute("data-drop");

        z.addEventListener("click", () => {
            if (input instanceof HTMLInputElement) input.click();
        });
        z.addEventListener("dragover", (ev) => {
            ev.preventDefault();
            z.classList.add("drop-zone--over");
        });
        z.addEventListener("dragleave", () => z.classList.remove("drop-zone--over"));
        z.addEventListener("drop", (ev) => {
            ev.preventDefault();
            z.classList.remove("drop-zone--over");
            const file = ev.dataTransfer?.files?.[0];
            if (file && kind) void handlePopupFileUpload(kind, file);
        });
        if (input instanceof HTMLInputElement) {
            input.addEventListener("change", () => {
                const file = input.files?.[0];
                if (file && kind) void handlePopupFileUpload(kind, file);
                input.value = "";
            });
        }
    });

    form.querySelectorAll("[data-remove-attach]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const idx = Number(btn.getAttribute("data-remove-attach"));
            const item = getActiveAward();
            if (!item || !Array.isArray(item.attachments)) return;
            item.attachments.splice(idx, 1);
            void savePopupData(true).then(() => renderPopupForm());
        });
    });

    form.querySelector("[data-remove-thumbnail]")?.addEventListener("click", () => {
        void removeThumbnail();
    });

    form.querySelector("[data-save-popup]")?.addEventListener("click", () => void savePopupData());
}

function refreshPopupPreview() {
    const preview = document.getElementById("awardPopupPreview");
    if (!preview) return;
    const item = getActiveAward();
    preview.innerHTML = buildDetailPreviewHtml(readPopupDraft());
    bindPreviewThumbEditor(item);
}

/**
 * @param {Record<string, unknown>|null} item
 */
function bindPreviewThumbEditor(item) {
    const preview = document.getElementById("awardPopupPreview");
    const viewport = preview?.querySelector("[data-thumb-viewport]");
    if (!viewport || !(viewport instanceof HTMLElement) || !item || !window.AwardThumbEditor) return;

    let saveTimer = null;
    window.AwardThumbEditor.bindThumbEditor(viewport, (frame) => {
        item.thumbnailFrame = frame;
        if (saveTimer) window.clearTimeout(saveTimer);
        saveTimer = window.setTimeout(() => {
            void savePopupData(true);
        }, 400);
    });
}

async function removeThumbnail() {
    const item = getActiveAward();
    if (!item || !item.thumbnail) return;
    if (!window.confirm("サムネ画像を削除しますか？")) return;

    const awardId = Number(item.id);
    try {
        const res = await fetch("api/awards-upload.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ awardId, kind: "thumbnail" }),
        });
        const result = await res.json();
        if (!isApiSuccess(result)) {
            showToast(result.error || "サムネの削除に失敗しました", "error");
            return;
        }
        item.thumbnail = "";
        item.thumbnailFrame = window.AwardThumbEditor?.DEFAULT_FRAME || {
            width: 100,
            height: 0,
            cropLeft: 0,
            cropTop: 0,
            cropRight: 0,
            cropBottom: 0,
        };
        await savePopupData(true);
        showToast("サムネを削除しました", "success");
        renderPopupForm();
    } catch (e) {
        showToast("サムネの削除に失敗しました", "error");
        console.error(e);
    }
}

/**
 * @param {string} kind
 * @param {File} file
 */
async function handlePopupFileUpload(kind, file) {
    const item = getActiveAward();
    if (!item) {
        showToast("先に受賞歴を作成してください", "error");
        return;
    }
    const awardId = Number(item.id);
    const fd = new FormData();
    fd.append("awardId", String(awardId));
    fd.append("kind", kind);
    fd.append("file", file);
    showToast("アップロード中…", "info", 0);
    try {
        const res = await fetch("api/awards-upload.php", { method: "POST", body: fd });
        const result = await res.json();
        if (!isApiSuccess(result)) {
            showToast(result.error || "アップロードに失敗しました", "error");
            return;
        }
        if (kind === "thumbnail" && result.data?.thumbnail) {
            item.thumbnail = result.data.thumbnail;
            item.thumbnailFrame = window.AwardThumbEditor?.DEFAULT_FRAME || {
                width: 100,
                height: 0,
                cropLeft: 0,
                cropTop: 0,
                cropRight: 0,
                cropBottom: 0,
            };
        }
        if (kind === "attachment" && result.data?.attachment) {
            if (!Array.isArray(item.attachments)) item.attachments = [];
            item.attachments.push(result.data.attachment);
        }
        await savePopupData(true);
        showToast("アップロードしました", "success");
        renderPopupForm();
    } catch (e) {
        showToast("アップロードに失敗しました", "error");
        console.error(e);
    }
}

async function handleAwardDescTranslate() {
    syncDescFromTextarea();
    const item = getActiveAward();
    const text = String(item?.description?.ja || "").trim();
    if (!text) {
        showToast("日本語の説明を入力してください", "error");
        return;
    }

    const btn = document.querySelector("[data-desc-translate]");
    showToast("高精度翻訳中…", "info", 0);
    if (btn instanceof HTMLButtonElement) {
        btn.disabled = true;
        btn.textContent = "翻訳中…";
    }

    try {
        const res = await fetch("api/translate.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ text }),
        });
        const result = await res.json();
        if (!isApiSuccess(result)) {
            showToast(result.error || "翻訳に失敗しました", "error");
            return;
        }

        const d = result.data || {};
        if (!item.description || typeof item.description !== "object") {
            item.description = { ja: "", en: "", zh: "", "zh-TW": "", ko: "" };
        }
        const desc = /** @type {Record<string, string>} */ (item.description);
        if (typeof d.ja === "string") desc.ja = d.ja;
        if (typeof d.en === "string") desc.en = d.en;
        if (typeof d.zh === "string") desc.zh = d.zh;
        if (typeof d["zh-TW"] === "string") desc["zh-TW"] = d["zh-TW"];
        if (typeof d.ko === "string") desc.ko = d.ko;

        const form = document.getElementById("awardPopupForm");
        const descEl = form?.querySelector("[data-popup-desc]");
        if (descEl instanceof HTMLTextAreaElement) {
            descEl.value = String(desc[popupDescLang] || "");
        }

        refreshPopupPreview();
        showToast(
            result.status === "partial_success" ? "一部のみ翻訳を反映しました" : "翻訳を反映しました",
            "success"
        );
    } catch (e) {
        showToast("翻訳リクエストに失敗しました", "error");
        console.error(e);
    } finally {
        if (btn instanceof HTMLButtonElement) {
            btn.disabled = false;
            btn.textContent = "翻訳";
        }
    }
}

/**
 * @param {boolean} [silent]
 */
async function savePopupData(silent) {
    const item = getActiveAward();
    if (!item) return;
    syncDescFromTextarea();

    const body = {
        id: Number(item.id),
        year: String(item.year || new Date().getFullYear()),
        status: String(item.status || "nominated"),
        featured: true,
        published: true,
        organization: item.organization || { ja: "", en: "", zh: "", "zh-TW": "", ko: "" },
        title: item.title || item.organization || { ja: "", en: "", zh: "", "zh-TW": "", ko: "" },
        result: item.result || { ja: "", en: "", zh: "", "zh-TW": "", ko: "" },
        description: {
            ja: "",
            en: "",
            zh: "",
            "zh-TW": "",
            ko: "",
            ...(item.description || {}),
        },
        thumbnail: String(item.thumbnail || ""),
        thumbnailFrame: item.thumbnailFrame || window.AwardThumbEditor?.DEFAULT_FRAME,
        attachments: Array.isArray(item.attachments) ? item.attachments : [],
    };

    try {
        const res = await fetch("api/awards.php", {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body),
        });
        const result = await res.json();
        if (!isApiSuccess(result)) {
            showToast(result.error || "保存に失敗しました", "error");
            return;
        }
        const idx = awardItems.findIndex((a) => Number(a.id) === Number(item.id));
        if (idx >= 0 && result.data) awardItems[idx] = result.data;
        if (!silent) {
            showToast("保存しました", "success");
            refreshPopupPreview();
        }
    } catch (e) {
        showToast("保存に失敗しました", "error");
        console.error(e);
    }
}

async function createHpAward() {
    const hp = getHpAwards();
    const maxOrder = hp.reduce((max, a) => Math.max(max, Number(a.order ?? 0)), -1);
    const body = {
        year: String(new Date().getFullYear()),
        status: "nominated",
        featured: true,
        published: true,
        organization: { ja: "コンテスト名", en: "", zh: "", "zh-TW": "", ko: "" },
        title: { ja: "コンテスト名", en: "", zh: "", "zh-TW": "", ko: "" },
        result: { ja: "受賞名", en: "", zh: "", "zh-TW": "", ko: "" },
        description: { ja: "", en: "", zh: "", "zh-TW": "", ko: "" },
        thumbnail: "",
        attachments: [],
        order: maxOrder + 1,
    };

    try {
        const res = await fetch("api/awards.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body),
        });
        const result = await res.json();
        if (!isApiSuccess(result)) {
            showToast(result.error || "作成に失敗しました", "error");
            return;
        }
        const newId = Number(result.data?.id);
        showToast("受賞を追加しました", "success");
        if (newId) activeAwardId = newId;
        await loadAwards();
        if (newId) setActiveAward(newId);
    } catch (e) {
        showToast("作成に失敗しました", "error");
        console.error(e);
    }
}

function updatePopupEditorTitle() {
    const el = document.getElementById("awardPopupEditorTitle");
    const item = getActiveAward();
    if (!el) return;
    if (!item) {
        el.textContent = "ポップアップ詳細編集";
        return;
    }
    const name = String((item.result && item.result.ja) || "受賞名");
    el.textContent = `「${name}」のポップアップ編集`;
}

/**
 * @param {number} id
 * @param {{ scroll?: boolean }} [opts]
 */
function setActiveAward(id, opts) {
    activeAwardId = id;
    document.querySelectorAll(".award-orb[data-award-id]").forEach((orb) => {
        const orbId = Number(orb.getAttribute("data-award-id"));
        orb.classList.toggle("award-orb--selected", orbId === id);
    });
    renderPopupForm();
    if (opts?.scroll !== false) {
        const orb = document.querySelector(`.award-orb[data-award-id="${id}"]`);
        orb?.scrollIntoView({ inline: "center", block: "nearest", behavior: "smooth" });
        const track = document.getElementById("adminHpOrbs");
        if (track && window.AwardsOrbCarousel) {
            requestAnimationFrame(() => window.AwardsOrbCarousel.updateCenter(track));
        }
    }
}

function renderAwardEditor() {
    const hp = getHpAwards();
    if (activeAwardId != null && !hp.some((a) => Number(a.id) === Number(activeAwardId))) {
        activeAwardId = hp[0] ? Number(hp[0].id) : null;
    }
    if (activeAwardId == null && hp[0]) {
        activeAwardId = Number(hp[0].id);
    }
    renderHpOrbs();
    renderPopupForm();
}

async function loadAwards() {
    const params = new URLSearchParams();
    if (awardSearchTerm) params.set("search", awardSearchTerm);
    params.set("sort", "order");
    const res = await fetch(`api/awards.php?${params.toString()}`);
    const result = await res.json();
    if (!isApiSuccess(result)) {
        awardItems = [];
        showToast(result.error || "読み込みに失敗しました", "error");
    } else {
        awardItems = Array.isArray(result.data) ? result.data : [];
    }
    renderAwardEditor();
    renderAwardList();
}

function renderAwardList() {
    const grid = document.getElementById("awardsGrid");
    if (!grid) return;

    let items = [...awardItems];
    if (awardSearchTerm) {
        const q = awardSearchTerm.toLowerCase();
        items = items.filter((item) => {
            const blob = [
                (item.result && item.result.ja) || "",
                (item.organization && item.organization.ja) || "",
            ]
                .join(" ")
                .toLowerCase();
            return blob.includes(q);
        });
    }

    if (items.length === 0) {
        grid.innerHTML = '<div class="loading">受賞歴がありません</div>';
        destroyAwardSortable();
        return;
    }

    grid.innerHTML = items
        .map((item) => {
            const status = String(item.status || "nominated");
            const badges = [];
            if (item.featured) badges.push('<span class="item-tag item-tag--featured">HP代表</span>');
            if (item.published) badges.push('<span class="item-tag item-tag--published">公開</span>');
            return `
        <div class="item-list-row" data-id="${item.id}">
            <div class="item-checkbox">
                <input type="checkbox" ${awardSelectedIds.has(Number(item.id)) ? "checked" : ""} data-award-select-id="${item.id}">
            </div>
            <div class="item-list-content">
                <div class="item-list-main">
                    <div class="item-title">${escapeHtml(String((item.result && item.result.ja) || ""))}</div>
                    <div class="item-meta">${escapeHtml(String(item.year || ""))} · ${escapeHtml(String((item.organization && item.organization.ja) || ""))}</div>
                    <div class="item-tags">${badges.join("")} <span class="item-status-badge">${escapeHtml(STATUS_LABELS[status] || status)}</span></div>
                </div>
            </div>
            <div class="item-list-actions">
                <button type="button" class="item-btn danger" data-award-action="delete" data-id="${item.id}">削除</button>
            </div>
        </div>`;
        })
        .join("");

    grid.querySelectorAll("[data-award-select-id]").forEach((input) => {
        input.addEventListener("change", (e) => {
            const id = Number(/** @type {HTMLInputElement} */ (e.target).dataset.awardSelectId);
            if (/** @type {HTMLInputElement} */ (e.target).checked) awardSelectedIds.add(id);
            else awardSelectedIds.delete(id);
            syncAwardBulkDelete();
        });
    });
    grid.querySelectorAll("[data-award-action='delete']").forEach((btn) => {
        btn.addEventListener("click", () => void deleteAwardItem(Number(btn.getAttribute("data-id"))));
    });
    initAwardSortable();
    syncAwardBulkDelete();
}

function destroyAwardSortable() {
    if (awardSortableInstance) {
        awardSortableInstance.destroy();
        awardSortableInstance = null;
    }
}

function initAwardSortable() {
    const grid = document.getElementById("awardsGrid");
    if (!grid || typeof Sortable === "undefined") return;
    destroyAwardSortable();
    awardSortableInstance = Sortable.create(grid, {
        animation: 150,
        ghostClass: "sortable-ghost",
        draggable: ".item-list-row",
        onEnd: () => void saveAwardOrder(),
    });
}

async function saveAwardOrder() {
    const grid = document.getElementById("awardsGrid");
    if (!grid) return;
    const orders = Array.from(grid.querySelectorAll(".item-list-row")).map((row) =>
        Number(row.getAttribute("data-id"))
    );
    try {
        const res = await fetch("api/awards.php", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ orders }),
        });
        const result = await res.json();
        if (isApiSuccess(result)) {
            await loadAwards();
            showToast("並び順を保存しました", "success");
        } else {
            showToast(result.error || "並び順の保存に失敗しました", "error");
        }
    } catch (e) {
        showToast("並び順の保存に失敗しました", "error");
    }
}

function syncAwardBulkDelete() {
    const btn = document.getElementById("awardBulkDeleteBtn");
    if (btn) btn.hidden = awardSelectedIds.size === 0;
}

async function createBlankAward() {
    const body = {
        year: String(new Date().getFullYear()),
        status: "nominated",
        featured: false,
        published: false,
        organization: { ja: "新しいコンテスト", en: "", zh: "", "zh-TW": "", ko: "" },
        title: { ja: "部門名", en: "", zh: "", "zh-TW": "", ko: "" },
        result: { ja: "受賞名", en: "", zh: "", "zh-TW": "", ko: "" },
        description: { ja: "", en: "", zh: "", "zh-TW": "", ko: "" },
        thumbnail: "",
        attachments: [],
    };
    const res = await fetch("api/awards.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body),
    });
    const result = await res.json();
    if (isApiSuccess(result)) {
        showToast("受賞歴を追加しました（下書き）", "success");
        await loadAwards();
    } else {
        showToast(result.error || "追加に失敗しました", "error");
    }
}

async function deleteAwardItem(id) {
    if (!window.confirm("この受賞歴を削除しますか？")) return;
    try {
        const res = await fetch("api/awards.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ids: [id] }),
        });
        const result = await res.json();
        if (isApiSuccess(result)) {
            awardSelectedIds.delete(id);
            if (activeAwardId === id) activeAwardId = null;
            showToast("削除しました", "success");
            await loadAwards();
        } else {
            showToast(result.error || "削除に失敗しました", "error");
        }
    } catch (e) {
        showToast("削除に失敗しました", "error");
    }
}

async function awardBulkDelete() {
    if (awardSelectedIds.size === 0) return;
    if (!window.confirm(`${awardSelectedIds.size} 件を削除しますか？`)) return;
    try {
        const res = await fetch("api/awards.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ids: Array.from(awardSelectedIds) }),
        });
        const result = await res.json();
        if (isApiSuccess(result)) {
            awardSelectedIds.clear();
            showToast("削除しました", "success");
            await loadAwards();
        } else {
            showToast(result.error || "削除に失敗しました", "error");
        }
    } catch (e) {
        showToast("削除に失敗しました", "error");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("awardAddBtn")?.addEventListener("click", () => void createBlankAward());
    document.getElementById("awardBulkDeleteBtn")?.addEventListener("click", awardBulkDelete);

    const searchInput = document.getElementById("awardSearchInput");
    let searchTimer;
    searchInput?.addEventListener("input", () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => {
            awardSearchTerm = searchInput.value.trim().toLowerCase();
            renderAwardList();
        }, 300);
    });

    void loadAwards();
});

window.AwardsAdmin = { loadAwards };
