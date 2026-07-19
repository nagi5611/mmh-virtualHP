// ar/ar.js — AR 体験ページ（モデルパスは後から指定）
(function () {
    "use strict";

    /** @type {{ glb: string, usdz: string }} */
    const AR_MODEL = {
        glb: "新校舎_外装_造形物コンテスト_最終.glb",
        usdz: "新校舎_外装_造形物コンテスト_最終.usdz",
    };

    const TRANSLATIONS = {
        ja: {
            arPageTitle: "AR体験",
            arBackHome: "トップへ戻る",
            arBtnLaunch: "ARで体験する",
            arHowtoTitle: "操作方法",
            arHowtoStep1: "「ARで体験する」をタップし、カメラの使用を許可します。",
            arHowtoStep2: "スマホをゆっくり動かし、水平な面を認識させます。",
            arHowtoStep3: "画面をタップして3Dモデルを配置します。",
            arHowtoStep4: "AR中は2本指のピンチで拡大・縮小できます。",
            arHowtoStep5: "2本指でスライドしてオブジェクトを移動できます（Androidのブラウザ内ARではモデル上を1本指でドラッグ）。",
            arHowtoStep6: "右端のスライダーでモデルの大きさを調整できます（最小0.1%）。",
            arScaleLabel: "サイズ",
            arScaleAria: "モデルの大きさ",
            arMobileNote: "明るい場所で、机や床などの水平な面の上でお試しください。",
            arUnsupported: "この端末ではARをご利用いただけません。下の画面で3Dモデルをご覧いただけます。",
            arLoadingHint: "モデルを読み込んでいます…",
            arPreparing: "3Dモデルは現在準備中です。公開までしばらくお待ちください。",
            arViewerAlt: "新校舎外装・造形物コンテスト最終作品",
        },
        en: {
            arPageTitle: "AR Experience",
            arBackHome: "Back to home",
            arBtnLaunch: "Launch AR",
            arHowtoTitle: "How to use",
            arHowtoStep1: "Tap “Launch AR” and allow camera access.",
            arHowtoStep2: "Move your phone slowly to detect a flat surface.",
            arHowtoStep3: "Tap the screen to place the 3D model.",
            arHowtoStep4: "In AR, pinch with two fingers to resize the model.",
            arHowtoStep5: "Slide with two fingers to move the object (on Android in-browser AR, drag on the model with one finger).",
            arHowtoStep6: "Use the slider on the right edge to resize the model (minimum 0.1%).",
            arScaleLabel: "Size",
            arScaleAria: "Model size",
            arMobileNote: "Use in a bright area on a flat surface.",
            arUnsupported: "AR is not available on this device. You can still view the 3D model below.",
            arLoadingHint: "Loading model…",
            arPreparing: "The 3D model is being prepared. Please check back soon.",
            arViewerAlt: "New building facade sculpture contest final entry",
        },
        zh: {
            arPageTitle: "AR体验",
            arBackHome: "返回首页",
            arBtnLaunch: "启动AR体验",
            arHowtoTitle: "操作说明",
            arHowtoStep1: "点击“启动AR体验”，并允许使用摄像头。",
            arHowtoStep2: "缓慢移动手机，识别水平面。",
            arHowtoStep3: "点击屏幕放置3D模型。",
            arHowtoStep4: "AR中可用双指捏合放大或缩小。",
            arHowtoStep5: "双指滑动可移动对象（Android浏览器内AR请用单指在模型上拖动）。",
            arHowtoStep6: "使用右侧滑块调整模型大小（最小0.1%）。",
            arScaleLabel: "大小",
            arScaleAria: "模型大小",
            arMobileNote: "请在明亮环境下，于水平面上体验。",
            arUnsupported: "此设备无法使用AR。您可在下方查看3D模型。",
            arLoadingHint: "正在加载模型…",
            arPreparing: "3D模型正在准备中，请稍后再试。",
            arViewerAlt: "新校舍外装造形物大赛最终作品",
        },
    };

    /**
     * Resolves locale from localStorage (shared with index.html).
     * @returns {'ja'|'en'|'zh'}
     */
    function resolveLocale() {
        const saved = localStorage.getItem("language");
        if (saved === "en" || saved === "zh") return saved;
        return "ja";
    }

    /**
     * Applies translated strings to the AR page.
     * @param {'ja'|'en'|'zh'} locale
     */
    function applyLocale(locale) {
        const t = TRANSLATIONS[locale] || TRANSLATIONS.ja;
        document.documentElement.lang = locale === "zh" ? "zh-CN" : locale;

        document.querySelectorAll("[data-i18n]").forEach((el) => {
            const key = el.getAttribute("data-i18n");
            if (!key || !t[key]) return;
            const attr = el.getAttribute("data-i18n-attr");
            if (attr) {
                el.setAttribute(attr, t[key]);
            } else {
                el.textContent = t[key];
            }
        });

        if (t.arPageTitle) {
            document.title = `${t.arPageTitle} — ばーちゃるず`;
        }
    }

    /**
     * Syncs dark/light theme from index.html settings.
     */
    function applyTheme() {
        const theme = localStorage.getItem("theme");
        if (theme === "dark" || theme === "light") {
            document.documentElement.setAttribute("data-theme", theme);
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
     * Initializes the model size slider on the viewer edge.
     * @param {HTMLElement} viewer
     */
    function initScaleSlider(viewer) {
        const stage = viewer?.closest(".ar-viewer-stage");
        if (stage && globalThis.ArScaleControl) {
            globalThis.ArScaleControl.init(viewer, stage);
        }
    }

    /**
     * Shows model-viewer when paths are configured; otherwise preparing notice.
     */
    function initModelViewer() {
        const preparing = document.getElementById("ar-preparing");
        const wrap = document.getElementById("ar-viewer-wrap");
        const viewer = document.getElementById("diorama-viewer");

        if (!AR_MODEL.glb || !wrap || !viewer) {
            if (preparing) preparing.hidden = false;
            return;
        }

        viewer.setAttribute("src", encodeURI(AR_MODEL.glb));
        if (AR_MODEL.usdz) {
            viewer.setAttribute("ios-src", encodeURI(AR_MODEL.usdz));
        }
        configureViewerGestures(viewer);
        initScaleSlider(viewer);
        wrap.hidden = false;

        viewer.addEventListener("load", () => {
            const notice = document.getElementById("ar-unsupported");
            if (notice && viewer.canActivateAR === false) {
                notice.hidden = false;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        applyTheme();
        applyLocale(resolveLocale());
        initModelViewer();
    });
})();
