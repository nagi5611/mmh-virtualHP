// airport/ar/ar.js — AR ページ: i18n と非対応端末の案内
(function () {
  "use strict";

  /**
   * Applies site locale strings on the AR page.
   */
  function applyArLocale() {
    if (window.I18n && typeof window.I18n.applyLocale === "function") {
      window.I18n.applyLocale(window.I18n.resolveLocale());
      const locale = window.I18n.getLocale();
      const pageTitle = window.I18n.getString(locale, "arDiorama.pageTitle");
      const siteTitle = window.I18n.getString(locale, "meta.title");
      if (pageTitle && siteTitle) {
        document.title = `${pageTitle} — ${siteTitle}`;
      }
    }
  }

  /**
   * Enables pinch-zoom and two-finger pan on the model-viewer.
   * @param {HTMLElement} viewer
   */
  function configureViewerGestures(viewer) {
    viewer.setAttribute("ar-scale", "auto");
    viewer.setAttribute("touch-action", "none");
    viewer.removeAttribute("disable-pan");
    viewer.removeAttribute("disable-zoom");
  }

  /**
   * Shows a notice when model-viewer cannot activate AR (e.g. desktop).
   * @param {HTMLElement} viewer
   */
  function initArSupportNotice(viewer) {
    const notice = document.getElementById("ar-unsupported");
    if (!notice) return;

    const update = () => {
      if (viewer.canActivateAR === false) {
        notice.hidden = false;
      }
    };

    viewer.addEventListener("load", update);
    viewer.addEventListener("ar-status", update);
    window.setTimeout(update, 1200);
  }

  document.addEventListener("DOMContentLoaded", () => {
    applyArLocale();
    document.addEventListener("localechange", applyArLocale);

    const viewer = document.getElementById("diorama-viewer");
    if (viewer) {
      configureViewerGestures(viewer);
      const stage = viewer.closest(".ar-viewer-stage");
      if (stage && window.ArScaleControl) {
        window.ArScaleControl.init(viewer, stage);
      }
      initArSupportNotice(viewer);
    }
  });
})();
