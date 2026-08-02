/** 開発トップ専用：入場ロードアニメーション */
function initPortalLoader() {
    const loader = document.getElementById('portal-loader');
    if (!loader) return;

    const bar = document.getElementById('portal-loader-bar');
    const percentEl = document.getElementById('portal-loader-percent');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const revealPage = () => {
        document.body.classList.remove('is-loading');
        document.body.classList.add('is-ready');
        loader.classList.add('is-exiting');
        loader.setAttribute('aria-hidden', 'true');
        window.setTimeout(() => {
            loader.remove();
            initPortalHomeAnimations();
        }, 320);
    };

    const setProgress = value => {
        const progress = Math.max(0, Math.min(100, value));
        if (bar) bar.style.width = `${progress}%`;
        if (percentEl) percentEl.textContent = `${Math.round(progress)}%`;
        return progress;
    };

    setProgress(0);
    loader.classList.add('is-visible');

    if (prefersReducedMotion) {
        revealPage();
        return;
    }

    let assetsLoaded = document.readyState === 'complete';
    if (!assetsLoaded) {
        window.addEventListener('load', () => {
            assetsLoaded = true;
        }, { once: true });
    }

    const INTRO_MS = 180;
    const MAIN_MS = 650;
    const FINISH_MS = 200;
    const HOLD_MS = 100;
    const MAX_WAIT_MS = 1800;
    const easeOutCubic = t => 1 - Math.pow(1 - t, 3);
    const loaderStart = performance.now();

    window.setTimeout(() => {
        const mainStart = performance.now();
        let phase = 'main';
        let finishStart = 0;
        let lastProgress = 0;

        const tick = now => {
            const forceFinish = now - loaderStart >= MAX_WAIT_MS;

            if (phase === 'main') {
                const elapsed = now - mainStart;
                const t = Math.min(elapsed / MAIN_MS, 1);
                let progress = easeOutCubic(t) * 88;

                if (!assetsLoaded && !forceFinish) {
                    progress = Math.min(progress, 85);
                }

                lastProgress = setProgress(progress);

                const canFinish = assetsLoaded || forceFinish;
                if (canFinish && (elapsed >= MAIN_MS * 0.75 || forceFinish)) {
                    phase = 'finish';
                    finishStart = now;
                } else if (elapsed < MAIN_MS || !canFinish) {
                    requestAnimationFrame(tick);
                    return;
                } else {
                    phase = 'finish';
                    finishStart = now;
                }
            }

            if (phase === 'finish') {
                const finElapsed = now - finishStart;
                const finT = Math.min(finElapsed / FINISH_MS, 1);
                lastProgress = setProgress(lastProgress + (100 - lastProgress) * easeOutCubic(finT));

                if (finT < 1) {
                    requestAnimationFrame(tick);
                    return;
                }

                window.setTimeout(revealPage, HOLD_MS);
            }
        };

        requestAnimationFrame(tick);
    }, INTRO_MS);
}

/** 開発トップ専用：スクロール連動のフェードイン要素を監視 */
let portalRevealObserver;

function observePortalRevealScroll(root = document) {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const elements = root.querySelectorAll('.portal-reveal-scroll:not(.is-visible)');

    if (!elements.length) return;

    if (prefersReducedMotion) {
        elements.forEach(element => {
            element.classList.add('is-visible');
            element.style.opacity = '1';
            element.style.transform = 'none';
        });
        return;
    }

    if (!portalRevealObserver) {
        portalRevealObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                portalRevealObserver.unobserve(entry.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    }

    elements.forEach(element => {
        portalRevealObserver.observe(element);
    });
}

/** 開発トップ専用：コンテンツの段階表示・スクロール演出 */
function initPortalHomeAnimations() {
    if (!document.body.classList.contains('portal-home')) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.portal-desk__masthead > *').forEach((element, index) => {
        element.classList.add('portal-reveal-item');
        element.style.animationDelay = `${0.12 + index * 0.07}s`;
    });

    document.querySelectorAll('.portal-catalog__item').forEach((element, index) => {
        element.classList.add('portal-reveal-item');
        element.style.animationDelay = `${0.35 + index * 0.09}s`;
    });

    if (prefersReducedMotion) {
        document.querySelectorAll('.portal-reveal-item, .portal-reveal-scroll').forEach(element => {
            element.classList.add('is-visible');
            element.style.opacity = '1';
            element.style.transform = 'none';
        });
        return;
    }

    document.querySelectorAll(
        '.section-title, .section-subtitle, .section-spaced, .developer-year__title, .developer-card, .list-card, .muted-box, .portal-news-list li, .update-list li'
    ).forEach(element => {
        element.classList.add('portal-reveal-scroll');
    });

    observePortalRevealScroll();

    document.querySelectorAll('.portal-stats__item dd').forEach(element => {
        const target = Number.parseInt(element.textContent, 10);
        if (Number.isNaN(target)) return;

        const statItem = element.closest('.portal-stats__item');
        let started = false;

        const countUp = () => {
            if (started) return;
            started = true;
            statItem?.classList.add('is-counted');

            const duration = 700;
            const start = performance.now();

            const frame = now => {
                const t = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - t, 3);
                element.textContent = String(Math.round(target * eased));
                if (t < 1) requestAnimationFrame(frame);
            };

            requestAnimationFrame(frame);
        };

        const statObserver = new IntersectionObserver(entries => {
            if (entries.some(entry => entry.isIntersecting)) {
                countUp();
                statObserver.disconnect();
            }
        }, { threshold: 0.5 });

        if (statItem) statObserver.observe(statItem);
    });
}

if (document.getElementById('portal-loader')) {
    initPortalLoader();
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.body.classList.contains('portal-home') && !document.getElementById('portal-loader')) {
        document.body.classList.add('is-ready');
        initPortalHomeAnimations();
    }

    const header = document.querySelector('.site-header');
    const yearHolder = document.querySelector('[data-current-year]');
    const scrollLinks = document.querySelectorAll('[data-scroll]');
    const scrollTargets = document.querySelectorAll('[data-scroll-target]');
    const downloadModal = document.getElementById('download-modal');

    const scrollToSelector = selector => {
        if (!selector) return;
        const target = document.querySelector(selector);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    if (yearHolder) {
        yearHolder.textContent = new Date().getFullYear().toString();
    }

    if (header) {
        const toggleHeaderShadow = () => {
            if (window.scrollY > 24) {
                header.classList.add('is-active');
            } else {
                header.classList.remove('is-active');
            }
        };
        toggleHeaderShadow();
        window.addEventListener('scroll', toggleHeaderShadow);
    }

    scrollLinks.forEach(link => {
        link.addEventListener('click', event => {
            const targetId = link.getAttribute('href');
            if (!targetId || !targetId.startsWith('#')) return;
            event.preventDefault();
            scrollToSelector(targetId);
        });
    });

    scrollTargets.forEach(card => {
        const selector = card.getAttribute('data-scroll-target');
        const triggerScroll = event => {
            if (event) {
                event.preventDefault();
            }
            scrollToSelector(selector);
        };

        card.addEventListener('click', triggerScroll);
        card.addEventListener('keypress', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                triggerScroll(event);
            }
        });
    });

    const initNavMenus = () => {
        const toggles = document.querySelectorAll('.nav-toggle');
        toggles.forEach(toggle => {
            const navLinks = toggle.nextElementSibling;
            const closeMenu = () => {
                toggle.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                navLinks?.classList.remove('is-open');
            };

            toggle.addEventListener('click', () => {
                const isOpen = !toggle.classList.contains('is-open');
                if (isOpen) {
                    toggle.classList.add('is-open');
                    toggle.setAttribute('aria-expanded', 'true');
                    navLinks?.classList.add('is-open');
                } else {
                    closeMenu();
                }
            });

            navLinks?.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 720) {
                        closeMenu();
                    }
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 720) {
                    closeMenu();
                }
            });
        });
    };
    initNavMenus();

    const initDemoVideoHero = () => {
        const video = document.getElementById('demoVideo');
        const hero = document.querySelector('.demo-video-hero');
        if (!video || !hero) return;

        const playPauseBtn = document.getElementById('demoPlayPauseBtn');
        const controls = document.getElementById('demoVideoControls');
        const progress = document.getElementById('demoVideoProgress');
        const progressBar = document.getElementById('demoVideoProgressBar');
        const timeDisplay = document.getElementById('demoVideoTime');
        const volumeBtn = document.getElementById('demoVolumeBtn');
        const scrollHint = hero.querySelector('.demo-video-hero__scroll-hint');

        const formatTime = seconds => {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        };

        const updateProgress = () => {
            if (!video.duration) return;
            const percent = (video.currentTime / video.duration) * 100;
            progressBar.style.width = `${percent}%`;
            timeDisplay.textContent = `${formatTime(video.currentTime)} / ${formatTime(video.duration)}`;
        };

        const setPlayingState = isPlaying => {
            playPauseBtn.textContent = isPlaying ? '⏸' : '▶';
        };

        const showControls = () => {
            controls?.classList.add('is-visible');
        };

        const startPlayback = () => {
            video.muted = false;
            video.loop = false;
            showControls();
            video.play().then(() => {
                setPlayingState(true);
            }).catch(() => {
                video.muted = true;
                video.play();
            });
        };

        const togglePlay = () => {
            if (video.paused) {
                startPlayback();
            } else {
                video.pause();
                setPlayingState(false);
            }
        };

        playPauseBtn?.addEventListener('click', togglePlay);

        video.addEventListener('timeupdate', updateProgress);
        video.addEventListener('loadedmetadata', updateProgress);
        video.addEventListener('ended', () => {
            setPlayingState(false);
            video.loop = true;
            video.muted = true;
            video.currentTime = 0;
            video.play();
            controls?.classList.remove('is-visible');
        });

        progress?.addEventListener('click', event => {
            if (!video.duration) return;
            const rect = progress.getBoundingClientRect();
            const ratio = (event.clientX - rect.left) / rect.width;
            video.currentTime = ratio * video.duration;
            updateProgress();
        });

        volumeBtn?.addEventListener('click', () => {
            video.muted = !video.muted;
            volumeBtn.textContent = video.muted ? '🔇' : '🔊';
        });

        scrollHint?.addEventListener('click', () => {
            const target = scrollHint.getAttribute('data-scroll');
            if (target) scrollToSelector(target);
        });

        hero.addEventListener('mouseenter', () => {
            if (!video.paused && !video.muted) {
                showControls();
            }
        });
    };
    initDemoVideoHero();

    const startDownload = url => {
        if (!url) return;
        const tempLink = document.createElement('a');
        tempLink.href = url;
        tempLink.setAttribute('download', '');
        document.body.appendChild(tempLink);
        tempLink.click();
        document.body.removeChild(tempLink);
    };

    // --- models.json から3Dモデルカードを生成 ---
    const escapeHtml = (value) => {
        return String(value == null ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    };

    const PORTAL_NEWS_MAX = 5;

    /** 開発トップ: data/news.json を日付の新しい順で最大5件表示 */
    const initPortalNews = () => {
        const list = document.getElementById('portalNewsList');
        if (!list) return;

        fetch('../data/news.json', { cache: 'no-cache' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('news.json fetch failed');
                }
                return response.json();
            })
            .then(items => {
                if (!Array.isArray(items) || items.length === 0) {
                    list.innerHTML = '<li class="portal-news-list__empty">お知らせはまだありません。</li>';
                    return;
                }

                const sorted = [...items].sort((a, b) => {
                    const dateA = a.date || '';
                    const dateB = b.date || '';
                    return dateB.localeCompare(dateA);
                }).slice(0, PORTAL_NEWS_MAX);

                list.innerHTML = sorted.map(item => {
                    const date = escapeHtml(item.date || '');
                    const datetime = escapeHtml((item.date || '').replace(/\./g, '-'));
                    const text = item.text?.ja || '';
                    return `
                        <li class="portal-news-list__item">
                            <time class="portal-news-list__date" datetime="${datetime}">${date}</time>
                            <span class="portal-news-list__text">${text}</span>
                        </li>
                    `;
                }).join('');

                list.querySelectorAll('.portal-news-list__item').forEach((element, index) => {
                    element.classList.add('portal-reveal-scroll');
                    element.style.transitionDelay = `${index * 0.05}s`;
                });
                observePortalRevealScroll(list);
            })
            .catch(error => {
                console.error(error);
                list.innerHTML = '<li class="portal-news-list__empty">お知らせを読み込めませんでした。</li>';
            });
    };

    /** 未配置の配布ファイルを「準備中」表示にする */
    const markAssetPending = link => {
        link.dataset.pendingAsset = 'true';
        link.removeAttribute('href');
        link.removeAttribute('download');
        link.classList.add('is-asset-pending');
        if (link.classList.contains('sim-data-link')) {
            link.setAttribute('aria-disabled', 'true');
            link.title = '配布準備中';
        } else {
            const label = (link.textContent || '').trim();
            if (!label.includes('準備中')) {
                link.textContent = `${label}（準備中）`;
            }
        }
    };

    const initOptionalAssets = async (root = document) => {
        const links = root.querySelectorAll('a[data-asset-optional][href]:not([data-pending-asset])');
        await Promise.all([...links].map(async link => {
            const href = link.getAttribute('href');
            if (!href || href === '#') return;
            try {
                const url = new URL(href, window.location.href);
                const head = await fetch(url.href, { method: 'HEAD' });
                let ok = head.ok;
                if (!ok && head.status === 405) {
                    const getRes = await fetch(url.href, { method: 'GET' });
                    ok = getRes.ok;
                }
                if (!ok) markAssetPending(link);
            } catch {
                markAssetPending(link);
            }
        }));
    };

    const renderModelCards = () => {
        const highContainer = document.getElementById('high-poly-list');
        const midContainer = document.getElementById('mid-poly-list');
        const lowContainer = document.getElementById('low-poly-list');
        const miscContainer = document.getElementById('misc-models-list');

        if (!highContainer && !midContainer && !lowContainer && !miscContainer) {
            return;
        }

        // ディレクトリ構造変更に伴うパス調整
        // modeling/index.html から呼ぶ場合は ../assets/models.json
        // development/index.html から呼ぶ場合は assets/models.json (ただしdevelopment/index.htmlにはコンテナがないので実行されない)
        // 汎用的に、スクリプト自身が assets/ にあるので、ページの階層深さに応じてプレフィックスを決める
        
        // 簡易判定: URLに '/modeling/' が含まれていれば1階層深い
        const isDeep = location.pathname.includes('/modeling/');
        const prefix = isDeep ? '../' : '';
        const jsonPath = prefix + 'assets/models.json';

        fetch(jsonPath, { cache: 'no-cache' })
            .then(response => {
                if (!response.ok) {
                    throw new Error('models.json の取得に失敗しました');
                }
                return response.json();
            })
            .then(models => {
                if (!Array.isArray(models)) return;
                
                // orderの昇順でソート（orderが小さいほど新しい）
                models.sort((a, b) => {
                    const orderA = a.order !== undefined ? a.order : 999999;
                    const orderB = b.order !== undefined ? b.order : 999999;
                    return orderA - orderB;
                });

                const buckets = {
                    high: highContainer,
                    mid: midContainer,
                    low: lowContainer,
                    misc: miscContainer
                };

                Object.values(buckets).forEach(c => {
                    if (c) c.innerHTML = '';
                });

                models.forEach(model => {
                    const container = buckets[model.category];
                    if (!container) return;

                    const title = escapeHtml(model.title || 'モデル');
                    const poly = escapeHtml(model.poly || '');
                    const fileSize = escapeHtml(model.fileSize || '');
                    const note = escapeHtml(model.note || '');
                    const descParts = [poly, fileSize, note].filter(Boolean);
                    const description = descParts.join(' / ');
                    
                    // JSON内のパスは "assets/..." なので、深い階層からは "../" を付与する
                    const rawThumb = model.thumbPath || 'assets/images/thumb-placeholder.svg';
                    const rawGlb = model.glbPath || '#';
                    
                    const thumb = prefix + escapeHtml(rawThumb);
                    const glbPath = rawGlb === '#' ? '#' : prefix + escapeHtml(rawGlb);

                    const cardHtml = `
                        <article class="card">
                            <img class="thumb" src="${thumb}" alt="${title} サムネイル">
                            <h4>${title}</h4>
                            <p class="download-meta">${description}</p>
                            <a class="btn btn-secondary download-btn"
                               href="${glbPath}"
                               data-asset-optional
                               data-file="${glbPath}"
                               data-file-name="${title}.glb"
                               data-file-size="${fileSize}"
                               data-thumb="${thumb}"
                               data-title="${title}"
                               data-description="${description}">GLBを取得</a>
                        </article>
                    `;
                    container.insertAdjacentHTML('beforeend', cardHtml);
                });
                initOptionalAssets(highContainer?.parentElement || document);
            })
            .catch(err => {
                console.error(err);
            });
    };

    renderModelCards();
    initPortalNews();
    initOptionalAssets();

    if (downloadModal) {
        const thumbImg = document.getElementById('modal-thumb');
        const modalTitle = document.getElementById('modal-title');
        const modalDescription = document.getElementById('modal-description');
        const agreeCheckbox = document.getElementById('download-agree');
        const downloadList = document.getElementById('download-list');
        let currentDownloads = [];

        const closeModal = () => {
            downloadModal.classList.remove('is-open');
            document.body.classList.remove('modal-open');
            agreeCheckbox.checked = false;
            currentDownloads = [];
            if (downloadList) {
                downloadList.innerHTML = '';
            }
        };

        const buildDownloadEntries = button => {
            const downloads = [];
            const primaryUrl = button.getAttribute('data-file') || button.getAttribute('href');
            if (primaryUrl) {
                downloads.push({
                    name: button.getAttribute('data-file-name') || primaryUrl.split('/').pop(),
                    size: button.getAttribute('data-file-size') || '',
                    url: primaryUrl
                });
            }

            const secondaryUrl = button.getAttribute('data-file-secondary');
            if (secondaryUrl) {
                downloads.push({
                    name: button.getAttribute('data-file-secondary-name') || secondaryUrl.split('/').pop(),
                    size: button.getAttribute('data-file-secondary-size') || '',
                    url: secondaryUrl
                });
            }

            return downloads;
        };

        const renderDownloadList = () => {
            if (!downloadList) return;
            downloadList.innerHTML = '';
            currentDownloads.forEach((item, index) => {
                const row = document.createElement('div');
                row.className = 'download-list__item';
                row.innerHTML = `
                    <div class="download-list__info">
                        <span class="download-list__name">${item.name || 'ファイル'}</span>
                        <span class="download-list__meta">${item.size ? `サイズ: ${item.size}` : ''}</span>
                    </div>
                    <button class="btn btn-primary download-list__button" type="button" data-download-index="${index}" disabled>Download</button>
                `;
                downloadList.appendChild(row);
            });
        };

        const updateDownloadButtonState = () => {
            if (!downloadList) return;
            const buttons = downloadList.querySelectorAll('.download-list__button');
            buttons.forEach(button => {
                button.disabled = !agreeCheckbox.checked;
            });
        };

        const openModal = button => {
            // data-thumb は相対パス済みかチェックが必要だが、
            // renderModelCards で生成されたボタンなら既に prefix 付きのパスが入っているはず。
            // HTML直書きのボタンがある場合（posters.html等）は注意。
            // posters.html は現在直書きだが、data-thumb属性を使っていない（ダウンロードリンクのみ）ので影響なし。
            // もしあれば、ここで補正が必要。
            
            if (thumbImg) {
                thumbImg.src = button.getAttribute('data-thumb') || 'assets/images/thumb-placeholder.svg';
            }
            if (modalTitle) {
                modalTitle.textContent = button.getAttribute('data-title') || 'モデルデータ';
            }
            if (modalDescription) {
                modalDescription.textContent = button.getAttribute('data-description') || '';
            }
            agreeCheckbox.checked = false;
            currentDownloads = buildDownloadEntries(button);
            renderDownloadList();
            updateDownloadButtonState();
            downloadModal.classList.add('is-open');
            document.body.classList.add('modal-open');
            agreeCheckbox.focus();
        };

        document.addEventListener('click', event => {
            const closeTrigger = event.target.closest('[data-close-modal]');
            if (closeTrigger) {
                closeModal();
            }
            const downloadButton = event.target.closest('.download-btn');
            if (downloadButton) {
                event.preventDefault();
                openModal(downloadButton);
            }
        });

        agreeCheckbox.addEventListener('change', () => {
            updateDownloadButtonState();
        });

        if (downloadList) {
            downloadList.addEventListener('click', event => {
                const button = event.target.closest('.download-list__button');
                if (!button || button.disabled) return;
                const index = Number(button.getAttribute('data-download-index'));
                const target = currentDownloads[index];
                if (target?.url) {
                    startDownload(target.url);
                }
            });
        }

        window.addEventListener('keydown', event => {
            if (event.key === 'Escape' && downloadModal.classList.contains('is-open')) {
                closeModal();
            }
        });
    }
});

