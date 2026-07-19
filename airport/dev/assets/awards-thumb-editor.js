// public_html/airport/dev/assets/awards-thumb-editor.js — 受賞歴サムネのリサイズ・トリミング

/** @typedef {{ width: number, height: number, cropLeft: number, cropTop: number, cropRight: number, cropBottom: number }} ThumbFrame */

(function () {
    "use strict";

  const DEFAULT_FRAME = /** @type {ThumbFrame} */ ({
      width: 100,
      height: 0,
      cropLeft: 0,
      cropTop: 0,
      cropRight: 0,
      cropBottom: 0,
  });

  /** @type {HTMLElement|null} */
  let activeEditor = null;

  /**
   * @param {unknown} raw
   * @returns {ThumbFrame}
   */
  function normalizeFrame(raw) {
      if (!raw || typeof raw !== "object") {
          return { ...DEFAULT_FRAME };
      }
      const f = /** @type {Record<string, unknown>} */ (raw);
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
   * @param {number} n
   * @param {number} min
   * @param {number} max
   */
  function clamp(n, min, max) {
      return Math.min(max, Math.max(min, n));
  }

  /**
   * @param {HTMLElement} viewport
   * @param {HTMLImageElement} img
   * @param {ThumbFrame} frame
   */
  function applyThumbFrame(viewport, img, frame) {
      const f = normalizeFrame(frame);
      viewport.style.width = `${f.width}%`;
      if (f.height > 0) {
          viewport.style.height = `${f.height}px`;
          viewport.classList.add("award-thumb-viewport--fixed-height");
      } else {
          viewport.style.height = "";
          viewport.classList.remove("award-thumb-viewport--fixed-height");
      }
      const clip =
          f.cropTop || f.cropRight || f.cropBottom || f.cropLeft
              ? `inset(${f.cropTop}% ${f.cropRight}% ${f.cropBottom}% ${f.cropLeft}%)`
              : "";
      img.style.clipPath = clip;
      img.style.webkitClipPath = clip;
  }

  /**
   * @param {HTMLElement} viewport
   * @param {HTMLImageElement} img
   * @param {ThumbFrame} frame
   * @param {boolean} [editable]
   */
  function buildThumbMarkup(thumbSrc, frame, editable) {
      const f = normalizeFrame(frame);
      const editorClass = editable ? " award-thumb-viewport--editable" : "";
      const handles = editable
          ? `<div class="award-thumb-handles" hidden>
        <span class="award-thumb-handle award-thumb-handle--tl" data-handle="tl" title="サイズ変更"></span>
        <span class="award-thumb-handle award-thumb-handle--tr" data-handle="tr" title="サイズ変更"></span>
        <span class="award-thumb-handle award-thumb-handle--bl" data-handle="bl" title="サイズ変更"></span>
        <span class="award-thumb-handle award-thumb-handle--br" data-handle="br" title="サイズ変更"></span>
      </div>
      <div class="award-thumb-crop-layer" hidden>
        <div class="award-thumb-crop-shade award-thumb-crop-shade--top"></div>
        <div class="award-thumb-crop-shade award-thumb-crop-shade--right"></div>
        <div class="award-thumb-crop-shade award-thumb-crop-shade--bottom"></div>
        <div class="award-thumb-crop-shade award-thumb-crop-shade--left"></div>
        <div class="award-thumb-crop-box">
          <span class="award-thumb-crop-handle award-thumb-crop-handle--tl" data-crop-handle="tl"></span>
          <span class="award-thumb-crop-handle award-thumb-crop-handle--tr" data-crop-handle="tr"></span>
          <span class="award-thumb-crop-handle award-thumb-crop-handle--bl" data-crop-handle="bl"></span>
          <span class="award-thumb-crop-handle award-thumb-crop-handle--br" data-crop-handle="br"></span>
        </div>
        <div class="award-thumb-crop-toolbar">
          <button type="button" class="btn btn-secondary btn-sm" data-crop-apply>トリミングを適用</button>
          <button type="button" class="btn btn-secondary btn-sm" data-crop-cancel>キャンセル</button>
        </div>
      </div>
      <div class="award-thumb-context-menu" hidden>
        <button type="button" data-crop-start>トリミング</button>
        <button type="button" data-crop-reset>トリミングをリセット</button>
      </div>`
          : "";

      return `<div class="award-detail__thumb-wrap">
      <div class="award-thumb-viewport${editorClass}" data-thumb-viewport data-frame='${escapeAttr(JSON.stringify(f))}'>
        <img class="award-detail__thumb" src="${escapeAttr(thumbSrc)}" alt="">
        ${handles}
      </div>
    </div>`;
  }

  /**
   * @param {string} s
   */
  function escapeAttr(s) {
      return String(s)
          .replace(/&/g, "&amp;")
          .replace(/"/g, "&quot;")
          .replace(/</g, "&lt;");
  }

  /**
   * @param {HTMLElement} viewport
   * @returns {ThumbFrame}
   */
  function readFrameFromViewport(viewport) {
      try {
          const raw = viewport.getAttribute("data-frame");
          return normalizeFrame(raw ? JSON.parse(raw) : DEFAULT_FRAME);
      } catch {
          return { ...DEFAULT_FRAME };
      }
  }

  /**
   * @param {HTMLElement} viewport
   * @param {ThumbFrame} frame
   */
  function writeFrameToViewport(viewport, frame) {
      const f = normalizeFrame(frame);
      viewport.setAttribute("data-frame", JSON.stringify(f));
      const img = viewport.querySelector(".award-detail__thumb");
      if (img instanceof HTMLImageElement) {
          applyThumbFrame(viewport, img, f);
      }
  }

  /**
   * @param {HTMLElement} viewport
   * @param {(frame: ThumbFrame) => void} onChange
   */
  function bindThumbEditor(viewport, onChange) {
      const img = viewport.querySelector(".award-detail__thumb");
      if (!(img instanceof HTMLImageElement)) return;

      const handles = viewport.querySelector(".award-thumb-handles");
      const cropLayer = viewport.querySelector(".award-thumb-crop-layer");
      const contextMenu = viewport.querySelector(".award-thumb-context-menu");
      if (!handles || !cropLayer || !contextMenu) return;

      let frame = readFrameFromViewport(viewport);

      const applyAndNotify = () => {
          writeFrameToViewport(viewport, frame);
          onChange(frame);
      };

      const showHandles = () => {
          if (activeEditor && activeEditor !== viewport) {
              deactivateEditor(activeEditor);
          }
          activeEditor = viewport;
          viewport.classList.add("is-active");
          handles.hidden = false;
      };

      viewport.addEventListener("click", (ev) => {
          const t = /** @type {HTMLElement} */ (ev.target);
          if (t.closest(".award-thumb-crop-layer") || t.closest(".award-thumb-context-menu")) return;
          if (t.closest("[data-handle]")) return;
          ev.stopPropagation();
          showHandles();
      });

      document.addEventListener(
          "click",
          (ev) => {
              if (!viewport.classList.contains("is-active")) return;
              const t = /** @type {HTMLElement} */ (ev.target);
              if (viewport.contains(t)) return;
              deactivateEditor(viewport);
          },
          true
      );

      viewport.addEventListener("contextmenu", (ev) => {
          ev.preventDefault();
          showHandles();
          contextMenu.hidden = false;
          contextMenu.style.left = `${ev.offsetX}px`;
          contextMenu.style.top = `${ev.offsetY}px`;
      });

      contextMenu.querySelector("[data-crop-start]")?.addEventListener("click", () => {
          contextMenu.hidden = true;
          startCropMode(viewport, img, cropLayer, () => frame, (next) => {
              frame = next;
              applyAndNotify();
          });
      });

      contextMenu.querySelector("[data-crop-reset]")?.addEventListener("click", () => {
          contextMenu.hidden = true;
          frame = { ...frame, cropLeft: 0, cropTop: 0, cropRight: 0, cropBottom: 0 };
          applyAndNotify();
      });

      handles.querySelectorAll("[data-handle]").forEach((handle) => {
          handle.addEventListener("pointerdown", (ev) => {
              ev.preventDefault();
              ev.stopPropagation();
              const corner = handle.getAttribute("data-handle") || "br";
              startResize(viewport, img, corner, () => frame, (next) => {
                  frame = next;
                  applyAndNotify();
              }, /** @type {PointerEvent} */ (ev));
          });
      });

      if (img.complete) {
          applyThumbFrame(viewport, img, frame);
      } else {
          img.addEventListener("load", () => applyThumbFrame(viewport, img, frame), { once: true });
      }
  }

  /**
   * @param {HTMLElement} viewport
   */
  function deactivateEditor(viewport) {
      viewport.classList.remove("is-active");
      const handles = viewport.querySelector(".award-thumb-handles");
      const menu = viewport.querySelector(".award-thumb-context-menu");
      const crop = viewport.querySelector(".award-thumb-crop-layer");
      if (handles) handles.hidden = true;
      if (menu) menu.hidden = true;
      if (crop) crop.hidden = true;
      if (activeEditor === viewport) activeEditor = null;
  }

  /**
   * @param {HTMLElement} viewport
   * @param {HTMLImageElement} img
   * @param {string} corner
   * @param {() => ThumbFrame} getFrame
   * @param {(f: ThumbFrame) => void} setFrame
   * @param {PointerEvent} startEv
   */
  function startResize(viewport, img, corner, getFrame, setFrame, startEv) {
      const wrap = viewport.closest(".award-detail__thumb-wrap");
      if (!wrap) return;
      const wrapRect = wrap.getBoundingClientRect();
      const startFrame = { ...getFrame() };
      const startX = startEv.clientX;
      const startY = startEv.clientY;
      const startW = (startFrame.width / 100) * wrapRect.width;
      const startH = startFrame.height > 0 ? startFrame.height : img.getBoundingClientRect().height;

      const onMove = (ev) => {
          const dx = ev.clientX - startX;
          const dy = ev.clientY - startY;
          let newW = startW;
          let newH = startH;

          if (corner.includes("r")) newW = startW + dx;
          if (corner.includes("l")) newW = startW - dx;
          if (corner.includes("b")) newH = startH + dy;
          if (corner.includes("t")) newH = startH - dy;

          newW = clamp(newW, wrapRect.width * 0.3, wrapRect.width);
          newH = clamp(newH, 40, 480);

          setFrame({
              ...startFrame,
              width: Math.round((newW / wrapRect.width) * 100),
              height: Math.round(newH),
          });
      };

      const onUp = () => {
          window.removeEventListener("pointermove", onMove);
          window.removeEventListener("pointerup", onUp);
      };

      window.addEventListener("pointermove", onMove);
      window.addEventListener("pointerup", onUp);
  }

  /**
   * @param {HTMLElement} viewport
   * @param {HTMLImageElement} img
   * @param {HTMLElement} cropLayer
   * @param {() => ThumbFrame} getFrame
   * @param {(f: ThumbFrame) => void} setFrame
   */
  function startCropMode(viewport, img, cropLayer, getFrame, setFrame) {
      cropLayer.hidden = false;
      const box = cropLayer.querySelector(".award-thumb-crop-box");
      if (!box) return;

      const imgRect = () => img.getBoundingClientRect();
      const vpRect = () => viewport.getBoundingClientRect();

      const frame = getFrame();
      const ir = imgRect();
      let left = (frame.cropLeft / 100) * ir.width;
      let top = (frame.cropTop / 100) * ir.height;
      let right = ir.width - (frame.cropRight / 100) * ir.width;
      let bottom = ir.height - (frame.cropBottom / 100) * ir.height;

      const minSize = 24;

      const syncBox = () => {
          const base = vpRect();
          const im = imgRect();
          const l = im.left - base.left + left;
          const t = im.top - base.top + top;
          box.style.left = `${l}px`;
          box.style.top = `${t}px`;
          box.style.width = `${Math.max(minSize, right - left)}px`;
          box.style.height = `${Math.max(minSize, bottom - top)}px`;
          updateShades(cropLayer, im, base, left, top, right, bottom);
      };

      syncBox();

      /** @type {string|null} */
      let dragHandle = null;
      let dragStartX = 0;
      let dragStartY = 0;
      let dragLeft = 0;
      let dragTop = 0;
      let dragRight = 0;
      let dragBottom = 0;

      const onCropDown = (ev) => {
          const t = /** @type {HTMLElement} */ (ev.target);
          const handle = t.closest("[data-crop-handle]");
          if (!handle) return;
          ev.preventDefault();
          dragHandle = handle.getAttribute("data-crop-handle");
          dragStartX = ev.clientX;
          dragStartY = ev.clientY;
          dragLeft = left;
          dragTop = top;
          dragRight = right;
          dragBottom = bottom;
          window.addEventListener("pointermove", onCropMove);
          window.addEventListener("pointerup", onCropUp);
      };

      const onCropMove = (ev) => {
          if (!dragHandle) return;
          const im = imgRect();
          const dx = ev.clientX - dragStartX;
          const dy = ev.clientY - dragStartY;
          let l = dragLeft;
          let t = dragTop;
          let r = dragRight;
          let b = dragBottom;

          if (dragHandle.includes("l")) l = dragLeft + dx;
          if (dragHandle.includes("r")) r = dragRight + dx;
          if (dragHandle.includes("t")) t = dragTop + dy;
          if (dragHandle.includes("b")) b = dragBottom + dy;

          l = clamp(l, 0, im.width - minSize);
          t = clamp(t, 0, im.height - minSize);
          r = clamp(r, minSize, im.width);
          b = clamp(b, minSize, im.height);
          if (r - l < minSize) r = l + minSize;
          if (b - t < minSize) b = t + minSize;

          left = l;
          top = t;
          right = r;
          bottom = b;
          syncBox();
      };

      const onCropUp = () => {
          dragHandle = null;
          window.removeEventListener("pointermove", onCropMove);
          window.removeEventListener("pointerup", onCropUp);
      };

      const applyCrop = () => {
          const im = imgRect();
          const w = im.width || 1;
          const h = im.height || 1;
          setFrame({
              ...getFrame(),
              cropLeft: (left / w) * 100,
              cropTop: (top / h) * 100,
              cropRight: ((w - right) / w) * 100,
              cropBottom: ((h - bottom) / h) * 100,
          });
          cropLayer.hidden = true;
          cleanup();
      };

      const cancelCrop = () => {
          cropLayer.hidden = true;
          cleanup();
      };

      const cleanup = () => {
          box.removeEventListener("pointerdown", onCropDown);
          cropLayer.querySelector("[data-crop-apply]")?.removeEventListener("click", applyCrop);
          cropLayer.querySelector("[data-crop-cancel]")?.removeEventListener("click", cancelCrop);
      };

      box.addEventListener("pointerdown", onCropDown);
      cropLayer.querySelector("[data-crop-apply]")?.addEventListener("click", applyCrop);
      cropLayer.querySelector("[data-crop-cancel]")?.addEventListener("click", cancelCrop);
  }

  /**
   * @param {HTMLElement} layer
   * @param {DOMRect} im
   * @param {DOMRect} base
   * @param {number} left
   * @param {number} top
   * @param {number} right
   * @param {number} bottom
   */
  function updateShades(layer, im, base, left, top, right, bottom) {
      const l = im.left - base.left + left;
      const t = im.top - base.top + top;
      const w = right - left;
      const h = bottom - top;
      const iw = im.width;
      const ih = im.height;
      const il = im.left - base.left;
      const it = im.top - base.top;

      const topShade = layer.querySelector(".award-thumb-crop-shade--top");
      const rightShade = layer.querySelector(".award-thumb-crop-shade--right");
      const bottomShade = layer.querySelector(".award-thumb-crop-shade--bottom");
      const leftShade = layer.querySelector(".award-thumb-crop-shade--left");

      if (topShade instanceof HTMLElement) {
          topShade.style.cssText = `left:${il}px;top:${it}px;width:${iw}px;height:${top}px`;
      }
      if (leftShade instanceof HTMLElement) {
          leftShade.style.cssText = `left:${il}px;top:${t}px;width:${left}px;height:${h}px`;
      }
      if (rightShade instanceof HTMLElement) {
          rightShade.style.cssText = `left:${l + w}px;top:${t}px;width:${iw - right}px;height:${h}px`;
      }
      if (bottomShade instanceof HTMLElement) {
          bottomShade.style.cssText = `left:${il}px;top:${t + h}px;width:${iw}px;height:${ih - bottom}px`;
      }
  }

  window.AwardThumbEditor = {
      DEFAULT_FRAME,
      normalizeFrame,
      applyThumbFrame,
      buildThumbMarkup,
      bindThumbEditor,
      readFrameFromViewport,
  };
})();
