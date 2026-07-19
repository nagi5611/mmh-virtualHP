// public_html/diorama/dev/assets/diorama-admin.js — ジオラマお知らせ管理

/** @type {Array<Record<string, unknown>>} */
let newsItems = [];
/** @type {Set<number>} */
const selectedIds = new Set();
/** @type {number|null} */
let editingId = null;
/** @type {unknown} */
let sortableInstance = null;

let searchTerm = "";
let sortValue = "order";

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

async function loadNews() {
    const params = new URLSearchParams();
    if (searchTerm) params.set("search", searchTerm);
    params.set("sort", sortValue);
    const res = await fetch(`api/news.php?${params.toString()}`);
    const result = await res.json();
    if (!isApiSuccess(result)) {
        newsItems = [];
        showToast(result.error || "読み込みに失敗しました", "error");
    } else {
        newsItems = Array.isArray(result.data) ? result.data : [];
    }
    renderList();
}

function renderList() {
    const grid = document.getElementById("newsGrid");
    if (!grid) return;

    if (newsItems.length === 0) {
        grid.innerHTML = '<div class="loading">お知らせがありません</div>';
        destroySortable();
        syncBulkDelete();
        return;
    }

    grid.className = "items-list";
    grid.innerHTML = newsItems
        .map(
            (item) => `
        <div class="item-list-row" data-id="${item.id}">
            <div class="item-checkbox">
                <input type="checkbox" ${selectedIds.has(Number(item.id)) ? "checked" : ""}
                    data-select-id="${item.id}">
            </div>
            <div class="item-list-content">
                <div class="item-list-main">
                    <div class="item-title">${escapeHtml(String((item.text && item.text.ja) || ""))}</div>
                    <div class="item-meta">${escapeHtml(String(item.date || ""))}</div>
                </div>
            </div>
            <div class="item-list-actions">
                <button type="button" class="item-btn" data-action="edit" data-id="${item.id}">編集</button>
                <button type="button" class="item-btn danger" data-action="delete" data-id="${item.id}">削除</button>
            </div>
        </div>`
        )
        .join("");

    grid.querySelectorAll("[data-select-id]").forEach((input) => {
        input.addEventListener("change", (e) => {
            const id = Number(/** @type {HTMLInputElement} */ (e.target).dataset.selectId);
            if (/** @type {HTMLInputElement} */ (e.target).checked) selectedIds.add(id);
            else selectedIds.delete(id);
            syncBulkDelete();
        });
    });

    grid.querySelectorAll("[data-action]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = Number(btn.getAttribute("data-id"));
            const action = btn.getAttribute("data-action");
            if (action === "edit") openEditModal(id);
            if (action === "delete") deleteItem(id);
        });
    });

    initSortable();
    syncBulkDelete();
}

function destroySortable() {
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }
}

function initSortable() {
    const grid = document.getElementById("newsGrid");
    if (!grid || typeof Sortable === "undefined") return;
    destroySortable();
    sortableInstance = Sortable.create(grid, {
        animation: 150,
        ghostClass: "sortable-ghost",
        draggable: ".item-list-row",
        onEnd: () => void saveOrder(),
    });
}

async function saveOrder() {
    const grid = document.getElementById("newsGrid");
    if (!grid) return;
    const rows = grid.querySelectorAll(".item-list-row");
    const orders = Array.from(rows).map((row) => Number(row.getAttribute("data-id")));
    try {
        const res = await fetch("api/news.php", {
            method: "PATCH",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ orders }),
        });
        const result = await res.json();
        if (isApiSuccess(result)) {
            await loadNews();
            showToast("並び順を保存しました", "success");
        } else {
            showToast(result.error || "並び順の保存に失敗しました", "error");
        }
    } catch (e) {
        showToast("並び順の保存に失敗しました", "error");
        console.error(e);
    }
}

function syncBulkDelete() {
    const btn = document.getElementById("bulkDeleteBtn");
    if (btn) btn.hidden = selectedIds.size === 0;
}

function openAddModal() {
    editingId = null;
    document.getElementById("modalTitle").textContent = "お知らせを追加";
    document.getElementById("modalBody").innerHTML = getFormHtml(null);
    openModal();
}

function openEditModal(id) {
    const item = newsItems.find((n) => Number(n.id) === id);
    if (!item) return;
    editingId = id;
    document.getElementById("modalTitle").textContent = "お知らせを編集";
    document.getElementById("modalBody").innerHTML = getFormHtml(item);
    openModal();
}

function openModal() {
    const m = document.getElementById("itemModal");
    m.classList.add("active");
    m.setAttribute("aria-hidden", "false");
}

function closeModal() {
    const m = document.getElementById("itemModal");
    m.classList.remove("active");
    m.setAttribute("aria-hidden", "true");
}

/**
 * @param {Record<string, unknown>|null} item
 */
function getFormHtml(item) {
    const t = (item && item.text) || {};
    const ja = String(t.ja || "");
    const en = String(t.en || "");
    const zh = String(t.zh || "");
    const zhTw = String(t["zh-TW"] || "");
    const ko = String(t.ko || "");
    const date = String(item?.date || "");
    const datetime = String(item?.datetime || "");
    const link = String(item?.link || "");
    return `
        <div class="form-group">
            <label class="form-label required" for="f-date">表示日付</label>
            <input type="text" class="form-input" id="f-date" name="date" required placeholder="2026.05.15" value="${escapeHtml(date)}">
        </div>
        <div class="form-group">
            <label class="form-label" for="f-datetime">&lt;time datetime&gt; 用（任意）</label>
            <input type="text" class="form-input" id="f-datetime" name="datetime" placeholder="2026-05-15" value="${escapeHtml(datetime)}">
            <p class="form-hint">空のときは表示日付から推測されます（保存時にサーバーで補完）。</p>
        </div>
        <div class="form-group">
            <label class="form-label" for="f-link">リンク先（任意）</label>
            <input type="text" class="form-input" id="f-link" name="link" placeholder="#about または URL" value="${escapeHtml(link)}">
            <p class="form-hint">空ならテキストのみ表示（リンクなし）。</p>
        </div>
        <div class="form-group">
            <div class="form-group-header">
                <label class="form-label required" for="f-ja" style="margin:0;">日本語</label>
                <button type="button" class="btn btn-secondary" id="btnTranslateConfirm">確定</button>
            </div>
            <textarea class="form-textarea" id="f-ja" name="textJa" required>${escapeHtml(ja)}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="f-en">English</label>
            <textarea class="form-textarea" id="f-en" name="textEn">${escapeHtml(en)}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="f-zh">中文（简体）</label>
            <textarea class="form-textarea" id="f-zh" name="textZh">${escapeHtml(zh)}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="f-zhtw">中文（繁體・台灣）</label>
            <textarea class="form-textarea" id="f-zhtw" name="textZhTw">${escapeHtml(zhTw)}</textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="f-ko">한국어</label>
            <textarea class="form-textarea" id="f-ko" name="textKo">${escapeHtml(ko)}</textarea>
        </div>
    `;
}

async function handleTranslateConfirm() {
    const jaEl = document.getElementById("f-ja");
    const text = jaEl && jaEl.value.trim();
    if (!text) {
        showToast("日本語を入力してください", "error");
        return;
    }
    const btn = document.getElementById("btnTranslateConfirm");
    showToast("高精度翻訳中…", "info", 0);
    if (btn) {
        btn.disabled = true;
        btn.textContent = "高精度翻訳中…";
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
        const setVal = (id, key) => {
            const v = d[key];
            const el = document.getElementById(id);
            if (el && typeof v === "string") el.value = v;
        };
        setVal("f-en", "en");
        setVal("f-zh", "zh");
        setVal("f-zhtw", "zh-TW");
        setVal("f-ko", "ko");
        if (typeof d.ja === "string" && jaEl) jaEl.value = d.ja;
        showToast(result.status === "partial_success" ? "一部のみ翻訳を反映しました" : "翻訳を反映しました", "success");
    } catch (e) {
        showToast("翻訳リクエストに失敗しました", "error");
        console.error(e);
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.textContent = "確定";
        }
    }
}

async function handleFormSubmit(ev) {
    ev.preventDefault();
    const form = document.getElementById("itemForm");
    const fd = new FormData(form);
    const body = {
        date: String(fd.get("date") || "").trim(),
        datetime: String(fd.get("datetime") || "").trim(),
        link: String(fd.get("link") || "").trim(),
        text: {
            ja: String(fd.get("textJa") || "").trim(),
            en: String(fd.get("textEn") || "").trim(),
            zh: String(fd.get("textZh") || "").trim(),
            "zh-TW": String(fd.get("textZhTw") || "").trim(),
            ko: String(fd.get("textKo") || "").trim(),
        },
    };
    if (!body.date || !body.text.ja) {
        showToast("日付と日本語は必須です", "error");
        return;
    }

    const isNew = editingId === null;
    const url = "api/news.php";
    const method = isNew ? "POST" : "PUT";
    if (!isNew) body.id = editingId;

    try {
        const res = await fetch(url, {
            method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(body),
        });
        const result = await res.json();
        if (isApiSuccess(result)) {
            showToast(isNew ? "作成しました" : "更新しました", "success");
            closeModal();
            await loadNews();
        } else {
            showToast(result.error || "保存に失敗しました", "error");
        }
    } catch (e) {
        showToast("保存に失敗しました", "error");
        console.error(e);
    }
}

async function deleteItem(id) {
    if (!window.confirm("このお知らせを削除しますか？")) return;
    try {
        const res = await fetch("api/news.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ids: [id] }),
        });
        const result = await res.json();
        if (isApiSuccess(result)) {
            selectedIds.delete(id);
            showToast("削除しました", "success");
            await loadNews();
        } else {
            showToast(result.error || "削除に失敗しました", "error");
        }
    } catch (e) {
        showToast("削除に失敗しました", "error");
        console.error(e);
    }
}

async function bulkDelete() {
    if (selectedIds.size === 0) return;
    if (!window.confirm(`${selectedIds.size} 件を削除しますか？`)) return;
    try {
        const res = await fetch("api/news.php", {
            method: "DELETE",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ ids: Array.from(selectedIds) }),
        });
        const result = await res.json();
        if (isApiSuccess(result)) {
            selectedIds.clear();
            showToast("削除しました", "success");
            await loadNews();
        } else {
            showToast(result.error || "削除に失敗しました", "error");
        }
    } catch (e) {
        showToast("削除に失敗しました", "error");
        console.error(e);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("addNewBtn")?.addEventListener("click", openAddModal);
    document.getElementById("bulkDeleteBtn")?.addEventListener("click", bulkDelete);
    document.getElementById("itemForm")?.addEventListener("submit", handleFormSubmit);

    document.querySelectorAll("[data-close-modal='news']").forEach((el) => {
        el.addEventListener("click", closeModal);
    });

    const searchInput = document.getElementById("searchInput");
    let searchTimer;
    searchInput?.addEventListener("input", () => {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => {
            searchTerm = searchInput.value.trim().toLowerCase();
            void loadNews();
        }, 300);
    });

    document.getElementById("sortSelect")?.addEventListener("change", (e) => {
        sortValue = /** @type {HTMLSelectElement} */ (e.target).value;
        void loadNews();
    });

    document.getElementById("selectAllNews")?.addEventListener("change", (e) => {
        const on = /** @type {HTMLInputElement} */ (e.target).checked;
        if (on) {
            newsItems.forEach((n) => selectedIds.add(Number(n.id)));
        } else {
            selectedIds.clear();
        }
        renderList();
    });

    document.getElementById("itemForm")?.addEventListener("click", (ev) => {
        const t = /** @type {HTMLElement} */ (ev.target);
        if (t && t.id === "btnTranslateConfirm") {
            void handleTranslateConfirm();
        }
    });

    void loadNews();
});

window.NewsAdmin = { loadNews };
