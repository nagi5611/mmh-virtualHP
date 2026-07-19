<?php
// public_html/airport/dev/index.php
require __DIR__ . '/config.php';
admin_require_login();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ジオラマ管理</title>
    <link rel="stylesheet" href="assets/diorama-admin.css">
    <link rel="stylesheet" href="../css/theme.css?v=20260704c">
</head>
<body>
    <header class="app-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="app-title">ジオラマ管理</h1>
                <span class="app-subtitle">松山空港ジオラマ</span>
            </div>
            <div class="header-right">
                <button type="button" class="icon-btn" id="refreshBtn" title="再読み込み" aria-label="再読み込み">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                    </svg>
                </button>
                <a href="logout.php" class="icon-btn" title="ログアウト" aria-label="ログアウト">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                </a>
            </div>
        </div>
        <nav class="app-tabs" aria-label="管理メニュー">
            <button type="button" class="app-tab is-active" data-tab="news" id="tabNews">お知らせ</button>
            <button type="button" class="app-tab" data-tab="awards" id="tabAwards">受賞歴</button>
        </nav>
    </header>

    <div class="app-container">
        <main class="main-content main-content--full">
            <div id="panel-news" class="admin-panel" data-panel="news">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <div class="search-box">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                            <input type="search" id="searchInput" placeholder="検索…" autocomplete="off">
                        </div>
                        <select id="sortSelect" class="filter-select" aria-label="並び順">
                            <option value="order">表示順（新しい順）</option>
                            <option value="date-desc">日付（新しい順）</option>
                            <option value="date-asc">日付（古い順）</option>
                        </select>
                    </div>
                    <div class="toolbar-right">
                        <button type="button" class="btn btn-secondary" id="bulkDeleteBtn" hidden>選択を削除</button>
                        <button type="button" class="btn btn-primary" id="addNewBtn">お知らせを追加</button>
                    </div>
                </div>

                <div class="content-area">
                    <div class="content-header">
                        <h2>お知らせ一覧</h2>
                        <label class="checkbox-label">
                            <input type="checkbox" id="selectAllNews">
                            すべて選択
                        </label>
                    </div>
                    <div id="newsGrid" class="items-list" role="list"></div>
                </div>
            </div>

            <div id="panel-awards" class="admin-panel" data-panel="awards" hidden>
                <div class="content-area award-admin">
                    <div class="content-header">
                        <h2>受賞歴エディタ</h2>
                        <a href="../index.html#awards" class="btn btn-secondary" target="_blank" rel="noopener noreferrer">公開サイトで確認</a>
                    </div>
                    <p class="panel-hint">上のプレビューは横スクロール・ドラッグで操作できます。円内テキストをクリックして編集し、「↑ 詳しく」で下のポップアップ編集を開きます。右上の × で削除、右端の「＋」で受賞を追加できます。</p>

                    <section class="award-admin-hp-editor" aria-label="HP受賞プレビュー">
                        <h3 class="award-admin-section-title">プレビュー</h3>
                        <div class="award-admin-hp-frame">
                            <div class="award-orbs-carousel" data-award-carousel>
                                <ol id="adminHpOrbs" class="award-orbs-track award-orbs award-orbs--editable" role="list"></ol>
                            </div>
                        </div>
                    </section>

                    <section class="award-admin-popup-editor" aria-label="ポップアップ詳細編集">
                        <h3 class="award-admin-section-title" id="awardPopupEditorTitle">ポップアップ詳細編集</h3>
                        <div class="award-admin-popup-grid">
                            <div class="award-admin-popup-form" id="awardPopupForm"></div>
                            <aside class="award-admin-popup-preview-col" aria-label="ポップアッププレビュー">
                                <h4 class="award-admin-section-title">プレビュー</h4>
                                <div id="awardPopupPreview"></div>
                            </aside>
                        </div>
                    </section>

                    <details class="award-admin-more">
                        <summary class="award-admin-more__summary">その他の受賞歴を管理（追加・削除・並び替え）</summary>
                        <div class="toolbar award-admin-more__toolbar">
                            <div class="toolbar-left">
                                <div class="search-box">
                                    <input type="search" id="awardSearchInput" placeholder="検索…" autocomplete="off">
                                </div>
                            </div>
                            <div class="toolbar-right">
                                <button type="button" class="btn btn-secondary" id="awardBulkDeleteBtn" hidden>選択を削除</button>
                                <button type="button" class="btn btn-primary" id="awardAddBtn">受賞歴を追加</button>
                            </div>
                        </div>
                        <div id="awardsGrid" class="items-list" role="list"></div>
                    </details>
                </div>
            </div>
        </main>
    </div>

    <div class="modal" id="itemModal" aria-hidden="true">
        <div class="modal-overlay" data-close-modal="news"></div>
        <div class="modal-container modal-container--wide">
            <div class="modal-header">
                <h3 id="modalTitle">お知らせ</h3>
                <button type="button" class="modal-close" data-close-modal="news" aria-label="閉じる">&times;</button>
            </div>
            <form id="itemForm">
                <div class="modal-body" id="modalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close-modal="news">キャンセル</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>

    <div class="toast" id="toast" hidden></div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="assets/diorama-admin.js?v=20260703"></script>
    <script src="../assets/awards-carousel.js?v=20260704c"></script>
    <script src="assets/awards-thumb-editor.js?v=20260704"></script>
    <script src="assets/awards-admin.js?v=20260704c"></script>
    <script src="assets/admin-tabs.js?v=20260703"></script>
</body>
</html>
