<?php
// public_html/dev-admin-unified/index.php
require __DIR__ . '/config.php';
admin_require_login();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>統合管理ダッシュボード - 松山南高校</title>
    <link rel="stylesheet" href="assets/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
</head>
<body>
    <!-- ヘッダー -->
    <header class="app-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="app-title">統合管理システム</h1>
                <span class="app-subtitle">松山南高校 開発ポータル</span>
            </div>
            <div class="header-right">
                <button class="icon-btn" id="refreshBtn" title="更新">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                    </svg>
                </button>
                <button class="icon-btn" id="settingsBtn" title="設定">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                    </svg>
                </button>
                <a href="logout.php" class="icon-btn" title="ログアウト">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <!-- メインコンテンツ -->
    <div class="app-container">
        <!-- サイドバー -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <button class="nav-item active" data-tab="news">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 9h12v2H6V9zm8 5H6v-2h8v2zm4-6H6V6h12v2z"/>
                    </svg>
                    <span>ニュース</span>
                    <span class="badge" id="newsCount">0</span>
                </button>
                <button class="nav-item" data-tab="contests">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 14H7v-2h5v2zm7-4H7v-2h12v2zm0-4H7V7h12v2z"/>
                    </svg>
                    <span>コンテスト</span>
                    <span class="badge" id="contestsCount">0</span>
                </button>
                <button class="nav-item" data-tab="posters">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    <span>資料</span>
                    <span class="badge" id="postersCount">0</span>
                </button>
                <button class="nav-item" data-tab="models">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l-5.5 9h11z"/>
                        <circle cx="17.5" cy="17.5" r="4.5"/>
                        <path d="M3 13.5h8v8H3z"/>
                    </svg>
                    <span>3Dモデル</span>
                    <span class="badge" id="modelsCount">0</span>
                </button>
            </nav>
        </aside>

        <!-- メインエリア -->
        <main class="main-content">
            <!-- ツールバー -->
            <div class="toolbar">
                <div class="toolbar-left">
                    <div class="search-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <input type="text" id="searchInput" placeholder="検索...">
                    </div>
                    <div class="filter-group">
                        <select id="filterSelect" class="filter-select">
                            <option value="">すべて</option>
                        </select>
                        <select id="sortSelect" class="filter-select">
                            <option value="date-desc">新しい順</option>
                            <option value="date-asc">古い順</option>
                            <option value="title-asc">タイトル順</option>
                        </select>
                    </div>
                </div>
                <div class="toolbar-right">
                    <button class="btn btn-secondary" id="bulkDeleteBtn" style="display:none;">
                        選択を削除
                    </button>
                    <button class="btn btn-primary" id="addNewBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                        </svg>
                        新規追加
                    </button>
                </div>
            </div>

            <!-- コンテンツエリア -->
            <div class="content-area">
                <!-- ニュースタブ -->
                <div class="tab-content active" id="newsTab">
                    <div class="content-header">
                        <h2>ニュース管理</h2>
                        <label class="checkbox-label">
                            <input type="checkbox" id="selectAllNews">
                            <span>すべて選択</span>
                        </label>
                    </div>
                    <div class="items-grid" id="newsGrid">
                        <div class="loading">読み込み中...</div>
                    </div>
                </div>

                <!-- コンテストタブ -->
                <div class="tab-content" id="contestsTab">
                    <div class="content-header">
                        <h2>コンテスト管理</h2>
                        <label class="checkbox-label">
                            <input type="checkbox" id="selectAllContests">
                            <span>すべて選択</span>
                        </label>
                    </div>
                    <div class="items-grid" id="contestsGrid">
                        <div class="loading">読み込み中...</div>
                    </div>
                </div>

                <!-- 資料タブ -->
                <div class="tab-content" id="postersTab">
                    <div class="content-header">
                        <h2>資料管理</h2>
                        <label class="checkbox-label">
                            <input type="checkbox" id="selectAllPosters">
                            <span>すべて選択</span>
                        </label>
                    </div>
                    <div class="items-grid" id="postersGrid">
                        <div class="loading">読み込み中...</div>
                    </div>
                </div>

                <!-- 3Dモデルタブ -->
                <div class="tab-content" id="modelsTab">
                    <div class="content-header">
                        <h2>3Dモデル管理</h2>
                        <label class="checkbox-label">
                            <input type="checkbox" id="selectAllModels">
                            <span>すべて選択</span>
                        </label>
                    </div>
                    <div class="items-grid" id="modelsGrid">
                        <div class="loading">読み込み中...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- モーダル -->
    <div class="modal" id="editModal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-header">
                <h3 id="modalTitle">新規追加</h3>
                <button class="modal-close" id="modalCloseBtn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <!-- フォーム内容は動的に生成 -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modalCancelBtn">キャンセル</button>
                <button type="submit" form="editForm" class="btn btn-primary" id="modalSaveBtn">保存</button>
            </div>
        </div>
    </div>

    <!-- トーストメッセージ -->
    <div class="toast" id="toast"></div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="assets/admin.js"></script>
</body>
</html>
