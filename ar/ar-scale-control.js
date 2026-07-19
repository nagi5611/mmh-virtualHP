// ar/ar-scale-control.js — モデルスケール用スライダー（0.1%〜100%）
(function (global) {
    "use strict";

    /** 0.1% */
    const SCALE_MIN = 0.001;
    /** 100% */
    const SCALE_MAX = 1;
    const SLIDER_STEPS = 1000;

    /**
     * Maps slider position to a logarithmic scale factor.
     * @param {number} sliderValue
     * @returns {number}
     */
    function sliderToScale(sliderValue) {
        const t = Number(sliderValue) / SLIDER_STEPS;
        const logMin = Math.log10(SCALE_MIN);
        const logMax = Math.log10(SCALE_MAX);
        return Math.pow(10, logMin + t * (logMax - logMin));
    }

    /**
     * Maps scale factor to slider position.
     * @param {number} scale
     * @returns {number}
     */
    function scaleToSlider(scale) {
        const clamped = Math.max(SCALE_MIN, Math.min(SCALE_MAX, scale));
        const logMin = Math.log10(SCALE_MIN);
        const logMax = Math.log10(SCALE_MAX);
        const t = (Math.log10(clamped) - logMin) / (logMax - logMin);
        return Math.round(t * SLIDER_STEPS);
    }

    /**
     * Formats scale as a percentage string.
     * @param {number} scale
     * @returns {string}
     */
    function formatPercent(scale) {
        const pct = scale * 100;
        if (pct < 1) {
            return `${pct.toFixed(1)}%`;
        }
        return `${Math.round(pct)}%`;
    }

    /**
     * Applies uniform scale to model-viewer with clamping.
     * @param {HTMLElement} viewer
     * @param {number} scale
     * @returns {number}
     */
    function applyScale(viewer, scale) {
        const s = Math.max(SCALE_MIN, Math.min(SCALE_MAX, scale));
        viewer.scale = `${s} ${s} ${s}`;
        return s;
    }

    /**
     * Wires the edge slider to a model-viewer instance.
     * @param {HTMLElement} viewer
     * @param {HTMLElement} root
     */
    function init(viewer, root) {
        const slider = root.querySelector(".ar-scale-control__slider");
        const output = root.querySelector(".ar-scale-control__value");
        if (!slider || !viewer) {
            return;
        }

        slider.min = "0";
        slider.max = String(SLIDER_STEPS);
        slider.value = String(SLIDER_STEPS);
        slider.step = "1";

        const updateDisplay = (scale) => {
            const label = formatPercent(scale);
            if (output) {
                output.textContent = label;
            }
            slider.setAttribute("aria-valuetext", label);
        };

        const onSliderInput = () => {
            const scale = applyScale(viewer, sliderToScale(slider.value));
            updateDisplay(scale);
        };

        slider.addEventListener("input", onSliderInput);

        viewer.addEventListener("load", () => {
            const scale = applyScale(viewer, SCALE_MAX);
            slider.value = String(SLIDER_STEPS);
            updateDisplay(scale);
        });
    }

    global.ArScaleControl = {
        init,
        applyScale,
        formatPercent,
        SCALE_MIN,
        SCALE_MAX,
    };
})(window);
