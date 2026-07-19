// public_html/assets/main.js — nav, smooth scroll, page loader
(function () {
  "use strict";

  const MIN_LOADER_MS = 480;

  /**
   * Smooth-scrolls to the element matching selector.
   * @param {string} selector
   */
  function scrollToSelector(selector) {
    if (!selector || !selector.startsWith("#")) return;
    const target = document.querySelector(selector);
    if (target) {
      target.scrollIntoView({ behavior: "smooth", block: "start" });
    }
  }

  /**
   * Hides the full-page loader after load + optional font wait + minimum display time.
   */
  function hidePageLoader() {
    const loader = document.getElementById("page-loader");
    if (!loader) return;

    const start = performance.now();
    const done = () => {
      const elapsed = performance.now() - start;
      const wait = Math.max(0, MIN_LOADER_MS - elapsed);
      window.setTimeout(() => {
        document.body.classList.add("is-loaded");
        loader.setAttribute("aria-busy", "false");
      }, wait);
    };

    const fontsReady = Promise.resolve();

    if (document.readyState === "complete") {
      fontsReady.then(done);
    } else {
      window.addEventListener("load", () => {
        fontsReady.then(done);
      });
    }
  }

  function initHeaderShadow() {
    const header = document.querySelector(".site-header");
    if (!header) return;

    let scrollTicking = false;
    const toggleHeaderShadow = () => {
      if (window.scrollY > 24) {
        header.classList.add("is-active");
      } else {
        header.classList.remove("is-active");
      }
    };
    const onScroll = () => {
      if (scrollTicking) return;
      scrollTicking = true;
      requestAnimationFrame(() => {
        toggleHeaderShadow();
        scrollTicking = false;
      });
    };
    toggleHeaderShadow();
    window.addEventListener("scroll", onScroll, { passive: true });
  }

  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((link) => {
      const href = link.getAttribute("href");
      if (!href || href.length < 2) return;
      link.addEventListener("click", (event) => {
        event.preventDefault();
        scrollToSelector(href);
        closeMobileNavIfOpen();
      });
    });
  }

  let closeMobileNavIfOpen = () => {};

  function initNavMenus() {
    document.querySelectorAll(".nav-toggle").forEach((toggle) => {
      const navLinks = toggle.nextElementSibling;
      if (!navLinks || !navLinks.classList.contains("nav-links")) return;

      const closeMenu = () => {
        toggle.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
        navLinks.classList.remove("is-open");
      };

      closeMobileNavIfOpen = closeMenu;

      toggle.addEventListener("click", () => {
        const isOpen = !toggle.classList.contains("is-open");
        if (isOpen) {
          toggle.classList.add("is-open");
          toggle.setAttribute("aria-expanded", "true");
          navLinks.classList.add("is-open");
        } else {
          closeMenu();
        }
      });

      navLinks.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => {
          if (window.innerWidth <= 720) {
            closeMenu();
          }
        });
      });

      window.addEventListener("resize", () => {
        if (window.innerWidth > 720) {
          closeMenu();
        }
      });
    });
  }

  function initCurrentYear() {
    document.querySelectorAll("[data-current-year]").forEach((el) => {
      el.textContent = String(new Date().getFullYear());
    });
  }

  /**
   * 初回描画直後にビュー内かどうかを判定する（レイアウト読み取りは呼び出し側で一括する）。
   * @param {Element} el
   * @param {number} vh
   * @returns {boolean}
   */
  function isElementInInitialViewport(el, vh) {
    const r = el.getBoundingClientRect();
    return r.top < vh * 0.92 && r.bottom > 0;
  }

  /**
   * スクロールで要素がビューに入ったときに .scroll-animate--inview を付与する。
   * prefers-reduced-motion のときは初期表示のまま（html.js-scroll-anim を付けない）。
   */
  function initScrollRevealAnimations() {
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!reduceMotion) {
      document.documentElement.classList.add("js-scroll-anim");
    }

    const opts = { root: null, rootMargin: "0px 0px -8% 0px", threshold: 0.08 };
    const vh = window.innerHeight || document.documentElement.clientHeight;

    const reveal = (el) => {
      el.classList.add("scroll-animate--inview");
    };

    const animatedEls = Array.from(document.querySelectorAll("[data-scroll-animate]"));
    const timelineEls = Array.from(document.querySelectorAll(".achv-timeline"));
    const animatedInView = animatedEls.map((el) => isElementInInitialViewport(el, vh));
    const timelineInView = timelineEls.map((el) => isElementInInitialViewport(el, vh));

    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        reveal(/** @type {Element} */ (entry.target));
        io.unobserve(entry.target);
      });
    }, opts);

    animatedEls.forEach((el, index) => {
      if (reduceMotion) {
        reveal(el);
        return;
      }
      if (animatedInView[index]) {
        reveal(el);
        return;
      }
      io.observe(el);
    });

    /** 実績タイムライン（凝った演出は CSS の .achv-timeline--inview に委譲） */
    const timelineIo = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const tl = /** @type {HTMLElement} */ (entry.target);
          tl.classList.add("achv-timeline--inview");
          timelineIo.unobserve(tl);
        });
      },
      { root: null, rootMargin: "0px 0px -10% 0px", threshold: 0.14 }
    );

    timelineEls.forEach((el, index) => {
      if (reduceMotion) {
        el.classList.add("achv-timeline--inview");
        return;
      }
      if (timelineInView[index]) {
        el.classList.add("achv-timeline--inview");
        return;
      }
      timelineIo.observe(el);
    });
  }

  /**
   * メンバー紹介: 枠線のストローク描画後にカードを順に表示（prefers-reduced-motion では即表示）。
   */
  function initMemberShowcaseScroll() {
    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const opts = { root: null, rootMargin: "0px 0px -8% 0px", threshold: 0.08 };
    const vh = window.innerHeight || document.documentElement.clientHeight;

    /** @param {Element} el */
    function reveal(el) {
      el.classList.add("member-showcase--inview");
    }

    const nodes = Array.from(document.querySelectorAll("[data-member-showcase]"));
    if (nodes.length === 0) return;

    const nodesInView = nodes.map((el) => isElementInInitialViewport(el, vh));

    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        reveal(/** @type {Element} */ (entry.target));
        io.unobserve(entry.target);
      });
    }, opts);

    nodes.forEach((el, index) => {
      if (reduceMotion) {
        reveal(el);
        return;
      }
      if (nodesInView[index]) {
        reveal(el);
        return;
      }
      io.observe(el);
    });
  }

  /** お知らせ一覧: 1ページあたり表示する最大件数 */
  const NEWS_ITEMS_PER_PAGE = 5;

  /** @type {unknown[]|null} */
  let dioramaNewsCache = null;

  /**
   * @param {string} s
   * @returns {string}
   */
  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  /**
   * @returns {"ja"|"zh"|"zh-TW"|"en"|"ko"}
   */
  function getDioramaNewsLocale() {
    if (window.I18n && typeof window.I18n.getLocale === "function") {
      const loc = window.I18n.getLocale();
      if (loc === "zh" || loc === "zh-TW" || loc === "en" || loc === "ko" || loc === "ja") {
        return loc;
      }
    }
    return "ja";
  }

  /**
   * dioramaNewsCache から #news-source を現在ロケールで再構築する。
   */
  function renderDioramaNewsSourceFromCache() {
    const source = document.getElementById("news-source");
    if (!source || !Array.isArray(dioramaNewsCache)) return;

    const loc = getDioramaNewsLocale();
    const items = [...dioramaNewsCache].sort((a, b) => {
      const orderA = typeof a === "object" && a !== null && "order" in a ? Number(a.order) : 999999;
      const orderB = typeof b === "object" && b !== null && "order" in b ? Number(b.order) : 999999;
      return (Number.isFinite(orderA) ? orderA : 999999) - (Number.isFinite(orderB) ? orderB : 999999);
    });

    const parts = [];
    for (const raw of items) {
      if (typeof raw !== "object" || raw === null) continue;
      const item = /** @type {Record<string, unknown>} */ (raw);
      const dateStr = typeof item.date === "string" ? item.date : "";
      const dt = typeof item.datetime === "string" ? item.datetime : "";
      const link = typeof item.link === "string" ? item.link.trim() : "";
      const textObj = item.text && typeof item.text === "object" ? /** @type {Record<string, string>} */ (item.text) : {};
      const line =
        textObj[loc] ||
        textObj.ja ||
        "";
      const timeEl = `<time datetime="${escapeHtml(dt || dateStr)}">${escapeHtml(dateStr)}</time>`;
      if (link) {
        parts.push(`<li>${timeEl}<a href="${escapeHtml(link)}">${escapeHtml(line)}</a></li>`);
      } else {
        parts.push(`<li>${timeEl}<span class="news-source__text">${escapeHtml(line)}</span></li>`);
      }
    }
    source.innerHTML = parts.join("");
  }

  /**
   * お知らせ DOM を再生成してからページャを初期化する。
   */
  function refreshDioramaNews() {
    renderDioramaNewsSourceFromCache();
    initNewsPagination();
  }

  /**
   * data/news.json を取得してキャッシュし、ビューを更新する。
   * @returns {Promise<void>}
   */
  function loadDioramaNews() {
    return fetch("data/news.json", { cache: "no-cache" })
      .then((res) => {
        if (!res.ok) throw new Error("news.json fetch failed");
        return res.json();
      })
      .then((data) => {
        if (!Array.isArray(data)) {
          dioramaNewsCache = [];
        } else {
          dioramaNewsCache = data;
        }
        refreshDioramaNews();
      })
      .catch(() => {
        dioramaNewsCache = [];
        refreshDioramaNews();
      });
  }

  function onDioramaLocaleChangeForNews() {
    if (!Array.isArray(dioramaNewsCache)) return;
    refreshDioramaNews();
  }

  /** @type {Array<Record<string, unknown>>|null} */
  let awardsCache = null;

  /** @type {number|null} */
  let openAwardDetailId = null;

  const AWARD_STATUSES = ["won", "finalist", "first_pass", "ongoing", "nominated"];

  /**
   * @returns {string}
   */
  function getActiveLocale() {
    if (window.I18n && typeof window.I18n.getLocale === "function") {
      return window.I18n.getLocale();
    }
    return "ja";
  }

  /**
   * @param {unknown} obj
   * @param {string} loc
   * @returns {string}
   */
  function pickLocalized(obj, loc) {
    if (!obj || typeof obj !== "object") return "";
    const rec = /** @type {Record<string, string>} */ (obj);
    return rec[loc] || rec.ja || "";
  }

  /**
   * @returns {Array<Record<string, unknown>>}
   */
  function getVisibleAwards() {
    const items = Array.isArray(awardsCache) ? awardsCache : [];
    return items
      .filter((item) => item.published && item.featured)
      .sort((a, b) => Number(a.order ?? 999999) - Number(b.order ?? 999999));
  }

  /**
   * @param {HTMLElement} root
   */
  function applyOrbThumbFrames(root) {
    root.querySelectorAll("[data-orb-thumb-viewport]").forEach((viewport) => {
      if (!(viewport instanceof HTMLElement)) return;
      const img = viewport.querySelector(".award-orb__thumb");
      if (!(img instanceof HTMLImageElement)) return;

      let frame = normalizeAwardThumbFrame(null);
      try {
        frame = normalizeAwardThumbFrame(JSON.parse(viewport.getAttribute("data-frame") || "{}"));
      } catch {
        /* use defaults */
      }

      const apply = () => {
        viewport.style.width = "100%";
        viewport.style.height = "100%";
        applyAwardThumbFrame(viewport, img, frame);
      };

      if (img.complete) apply();
      else img.addEventListener("load", apply, { once: true });
    });
  }

  /**
   * @param {Record<string, unknown>} item
   * @param {string} loc
   * @returns {string}
   */
  function buildAwardOrbHtml(item, loc) {
    const rawStatus = String(item.status || "nominated");
    const status = AWARD_STATUSES.includes(rawStatus) ? rawStatus : "nominated";
    const org = pickLocalized(item.organization, loc);
    const result = pickLocalized(item.result, loc);
    const year = String(item.year || "").trim();
    const id = Number(item.id);
    const thumbnail = typeof item.thumbnail === "string" ? item.thumbnail.trim() : "";
    const frame = normalizeAwardThumbFrame(item.thumbnailFrame);
    const ongoingClass =
      status === "ongoing" || status === "first_pass" ? " award-orb--ongoing" : "";

    let buttonInner;
    if (thumbnail) {
      const frameAttr = escapeHtml(JSON.stringify(frame));
      buttonInner = `<span class="award-orb__media">
        <span class="award-orb__thumb-viewport award-thumb-viewport" data-orb-thumb-viewport data-frame="${frameAttr}">
          <img class="award-orb__thumb" src="${escapeHtml(thumbnail)}" alt="${escapeHtml(result)}" loading="lazy" decoding="async" draggable="false">
        </span>
        <span class="award-orb__label">${escapeHtml(result)}</span>
      </span>`;
    } else {
      buttonInner = `<p class="award-orb__result">${escapeHtml(result)}</p>
        <p class="award-orb__org">${escapeHtml(org)}</p>`;
    }

    const thumbClass = thumbnail ? " award-orb__button--thumb" : "";

    return `<li class="award-orb${ongoingClass}" role="listitem" data-award-id="${id}" data-award-result="${escapeHtml(result)}" data-award-org="${escapeHtml(org)}" data-award-year="${escapeHtml(year)}">
      <button type="button" class="award-orb__button${thumbClass}" data-award-open="${id}" aria-haspopup="dialog">
        ${buttonInner}
      </button>
    </li>`;
  }

  /**
   * @param {Record<string, unknown>|null|undefined} raw
   */
  function normalizeAwardThumbFrame(raw) {
    const defaults = { width: 100, height: 0, cropLeft: 0, cropTop: 0, cropRight: 0, cropBottom: 0 };
    if (!raw || typeof raw !== "object") return defaults;
    const f = /** @type {Record<string, unknown>} */ (raw);
    const clamp = (n, min, max) => Math.min(max, Math.max(min, n));
    return {
      width: clamp(Number(f.width ?? 100), 20, 100),
      height: Math.max(0, Math.round(Number(f.height ?? 0))),
      cropLeft: clamp(Number(f.cropLeft ?? 0), 0, 95),
      cropTop: clamp(Number(f.cropTop ?? 0), 0, 95),
      cropRight: clamp(Number(f.cropRight ?? 0), 0, 95),
      cropBottom: clamp(Number(f.cropBottom ?? 0), 0, 95),
    };
  }

  /**
   * @param {HTMLElement} viewport
   * @param {HTMLImageElement} img
   * @param {ReturnType<typeof normalizeAwardThumbFrame>} frame
   */
  function applyAwardThumbFrame(viewport, img, frame) {
    viewport.style.width = `${frame.width}%`;
    if (frame.height > 0) {
      viewport.style.height = `${frame.height}px`;
      viewport.classList.add("award-thumb-viewport--fixed-height");
    } else {
      viewport.style.height = "";
      viewport.classList.remove("award-thumb-viewport--fixed-height");
    }
    const clip =
      frame.cropTop || frame.cropRight || frame.cropBottom || frame.cropLeft
        ? `inset(${frame.cropTop}% ${frame.cropRight}% ${frame.cropBottom}% ${frame.cropLeft}%)`
        : "";
    img.style.clipPath = clip;
    img.style.webkitClipPath = clip;
  }

  /**
   * @param {Record<string, unknown>} att
   * @returns {string}
   */
  function getAttachmentExtension(att) {
    const url = String(att.url || "");
    const mime = String(att.mime || "");
    const type = String(att.type || "");

    const pathPart = url.split(/[?#]/)[0] || "";
    const fromUrl = pathPart.match(/\.([a-zA-Z0-9]{1,8})$/);
    if (fromUrl) return fromUrl[1].toLowerCase();

    if (mime) {
      const sub = mime.split("/").pop() || "";
      if (sub && sub !== "octet-stream") {
        return sub.toLowerCase().replace(/^x-/, "");
      }
    }

    const typeMap = { pdf: "pdf", video: "mp4", image: "jpg" };
    if (type && type !== "file" && !type.includes("/")) {
      return typeMap[type] || type.toLowerCase();
    }

    return "";
  }

  /**
   * @param {Record<string, unknown>} att
   * @returns {string}
   */
  function buildAwardFileLinkHtml(att) {
    const url = String(att.url || "");
    const label = String(att.label || "ファイル");
    if (!url) return "";

    const ext = getAttachmentExtension(att);
    const isExternal = /^https?:\/\//i.test(url);
    const extHtml = ext
      ? `<span class="award-detail__file-ext">.${escapeHtml(ext)}</span>`
      : "";

    return `<li><a class="award-detail__file-link" href="${escapeHtml(url)}"${
      isExternal ? ' target="_blank" rel="noopener noreferrer"' : ""
    }><span class="award-detail__file-label">${escapeHtml(label)}</span>${extHtml}</a></li>`;
  }

  /**
   * @param {Record<string, unknown>} item
   * @param {string} loc
   */
  function openAwardDetail(item, loc) {
    const modal = document.getElementById("award-detail");
    const viewport = document.getElementById("award-detail-thumb-viewport");
    const thumb = document.getElementById("award-detail-thumb");
    const thumbPh = document.getElementById("award-detail-thumb-placeholder");
    const descEl = document.getElementById("award-detail-desc");
    const filesEl = document.getElementById("award-detail-files");
    const titleEl = document.getElementById("award-detail-title");
    if (!modal || !viewport || !thumb || !thumbPh || !descEl || !filesEl || !titleEl) return;

    const frame = normalizeAwardThumbFrame(item.thumbnailFrame);

    const result = pickLocalized(item.result, loc);
    const org = pickLocalized(item.organization, loc);
    const desc = pickLocalized(item.description, loc);
    const thumbnail = typeof item.thumbnail === "string" ? item.thumbnail.trim() : "";

    titleEl.textContent = `${result} — ${org}`;
    descEl.textContent = desc || "";
    openAwardDetailId = Number(item.id);

    if (thumbnail) {
      viewport.hidden = false;
      thumb.src = thumbnail;
      thumb.alt = result;
      thumb.hidden = false;
      thumbPh.hidden = true;
      const applyFrame = () => applyAwardThumbFrame(viewport, thumb, frame);
      if (thumb.complete) applyFrame();
      else thumb.addEventListener("load", applyFrame, { once: true });
    } else {
      viewport.hidden = true;
      thumb.removeAttribute("src");
      thumb.alt = "";
      thumb.hidden = true;
      thumbPh.hidden = false;
      viewport.style.width = "";
      viewport.style.height = "";
      viewport.classList.remove("award-thumb-viewport--fixed-height");
      thumb.style.clipPath = "";
    }

    const attachments = Array.isArray(item.attachments) ? item.attachments : [];
    if (attachments.length === 0) {
      const emptyText =
        window.I18n && typeof window.I18n.t === "function"
          ? window.I18n.t("awards.attachmentsEmpty")
          : "添付ファイルはありません";
      filesEl.innerHTML = `<li class="award-detail__files-empty">${escapeHtml(emptyText)}</li>`;
    } else {
      filesEl.innerHTML = attachments
        .map((att) => buildAwardFileLinkHtml(/** @type {Record<string, unknown>} */ (att)))
        .join("");
    }

    modal.hidden = false;
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("award-detail-open");
    const closeBtn = modal.querySelector(".award-detail__close");
    if (closeBtn instanceof HTMLElement) closeBtn.focus();
  }

  function closeAwardDetail() {
    const modal = document.getElementById("award-detail");
    if (!modal) return;
    openAwardDetailId = null;
    modal.hidden = true;
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("award-detail-open");
  }

  /**
   * ドラッグ用ポインタキャプチャでクリックがトラックに付く場合でも賞ボタンを特定する
   * @param {EventTarget|null} target
   * @param {number} clientX
   * @param {number} clientY
   * @returns {HTMLElement|null}
   */
  function findAwardOpenTrigger(target, clientX, clientY) {
    const el = target instanceof Element ? target : null;
    if (!el) return null;

    const direct = el.closest("[data-award-open]");
    if (direct instanceof HTMLElement) return direct;

    const track = el.closest(".award-orbs-track");
    if (!(track instanceof HTMLElement)) return null;

    const hit = document.elementFromPoint(clientX, clientY);
    if (!(hit instanceof Element)) return null;

    const viaPoint = hit.closest("[data-award-open]");
    return viaPoint instanceof HTMLElement ? viaPoint : null;
  }

  function initAwardDetailModal() {
    document.addEventListener("click", (ev) => {
      const t = /** @type {HTMLElement} */ (ev.target);
      const openEl = findAwardOpenTrigger(ev.target, ev.clientX, ev.clientY);
      const openId = openEl?.getAttribute("data-award-open");
      if (openId) {
        const track = openEl.closest(".award-orbs-track");
        if (
          track instanceof HTMLElement &&
          (track.classList.contains("was-dragging") || track.classList.contains("is-dragging"))
        ) {
          return;
        }
        const id = Number(openId);
        const loc = getActiveLocale();
        const item = (Array.isArray(awardsCache) ? awardsCache : []).find(
          (row) => Number(row.id) === id
        );
        if (item) {
          ev.preventDefault();
          openAwardDetail(item, loc);
        }
        return;
      }
      if (t.closest?.("[data-award-detail-close]")) {
        closeAwardDetail();
      }
    });

    document.addEventListener("keydown", (ev) => {
      if (ev.key === "Escape") {
        const modal = document.getElementById("award-detail");
        if (modal && !modal.hidden) closeAwardDetail();
      }
    });
  }

  function renderAwards() {
    const section = document.getElementById("awards");
    const orbs = document.getElementById("awards-orbs");
    if (!section || !orbs) return;

    const loc = getActiveLocale();
    const visible = getVisibleAwards();

    if (visible.length === 0) {
      section.hidden = true;
      orbs.innerHTML = "";
      return;
    }

    section.hidden = false;
    orbs.innerHTML = visible.map((item) => buildAwardOrbHtml(item, loc)).join("");
    applyOrbThumbFrames(orbs);
    if (window.AwardsOrbCarousel) {
      window.AwardsOrbCarousel.init(orbs, { initialIndex: Math.min(1, visible.length - 1) });
    }
  }

  /**
   * @returns {Promise<void>}
   */
  function loadAwards() {
    return fetch("data/awards.json", { cache: "no-cache" })
      .then((res) => {
        if (!res.ok) throw new Error("awards.json fetch failed");
        return res.json();
      })
      .then((data) => {
        awardsCache = Array.isArray(data) ? data : [];
        renderAwards();
      })
      .catch(() => {
        awardsCache = [];
        renderAwards();
      });
  }

  function onDioramaLocaleChangeForAwards() {
    if (!Array.isArray(awardsCache)) return;
    const prevId = openAwardDetailId;
    renderAwards();
    if (prevId != null) {
      const item = awardsCache.find((row) => Number(row.id) === prevId);
      if (item) openAwardDetail(item, getActiveLocale());
    }
  }

  /**
   * #news-source の li を「1ページ最大 NEWS_ITEMS_PER_PAGE 件」で分割し、ビューポートとページャを構築する。
   */
  function initNewsPagination() {
    const source = document.getElementById("news-source");
    let viewport = document.getElementById("news-viewport");
    let pager = document.getElementById("news-pager");
    let btnPrev = document.getElementById("news-prev");
    let btnNext = document.getElementById("news-next");
    if (!source || !viewport || !pager || !btnPrev || !btnNext) return;

    viewport.innerHTML = "";
    pager.innerHTML = "";
    pager.removeAttribute("hidden");
    btnPrev.removeAttribute("hidden");
    btnNext.removeAttribute("hidden");
    const prevClone = /** @type {HTMLButtonElement} */ (btnPrev.cloneNode(true));
    const nextClone = /** @type {HTMLButtonElement} */ (btnNext.cloneNode(true));
    btnPrev.replaceWith(prevClone);
    btnNext.replaceWith(nextClone);
    btnPrev = document.getElementById("news-prev");
    btnNext = document.getElementById("news-next");
    if (!btnPrev || !btnNext) return;

    const items = Array.from(source.querySelectorAll("li"));
    if (items.length === 0) {
      const empty = document.createElement("p");
      empty.className = "news-empty muted";
      empty.textContent =
        window.I18n && typeof window.I18n.t === "function"
          ? window.I18n.t("news.empty")
          : "お知らせはまだありません。";
      viewport.appendChild(empty);
      btnPrev.disabled = true;
      btnNext.disabled = true;
      return;
    }

    const perPage = NEWS_ITEMS_PER_PAGE;
    const totalPages = Math.ceil(items.length / perPage);

    /** @type {HTMLElement[]} */
    const pages = [];
    for (let p = 0; p < totalPages; p++) {
      const chunk = items.slice(p * perPage, (p + 1) * perPage);
      if (chunk.length === 0) break;
      const pageEl = document.createElement("div");
      pageEl.className = "news-page";
      pageEl.hidden = p !== 0;
      pageEl.setAttribute("role", "group");
      pageEl.setAttribute(
        "aria-label",
        window.I18n && typeof window.I18n.t === "function"
          ? window.I18n.t("news.pageGroupAria", { current: p + 1, total: totalPages })
          : `お知らせ ${p + 1} / ${totalPages} ページ`
      );
      const ul = document.createElement("ul");
      ul.className = "news-list";
      chunk.forEach((li) => {
        ul.appendChild(li.cloneNode(true));
      });
      pageEl.appendChild(ul);
      viewport.appendChild(pageEl);
      pages.push(pageEl);
    }

    let current = 0;

    function syncPagerState() {
      pager.querySelectorAll("button[data-news-page]").forEach((btn, idx) => {
        const on = idx === current;
        btn.classList.toggle("is-active", on);
        if (on) btn.setAttribute("aria-current", "page");
        else btn.removeAttribute("aria-current");
      });
      btnPrev.disabled = current <= 0;
      btnNext.disabled = current >= pages.length - 1;
    }

    function showPage(index) {
      current = Math.max(0, Math.min(pages.length - 1, index));
      pages.forEach((el, idx) => {
        el.hidden = idx !== current;
      });
      syncPagerState();
    }

    for (let i = 0; i < pages.length; i++) {
      const b = document.createElement("button");
      b.type = "button";
      b.dataset.newsPage = String(i);
      b.textContent = String(i + 1);
      b.setAttribute(
        "aria-label",
        window.I18n && typeof window.I18n.t === "function"
          ? window.I18n.t("news.pagerPageAria", { n: i + 1 })
          : `ページ ${i + 1}`
      );
      b.addEventListener("click", () => showPage(i));
      pager.appendChild(b);
    }

    btnPrev.addEventListener("click", () => showPage(current - 1));
    btnNext.addEventListener("click", () => showPage(current + 1));
    showPage(0);

    if (pages.length <= 1) {
      pager.setAttribute("hidden", "");
      btnPrev.setAttribute("hidden", "");
      btnNext.setAttribute("hidden", "");
    }
  }

  /**
   * ヘッダー言語メニュー: ボタンで一覧を開閉し、選択を localStorage に保存して applyLocale + お知らせ再構築。
   */
  function initLangSwitcher() {
    const root = document.querySelector("[data-lang-switch]");
    const toggle = document.getElementById("lang-switch-toggle");
    const list = document.getElementById("lang-switch-list");
    if (!root || !toggle || !list) return;

    function syncLangCodeDisplay() {
      if (!window.I18n || typeof window.I18n.getLocale !== "function") return;
      const loc = window.I18n.getLocale();
      const codeEl = root.querySelector("[data-lang-current]");
      if (codeEl) {
        codeEl.textContent =
          loc === "ja"
            ? "JA"
            : loc === "zh"
              ? "ZH"
              : loc === "zh-TW"
                ? "TW"
                : loc === "ko"
                  ? "KO"
                  : "EN";
      }
    }

    function setOpen(open) {
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      list.hidden = !open;
      root.classList.toggle("lang-switch--open", open);
    }

    function closeMenu() {
      setOpen(false);
    }

    toggle.addEventListener("click", (event) => {
      event.stopPropagation();
      setOpen(list.hidden);
    });

    list.querySelectorAll("[data-set-locale]").forEach((btn) => {
      btn.addEventListener("click", (event) => {
        event.stopPropagation();
        const loc = btn.getAttribute("data-set-locale");
        if (!loc || !window.I18n || typeof window.I18n.applyLocale !== "function") return;
        try {
          window.localStorage.setItem("site-locale", loc);
        } catch (_) {
          /* ignore */
        }
        window.I18n.applyLocale(/** @type {"ja"|"zh"|"zh-TW"|"en"|"ko"} */ (loc));
        closeMenu();
        closeMobileNavIfOpen();
      });
    });

    document.addEventListener("click", closeMenu);
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") closeMenu();
    });

    document.addEventListener("localechange", () => {
      syncLangCodeDisplay();
    });

    syncLangCodeDisplay();
  }

  /**
   * One virtual gallery: main media + overlaid thumbnail rail.
   * @param {Element} root
   */
  function initOneVirtualShowcase(root) {
    const mainImg = root.querySelector("[data-showcase-main-img]");
    const videoBooth = root.querySelector("[data-showcase-video-booth]");
    const mainVideo = root.querySelector("[data-showcase-main-video]");
    const playBtn = root.querySelector("[data-showcase-play-btn]");
    const rail = root.querySelector(".virtual-showcase__rail");
    const buttons = Array.from(root.querySelectorAll("[data-showcase-index]"));

    /**
     * @returns {boolean}
     */
    function prefersReducedMotion() {
      return (
        window.matchMedia &&
        window.matchMedia("(prefers-reduced-motion: reduce)").matches
      );
    }

    /**
     * Scrolls only the thumbnail rail horizontally (avoids window scroll from scrollIntoView when off-screen).
     * @param {HTMLElement} activeButton
     */
    function scrollActivePickIntoView(activeButton) {
      if (!(rail instanceof HTMLElement)) return;
      const behavior = prefersReducedMotion() ? "auto" : "smooth";
      const railRect = rail.getBoundingClientRect();
      const btnRect = activeButton.getBoundingClientRect();
      const pad = 8;
      let delta = 0;
      if (btnRect.left < railRect.left + pad) {
        delta = btnRect.left - railRect.left - pad;
      } else if (btnRect.right > railRect.right - pad) {
        delta = btnRect.right - railRect.right + pad;
      }
      if (delta === 0) return;
      const maxLeft = Math.max(0, rail.scrollWidth - rail.clientWidth);
      const nextLeft = Math.max(0, Math.min(maxLeft, rail.scrollLeft + delta));
      rail.scrollTo({ left: nextLeft, behavior });
    }

    if (rail instanceof HTMLElement) {
      rail.addEventListener(
        "wheel",
        (event) => {
          if (rail.scrollWidth <= rail.clientWidth) return;
          const dx = event.deltaX;
          const dy = event.deltaY;
          if (Math.abs(dy) <= Math.abs(dx)) return;
          event.preventDefault();
          rail.scrollLeft += dy;
        },
        { passive: false }
      );
    }

    /**
     * @param {Element} activeButton
     */
    function applyAccessibleNames(activeButton) {
      const path = activeButton.getAttribute("data-i18n");
      const text =
        window.I18n && path && typeof window.I18n.t === "function" ? window.I18n.t(path) : "";
      const type =
        activeButton.getAttribute("data-showcase-type") === "video" ? "video" : "image";
      if (type === "video" && mainVideo instanceof HTMLVideoElement) {
        mainVideo.setAttribute("title", text || "");
        return;
      }
      if (mainImg instanceof HTMLImageElement) {
        mainImg.setAttribute("alt", text || "");
      }
    }

    /**
     * @param {number} index
     */
    function activate(index) {
      const btn = buttons.find(
        (b) => parseInt(b.getAttribute("data-showcase-index") || "", 10) === index
      );
      if (!btn) return;

      const type =
        btn.getAttribute("data-showcase-type") === "video" ? "video" : "image";
      const src = btn.getAttribute("data-showcase-src") || "";

      buttons.forEach((b) => {
        const on = parseInt(b.getAttribute("data-showcase-index") || "", 10) === index;
        b.classList.toggle("is-active", on);
        b.setAttribute("aria-pressed", on ? "true" : "false");
      });

      if (type === "video" && mainVideo instanceof HTMLVideoElement && videoBooth instanceof HTMLElement) {
        if (mainImg instanceof HTMLElement) mainImg.hidden = true;
        videoBooth.hidden = false;
        if (src && mainVideo.getAttribute("src") !== src) {
          mainVideo.setAttribute("src", src);
          mainVideo.load();
        }
        mainVideo.pause();
        mainVideo.controls = false;
        try {
          mainVideo.currentTime = 0;
        } catch (_) {
          /* ignore */
        }
        videoBooth.classList.remove("is-playing");
      } else {
        if (mainVideo instanceof HTMLVideoElement) {
          mainVideo.pause();
          mainVideo.controls = false;
        }
        if (videoBooth instanceof HTMLElement) {
          videoBooth.hidden = true;
          videoBooth.classList.remove("is-playing");
        }
        if (mainImg instanceof HTMLImageElement) {
          mainImg.hidden = false;
          if (src) mainImg.src = src;
        }
      }

      applyAccessibleNames(btn);

      const activeNow = buttons.find((x) => x.classList.contains("is-active"));
      if (activeNow instanceof HTMLElement) {
        requestAnimationFrame(() => scrollActivePickIntoView(activeNow));
      }
    }

    if (playBtn instanceof HTMLButtonElement && mainVideo instanceof HTMLVideoElement && videoBooth instanceof HTMLElement) {
      playBtn.addEventListener("click", (event) => {
        event.stopPropagation();
        mainVideo.controls = true;
        videoBooth.classList.add("is-playing");
        mainVideo.play().catch(() => {});
      });
    }

    buttons.forEach((b) => {
      b.addEventListener("click", () => {
        const idx = parseInt(b.getAttribute("data-showcase-index") || "", 10);
        if (Number.isNaN(idx)) return;
        activate(idx);
      });
    });

    document.addEventListener("localechange", () => {
      const activeBtn = buttons.find((x) => x.classList.contains("is-active"));
      if (!activeBtn) return;
      applyAccessibleNames(activeBtn);
    });

    const initialRaw = root.getAttribute("data-showcase-initial");
    const initial = initialRaw == null ? 0 : parseInt(initialRaw, 10);
    requestAnimationFrame(() => activate(Number.isNaN(initial) ? 0 : initial));
  }

  /** Virtual section: all [data-virtual-showcase] blocks (e.g. stacked rows). */
  function initVirtualShowcase() {
    document.querySelectorAll("[data-virtual-showcase]").forEach((root) => {
      initOneVirtualShowcase(root);
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    document.addEventListener("localechange", onDioramaLocaleChangeForNews);
    document.addEventListener("localechange", onDioramaLocaleChangeForAwards);
    initLangSwitcher();
    if (window.I18n && typeof window.I18n.applyLocale === "function") {
      window.I18n.applyLocale(window.I18n.resolveLocale());
    }
    initHeaderShadow();
    initNavMenus();
    initSmoothScroll();
    initCurrentYear();
    initScrollRevealAnimations();
    initMemberShowcaseScroll();
    initAwardDetailModal();
    void loadDioramaNews();
    void loadAwards();
    initVirtualShowcase();
    hidePageLoader();
  });
})();
