// public_html/airport/assets/awards-carousel.js — 受賞歴円形カルーセル（横スクロール・中央拡大）

(function () {
    "use strict";

    const MIN_SCALE = 0.82;
    const MAX_SCALE = 1;
    const INFLUENCE_PX = 110;
    const SCALE_EPSILON = 0.02;
    const HINT_STORAGE_KEY = "mmh-awards-carousel-hint";
    const HINT_VISIBLE_MS = 4500;
    const HINT_FADE_MS = 400;
    const CAPTION_FADE_MS = 500;
    const DRAG_CLICK_THRESHOLD_PX = 8;
    const SNAP_SCROLL_THRESHOLD_PX = 3;
    const SNAP_PULL_RATIO = 0.25;
    const SCROLL_END_DEBOUNCE_MS = 140;
    const REDUCE_MOTION = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    /** @type {WeakMap<HTMLElement, {
     *   orbs: Array<{ el: HTMLElement, button: HTMLElement|null, mid: number, scale: number }>,
     *   centerId: string|null,
     *   activeIndex: number,
     *   raf: number|null,
     *   scrollEndTimer: number|null,
     *   resizeObserver: ResizeObserver|null,
     *   dotCount: number,
     *   navEl: HTMLElement|null,
     *   dotsEl: HTMLElement|null,
     *   counterEl: HTMLElement|null,
     *   captionEl: HTMLElement|null,
     *   captionBody: HTMLElement|null,
     *   captionContentKey: string|null,
     *   captionFadeTimer: number|null,
     *   captionResult: HTMLElement|null,
     *   captionOrg: HTMLElement|null,
     *   captionYear: HTMLElement|null,
     *   captionSepResult: HTMLElement|null,
     *   captionSepYear: HTMLElement|null,
     *   captionMore: HTMLButtonElement|null,
     *   hintEl: HTMLElement|null,
     *   hintShown: boolean,
     *   arrowsEl: HTMLElement|null,
     *   prevBtn: HTMLButtonElement|null,
     *   nextBtn: HTMLButtonElement|null,
     *   carouselEl: HTMLElement|null,
     *   centerX: number,
     *   trackLeft: number,
     * }>} */
    const trackState = new WeakMap();

    /**
     * @param {number} dist
     * @returns {number}
     */
    function scaleFromDistance(dist) {
        const t = Math.min(1, dist / INFLUENCE_PX);
        const eased = t * t * (3 - 2 * t);
        return MAX_SCALE - eased * (MAX_SCALE - MIN_SCALE);
    }

    /**
     * @param {HTMLElement} track
     */
    function measureTrack(track) {
        const state = trackState.get(track);
        if (!state) return;

        const carousel = state.carouselEl || track.closest(".award-orbs-carousel");
        const box = (carousel || track).getBoundingClientRect();
        const trackRect = track.getBoundingClientRect();
        state.centerX = box.left + box.width / 2;
        state.trackLeft = trackRect.left;
    }

    /**
     * @param {HTMLElement} track
     * @returns {number}
     */
    function findNearestOrbIndex(track) {
        const state = trackState.get(track);
        if (!state || state.orbs.length === 0) return 0;

        measureTrack(track);

        let activeIndex = 0;
        let minDist = Infinity;

        for (let i = 0; i < state.orbs.length; i++) {
            const item = state.orbs[i];
            const orbCenter = state.trackLeft + item.mid - track.scrollLeft;
            const dist = Math.abs(orbCenter - state.centerX);
            if (dist < minDist) {
                minDist = dist;
                activeIndex = i;
            }
        }

        return activeIndex;
    }

    /**
     * @param {HTMLElement} track
     * @param {ScrollBehavior} [behavior]
     */
    function snapToNearestOrb(track, behavior) {
        const state = trackState.get(track);
        if (!state || state.orbs.length === 0) return;

        const index = findNearestOrbIndex(track);
        const item = state.orbs[index];
        if (!item) return;

        const targetScroll = Math.max(0, item.mid - track.clientWidth / 2);
        const currentScroll = track.scrollLeft;
        const delta = targetScroll - currentScroll;

        if (Math.abs(delta) <= SNAP_SCROLL_THRESHOLD_PX) {
            updateCenterOrb(track);
            return;
        }

        const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
        const adjustedScroll = Math.max(
            0,
            Math.min(maxScroll, currentScroll + delta * SNAP_PULL_RATIO)
        );

        const snapBehavior = behavior ?? (REDUCE_MOTION ? "auto" : "smooth");
        track.scrollTo({
            left: adjustedScroll,
            behavior: snapBehavior,
        });
        scheduleUpdate(track, { skipSnap: true });
    }

    /**
     * @param {HTMLElement} track
     * @param {number} index
     * @param {ScrollBehavior} [behavior]
     */
    function scrollToOrbIndex(track, index, behavior) {
        const state = trackState.get(track);
        const item = state?.orbs[index];
        if (!item) return;
        const targetScroll = item.mid - track.clientWidth / 2;
        track.scrollTo({
            left: Math.max(0, targetScroll),
            behavior: behavior ?? "smooth",
        });
        scheduleUpdate(track);
    }

    /**
     * @param {HTMLElement} track
     * @param {number} delta
     */
    function scrollByIndex(track, delta) {
        const state = trackState.get(track);
        if (!state || state.orbs.length === 0) return;
        const next = Math.max(0, Math.min(state.orbs.length - 1, state.activeIndex + delta));
        scrollToOrbIndex(track, next);
    }

    /**
     * @param {HTMLElement} track
     */
    function bindChrome(track) {
        const state = trackState.get(track);
        if (!state) return;

        const carousel = track.closest(".award-orbs-carousel");
        if (!(carousel instanceof HTMLElement)) return;

        const section = track.closest("#awards");

        state.carouselEl = carousel;
        state.navEl = carousel.querySelector("[data-award-nav]");
        state.dotsEl = carousel.querySelector(".award-orbs-dots");
        state.counterEl = carousel.querySelector("[data-award-counter]");
        state.captionEl = carousel.querySelector("[data-award-caption]");
        state.captionBody = carousel.querySelector("[data-award-caption-body]");
        state.captionResult = carousel.querySelector("[data-award-caption-result]");
        state.captionOrg = carousel.querySelector("[data-award-caption-org]");
        state.captionYear = carousel.querySelector("[data-award-caption-year]");
        state.captionSepResult = carousel.querySelector("[data-award-caption-sep-result]");
        state.captionSepYear = carousel.querySelector("[data-award-caption-sep-year]");
        state.captionMore = carousel.querySelector("[data-award-caption-more]");
        state.hintEl = carousel.querySelector("[data-award-hint]");
        state.arrowsEl = section instanceof HTMLElement ? section.querySelector("[data-award-arrows]") : null;
        state.prevBtn = section instanceof HTMLElement
            ? section.querySelector("[data-award-prev]")
            : carousel.querySelector("[data-award-prev]");
        state.nextBtn = section instanceof HTMLElement
            ? section.querySelector("[data-award-next]")
            : carousel.querySelector("[data-award-next]");

        if (state.prevBtn && !state.prevBtn.dataset.bound) {
            state.prevBtn.dataset.bound = "1";
            state.prevBtn.addEventListener("click", () => {
                dismissHint(track);
                scrollByIndex(track, -1);
            });
        }
        if (state.nextBtn && !state.nextBtn.dataset.bound) {
            state.nextBtn.dataset.bound = "1";
            state.nextBtn.addEventListener("click", () => {
                dismissHint(track);
                scrollByIndex(track, 1);
            });
        }
    }

    /**
     * @param {HTMLElement|null|undefined} el
     * @param {string} text
     */
    function setCaptionPart(el, text) {
        if (!el) return;
        const value = text.trim();
        el.textContent = value;
        el.hidden = !value;
    }

    /**
     * @param {{
     *   captionResult: HTMLElement|null,
     *   captionOrg: HTMLElement|null,
     *   captionYear: HTMLElement|null,
     *   captionSepResult: HTMLElement|null,
     *   captionSepYear: HTMLElement|null,
     *   captionMore: HTMLButtonElement|null,
     * }} state
     * @param {string} result
     * @param {string} org
     * @param {string} year
     * @param {string} id
     */
    function applyCaptionContent(state, result, org, year, id) {
        setCaptionPart(state.captionResult, result);
        setCaptionPart(state.captionOrg, org);
        setCaptionPart(state.captionYear, year);

        if (state.captionSepResult) {
            state.captionSepResult.hidden = !(result.trim() && org.trim());
        }
        if (state.captionSepYear) {
            const hasYear = !!year.trim();
            const hasLeft = !!(org.trim() || result.trim());
            state.captionSepYear.hidden = !(hasYear && hasLeft);
        }

        if (state.captionMore) {
            state.captionMore.dataset.awardOpen = id;
        }
    }

    /**
     * @param {HTMLElement} track
     */
    function clearCaptionFade(state) {
        if (!state) return;
        if (state.captionFadeTimer != null) {
            window.clearTimeout(state.captionFadeTimer);
            state.captionFadeTimer = null;
        }
        if (state.captionBody) {
            state.captionBody.classList.remove("is-faded");
        }
    }

    /**
     * @param {HTMLElement} track
     * @param {number} activeIndex
     */
    function updateCaption(track, activeIndex) {
        const state = trackState.get(track);
        if (!state?.captionEl) return;

        const orb = state.orbs[activeIndex]?.el;
        if (!orb) {
            clearCaptionFade(state);
            state.captionContentKey = null;
            state.captionEl.hidden = true;
            return;
        }

        const result = orb.getAttribute("data-award-result") || "";
        const org = orb.getAttribute("data-award-org") || "";
        const year = orb.getAttribute("data-award-year") || "";
        const id = orb.getAttribute("data-award-id") || "";
        const nextKey = `${id}|${result}|${org}|${year}`;

        if (state.captionContentKey === nextKey) {
            state.captionEl.hidden = false;
            return;
        }

        const body = state.captionBody;
        const applyNow = () => {
            applyCaptionContent(state, result, org, year, id);
            state.captionContentKey = nextKey;
            state.captionEl.hidden = false;
        };

        if (!body || state.captionContentKey == null || REDUCE_MOTION) {
            clearCaptionFade(state);
            applyNow();
            return;
        }

        clearCaptionFade(state);

        /** @param {() => void} done */
        const fadeSwap = (done) => {
            body.classList.add("is-faded");

            const finishOut = () => {
                body.removeEventListener("transitionend", onTransitionEnd);
                if (state.captionFadeTimer != null) {
                    window.clearTimeout(state.captionFadeTimer);
                    state.captionFadeTimer = null;
                }
                done();
                requestAnimationFrame(() => {
                    body.classList.remove("is-faded");
                });
            };

            const onTransitionEnd = (ev) => {
                if (ev.target !== body || ev.propertyName !== "opacity") return;
                finishOut();
            };

            body.addEventListener("transitionend", onTransitionEnd);
            state.captionFadeTimer = window.setTimeout(finishOut, CAPTION_FADE_MS + 50);
        };

        fadeSwap(applyNow);
    }

    /**
     * @param {HTMLElement} track
     */
    function dismissHint(track) {
        const state = trackState.get(track);
        if (!state?.hintEl || state.hintEl.hidden) return;

        state.hintEl.classList.remove("is-visible");
        state.hintEl.classList.add("is-fading");
        window.setTimeout(() => {
            if (!state.hintEl) return;
            state.hintEl.hidden = true;
            state.hintEl.classList.remove("is-fading");
        }, HINT_FADE_MS);
    }

    /**
     * @param {HTMLElement} track
     */
    function maybeShowHint(track) {
        const state = trackState.get(track);
        if (!state?.hintEl || state.hintShown || state.orbs.length <= 1) return;

        state.hintShown = true;
        if (window.sessionStorage.getItem(HINT_STORAGE_KEY) === "1") return;

        window.sessionStorage.setItem(HINT_STORAGE_KEY, "1");
        state.hintEl.hidden = false;
        requestAnimationFrame(() => {
            state.hintEl?.classList.add("is-visible");
        });

        window.setTimeout(() => dismissHint(track), HINT_VISIBLE_MS);
    }

    /**
     * @param {HTMLElement} track
     * @param {number} activeIndex
     */
    function updateNav(track, activeIndex) {
        const state = trackState.get(track);
        if (!state) return;

        const count = state.orbs.length;
        const showChrome = count > 1;

        if (state.navEl) state.navEl.hidden = !showChrome;
        if (state.arrowsEl) state.arrowsEl.hidden = !showChrome;
        if (state.counterEl) {
            state.counterEl.hidden = !showChrome;
            state.counterEl.textContent = showChrome ? `${activeIndex + 1} / ${count}` : "";
        }
        if (state.prevBtn) {
            state.prevBtn.disabled = activeIndex <= 0;
        }
        if (state.nextBtn) {
            state.nextBtn.disabled = activeIndex >= count - 1;
        }

        updateCaption(track, activeIndex);

        if (!showChrome || !state.dotsEl) return;

        if (state.dotCount !== count) {
            state.dotCount = count;
            state.dotsEl.innerHTML = state.orbs
                .map(
                    (_item, i) =>
                        `<button type="button" class="award-orbs-dot" role="tab" data-orb-index="${i}" aria-label="${i + 1}件目" aria-selected="false"></button>`
                )
                .join("");

            state.dotsEl.querySelectorAll(".award-orbs-dot").forEach((btn) => {
                btn.addEventListener("click", () => {
                    dismissHint(track);
                    const idx = Number(btn.getAttribute("data-orb-index"));
                    if (!Number.isNaN(idx)) scrollToOrbIndex(track, idx);
                });
            });
        }

        state.dotsEl.querySelectorAll(".award-orbs-dot").forEach((dot, i) => {
            const isActive = i === activeIndex;
            if (dot.classList.contains("is-active") === isActive && dot.getAttribute("aria-selected") === (isActive ? "true" : "false")) {
                return;
            }
            dot.classList.toggle("is-active", isActive);
            dot.setAttribute("aria-selected", isActive ? "true" : "false");
        });
    }

    /**
     * @param {HTMLElement} track
     */
    function rebuildOrbCache(track) {
        const state = trackState.get(track);
        if (!state) return;

        state.orbs = [...track.querySelectorAll(".award-orb:not(.award-orb--add)")].map((el) => {
            const button = el.querySelector(".award-orb__button");
            return {
                el: /** @type {HTMLElement} */ (el),
                button: button instanceof HTMLElement ? button : null,
                mid: el.offsetLeft + el.offsetWidth / 2,
                scale: -1,
            };
        });
        state.centerId = null;
        state.dotCount = -1;
        state.activeIndex = 0;
        state.captionContentKey = null;
        clearCaptionFade(state);
    }

    /**
     * @param {HTMLElement} track
     */
    function updateCenterOrb(track) {
        const state = trackState.get(track);
        if (!state || state.orbs.length === 0) return;

        measureTrack(track);

        /** @type {typeof state.orbs[0]|null} */
        let closest = null;
        let minDist = Infinity;
        let activeIndex = 0;

        for (let i = 0; i < state.orbs.length; i++) {
            const item = state.orbs[i];
            const orbCenter = state.trackLeft + item.mid - track.scrollLeft;
            const dist = Math.abs(orbCenter - state.centerX);
            const scale = scaleFromDistance(dist);

            if (item.button && Math.abs(item.scale - scale) > SCALE_EPSILON) {
                item.scale = scale;
                item.button.style.transform = `scale(${scale.toFixed(3)})`;
            }

            if (dist < minDist) {
                minDist = dist;
                closest = item;
                activeIndex = i;
            }
        }

        const nextCenterId = closest ? closest.el.getAttribute("data-award-id") : null;
        const centerChanged = nextCenterId !== state.centerId;
        const indexChanged = activeIndex !== state.activeIndex;

        if (centerChanged) {
            state.centerId = nextCenterId;
            for (const item of state.orbs) {
                item.el.classList.toggle(
                    "award-orb--center",
                    item.el.getAttribute("data-award-id") === nextCenterId
                );
            }
        }

        if (indexChanged || centerChanged) {
            state.activeIndex = activeIndex;
            updateNav(track, activeIndex);
        }
    }

    /**
     * @param {HTMLElement} track
     * @param {{ skipSnap?: boolean }} [options]
     */
    function scheduleUpdate(track, options) {
        const state = trackState.get(track);
        if (!state) return;

        track.classList.add("is-scrolling");
        if (state.scrollEndTimer != null) window.clearTimeout(state.scrollEndTimer);
        state.scrollEndTimer = window.setTimeout(() => {
            track.classList.remove("is-scrolling");
            state.scrollEndTimer = null;
            if (!options?.skipSnap && !track.classList.contains("is-dragging")) {
                snapToNearestOrb(track);
                return;
            }
            updateCenterOrb(track);
        }, SCROLL_END_DEBOUNCE_MS);

        if (state.raf != null) return;
        state.raf = requestAnimationFrame(() => {
            state.raf = null;
            updateCenterOrb(track);
        });
    }

    /**
     * @param {HTMLElement} track
     */
    function bindDragScroll(track) {
        if (trackState.has(track)) return;

        trackState.set(track, {
            orbs: [],
            centerId: null,
            activeIndex: 0,
            raf: null,
            scrollEndTimer: null,
            resizeObserver: null,
            dotCount: -1,
            navEl: null,
            dotsEl: null,
            counterEl: null,
            captionEl: null,
            captionBody: null,
            captionContentKey: null,
            captionFadeTimer: null,
            captionResult: null,
            captionOrg: null,
            captionYear: null,
            captionSepResult: null,
            captionSepYear: null,
            captionMore: null,
            hintEl: null,
            hintShown: false,
            prevBtn: null,
            nextBtn: null,
            arrowsEl: null,
            carouselEl: null,
            centerX: 0,
            trackLeft: 0,
        });

        bindChrome(track);
        rebuildOrbCache(track);

        let isDragging = false;
        let dragMoved = false;
        let startX = 0;
        let scrollLeft = 0;

        track.addEventListener(
            "dragstart",
            (ev) => {
                if (ev.target instanceof HTMLImageElement) {
                    ev.preventDefault();
                }
            },
            { passive: false }
        );

        track.addEventListener(
            "pointerdown",
            (ev) => {
                const t = /** @type {HTMLElement} */ (ev.target);
                if (
                    t.closest(
                        ".award-orb__more, .award-orb__remove, .award-orb__add, input, textarea, select, [contenteditable], .inline-edit, .award-orbs-nav, .award-orbs-arrow, .award-orbs-caption"
                    )
                ) {
                    return;
                }
                dismissHint(track);
                isDragging = true;
                dragMoved = false;
                track.classList.remove("was-dragging");
                track.classList.add("is-dragging");
                startX = ev.clientX;
                scrollLeft = track.scrollLeft;
            },
            { passive: true }
        );

        track.addEventListener(
            "pointermove",
            (ev) => {
                if (!isDragging) return;
                if (Math.abs(ev.clientX - startX) > DRAG_CLICK_THRESHOLD_PX) {
                    if (!dragMoved) {
                        dragMoved = true;
                        if (!track.hasPointerCapture(ev.pointerId)) {
                            track.setPointerCapture(ev.pointerId);
                        }
                    }
                }
                if (!dragMoved) return;
                ev.preventDefault();
                track.scrollLeft = scrollLeft - (ev.clientX - startX);
                scheduleUpdate(track);
            },
            { passive: false }
        );

        const endDrag = (ev) => {
            if (!isDragging) return;
            isDragging = false;
            track.classList.remove("is-dragging");
            if (dragMoved) {
                track.classList.add("was-dragging");
                window.setTimeout(() => {
                    track.classList.remove("was-dragging");
                }, 320);
            }
            if (track.hasPointerCapture(ev.pointerId)) {
                track.releasePointerCapture(ev.pointerId);
            }
            if (dragMoved) {
                snapToNearestOrb(track);
            } else {
                updateCenterOrb(track);
            }
        };

        track.addEventListener("pointerup", endDrag);
        track.addEventListener("pointercancel", endDrag);
        track.addEventListener("scroll", () => scheduleUpdate(track), { passive: true });

        if ("onscrollend" in window) {
            track.addEventListener(
                "scrollend",
                () => {
                    if (!track.classList.contains("is-dragging")) {
                        snapToNearestOrb(track);
                    }
                },
                { passive: true }
            );
        }

        if (typeof ResizeObserver !== "undefined") {
            const ro = new ResizeObserver(() => {
                rebuildOrbCache(track);
                updateCenterOrb(track);
            });
            ro.observe(track);
            const state = trackState.get(track);
            if (state) state.resizeObserver = ro;
        } else {
            window.addEventListener("resize", () => {
                rebuildOrbCache(track);
                updateCenterOrb(track);
            });
        }
    }

    /**
     * @param {HTMLElement|null} track
     * @param {{ initialIndex?: number }} [options]
     */
    function init(track, options) {
        if (!track) return;

        if (!trackState.has(track)) {
            bindDragScroll(track);
        } else {
            bindChrome(track);
            rebuildOrbCache(track);
        }

        const index = options?.initialIndex ?? 0;
        const orbs = track.querySelectorAll(".award-orb:not(.award-orb--add)");
        const target = orbs[index];
        if (target instanceof HTMLElement) {
            const mid = target.offsetLeft + target.offsetWidth / 2;
            track.scrollLeft = Math.max(0, mid - track.clientWidth / 2);
        }

        requestAnimationFrame(() => {
            updateCenterOrb(track);
            maybeShowHint(track);
        });
    }

    /**
     * @param {HTMLElement|null} track
     */
    function refresh(track) {
        if (!track) return;
        if (!trackState.has(track)) {
            init(track);
            return;
        }
        bindChrome(track);
        rebuildOrbCache(track);
        updateCenterOrb(track);
    }

    window.AwardsOrbCarousel = {
        init,
        refresh,
        updateCenter: updateCenterOrb,
    };
})();
