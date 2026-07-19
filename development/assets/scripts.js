document.addEventListener('DOMContentLoaded', () => {
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
            })
            .catch(err => {
                console.error(err);
            });
    };

    renderModelCards();

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

