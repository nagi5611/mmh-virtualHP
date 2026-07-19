// 統合管理システム - JavaScript

// グローバル状態
const state = {
    currentTab: 'news',
    news: [],
    posters: [],
    models: [],
    selectedItems: new Set(),
    searchTerm: '',
    filterValue: '',
    sortValue: 'date-desc',
    sortableInstances: {}
};

// 初期化
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initToolbar();
    initModal();
    loadAllData();
});

// ナビゲーション初期化
function initNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const tab = this.dataset.tab;
            switchTab(tab);
        });
    });
}

// タブ切り替え
function switchTab(tab) {
    state.currentTab = tab;
    state.selectedItems.clear();
    
    // ナビゲーション更新
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.toggle('active', item.dataset.tab === tab);
    });
    
    // タブコンテンツ更新
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.toggle('active', content.id === tab + 'Tab');
    });
    
    // ツールバー更新
    updateToolbar();
    
    // データ表示
    renderCurrentTab();
}

// ツールバー初期化
function initToolbar() {
    // 検索
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', debounce(function(e) {
        state.searchTerm = e.target.value.toLowerCase();
        renderCurrentTab();
    }, 300));
    
    // フィルタ
    const filterSelect = document.getElementById('filterSelect');
    filterSelect.addEventListener('change', function(e) {
        state.filterValue = e.target.value;
        renderCurrentTab();
    });
    
    // ソート
    const sortSelect = document.getElementById('sortSelect');
    sortSelect.addEventListener('change', function(e) {
        state.sortValue = e.target.value;
        renderCurrentTab();
    });
    
    // 新規追加ボタン
    const addNewBtn = document.getElementById('addNewBtn');
    addNewBtn.addEventListener('click', function() {
        openAddModal();
    });
    
    // 一括削除ボタン
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    bulkDeleteBtn.addEventListener('click', function() {
        bulkDelete();
    });
    
    // 更新ボタン
    const refreshBtn = document.getElementById('refreshBtn');
    refreshBtn.addEventListener('click', function() {
        loadAllData();
    });
    
    // すべて選択
    ['News', 'Posters', 'Models'].forEach(type => {
        const checkbox = document.getElementById('selectAll' + type);
        if (checkbox) {
            checkbox.addEventListener('change', function(e) {
                selectAll(e.target.checked);
            });
        }
    });
}

// ツールバー更新
function updateToolbar() {
    const filterSelect = document.getElementById('filterSelect');
    filterSelect.innerHTML = '<option value="">すべて</option>';
    
    if (state.currentTab === 'news') {
        const tags = [...new Set(state.news.flatMap(item => item.tags || []))];
        tags.forEach(tag => {
            filterSelect.innerHTML += `<option value="${tag}">${tag}</option>`;
        });
    } else if (state.currentTab === 'posters') {
        const years = [...new Set(state.posters.map(item => item.year))].sort((a, b) => b - a);
        years.forEach(year => {
            filterSelect.innerHTML += `<option value="${year}">${year}年</option>`;
        });
    } else if (state.currentTab === 'models') {
        const categories = [
            {value: 'high', label: 'High Poly'},
            {value: 'mid', label: 'Mid Poly'},
            {value: 'low', label: 'Low Poly'},
            {value: 'misc', label: 'Misc'}
        ];
        categories.forEach(cat => {
            filterSelect.innerHTML += `<option value="${cat.value}">${cat.label}</option>`;
        });
    }
    
    // 一括削除ボタン表示
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    bulkDeleteBtn.style.display = state.selectedItems.size > 0 ? 'block' : 'none';
}

// データ読み込み
async function loadAllData() {
    try {
        await Promise.all([
            loadNews(),
            loadPosters(),
            loadModels()
        ]);
        
        renderCurrentTab();
        updateCounts();
    } catch (error) {
        showToast('データ読み込みエラー', 'error');
        console.error(error);
    }
}

async function loadNews() {
    const response = await fetch('api/news.php');
    const result = await response.json();
    state.news = result.data || [];
}

async function loadPosters() {
    const response = await fetch('api/posters.php');
    const result = await response.json();
    state.posters = result.data || [];
}

async function loadModels() {
    const response = await fetch('api/models.php');
    const result = await response.json();
    state.models = result.data || [];
}

// カウント更新
function updateCounts() {
    document.getElementById('newsCount').textContent = state.news.length;
    document.getElementById('postersCount').textContent = state.posters.length;
    document.getElementById('modelsCount').textContent = state.models.length;
}

// 現在のタブをレンダリング
function renderCurrentTab() {
    switch (state.currentTab) {
        case 'news':
            renderNews();
            break;
        case 'posters':
            renderPosters();
            break;
        case 'models':
            renderModels();
            break;
    }
}

// ニュースレンダリング
function renderNews() {
    const grid = document.getElementById('newsGrid');
    let items = [...state.news];
    
    // フィルタ
    if (state.searchTerm) {
        items = items.filter(item => {
            const text = (item.text.ja + item.text.en + item.text.zh).toLowerCase();
            const tags = (item.tags || []).join(' ').toLowerCase();
            return text.includes(state.searchTerm) || tags.includes(state.searchTerm);
        });
    }
    
    if (state.filterValue) {
        items = items.filter(item => (item.tags || []).includes(state.filterValue));
    }
    
    // ソート
    items.sort((a, b) => {
        if (state.sortValue === 'date-desc') return b.date.localeCompare(a.date);
        if (state.sortValue === 'date-asc') return a.date.localeCompare(b.date);
        if (state.sortValue === 'title-asc') return a.text.ja.localeCompare(b.text.ja);
        return a.order - b.order;
    });
    
    if (items.length === 0) {
        grid.innerHTML = '<div class="loading">ニュースがありません</div>';
        return;
    }
    
    // ニュースはリスト形式で表示
    grid.className = 'items-list';
    grid.innerHTML = items.map(item => `
        <div class="item-list-row" data-id="${item.id}">
            <div class="item-checkbox">
                <input type="checkbox" ${state.selectedItems.has(item.id) ? 'checked' : ''} 
                       onchange="toggleSelect(${item.id}, this.checked)">
            </div>
            <div class="item-list-content">
                <div class="item-list-main">
                    <div class="item-title">${escapeHtml(item.text.ja)}</div>
                    <div class="item-meta">${item.date}</div>
                </div>
            </div>
            <div class="item-list-actions">
                <button class="item-btn" onclick="editItem(${item.id})">編集</button>
                <button class="item-btn danger" onclick="deleteItem(${item.id})">削除</button>
            </div>
        </div>
    `).join('');
    
    initSortable('newsGrid');
}

// ポスターレンダリング
function renderPosters() {
    const grid = document.getElementById('postersGrid');
    let items = [...state.posters];
    
    // フィルタ
    if (state.searchTerm) {
        items = items.filter(item => {
            const text = (item.title + item.contestName + item.description).toLowerCase();
            return text.includes(state.searchTerm);
        });
    }
    
    if (state.filterValue) {
        items = items.filter(item => item.year == state.filterValue);
    }
    
    // ソート
    items.sort((a, b) => {
        if (state.sortValue === 'date-desc') return b.year - a.year;
        if (state.sortValue === 'date-asc') return a.year - b.year;
        if (state.sortValue === 'title-asc') return a.title.localeCompare(b.title);
        return a.order - b.order;
    });
    
    if (items.length === 0) {
        grid.innerHTML = '<div class="loading">ポスターがありません</div>';
        return;
    }
    
    grid.innerHTML = items.map(item => `
        <div class="item-card" data-id="${item.id}">
            <div class="item-card-header">
                <div class="item-checkbox">
                    <input type="checkbox" ${state.selectedItems.has(item.id) ? 'checked' : ''} 
                           onchange="toggleSelect(${item.id}, this.checked)">
                </div>
                <div class="item-content">
                    <div class="item-title">${escapeHtml(item.title)}</div>
                    <div class="item-meta">${item.year}年 - ${escapeHtml(item.type)}</div>
                    <div class="item-tags">
                        ${(item.tags || []).map(tag => `<span class="tag">${escapeHtml(tag)}</span>`).join('')}
                    </div>
                </div>
            </div>
            <div class="item-actions">
                <a href="../${item.pdfPath}" class="item-btn" target="_blank">PDF表示</a>
                <button class="item-btn" onclick="editItem(${item.id})">編集</button>
                <button class="item-btn danger" onclick="deleteItem(${item.id})">削除</button>
            </div>
        </div>
    `).join('');
    
    initSortable('postersGrid');
}

// モデルレンダリング
function renderModels() {
    const grid = document.getElementById('modelsGrid');
    let items = [...state.models];
    
    // フィルタ
    if (state.searchTerm) {
        items = items.filter(item => {
            const text = (item.title + item.category + item.description).toLowerCase();
            return text.includes(state.searchTerm);
        });
    }
    
    if (state.filterValue) {
        items = items.filter(item => item.category === state.filterValue);
    }
    
    // ソート
    items.sort((a, b) => {
        if (state.sortValue === 'title-asc') return a.title.localeCompare(b.title);
        if (state.sortValue === 'title-desc') return b.title.localeCompare(a.title);
        return a.order - b.order;
    });
    
    if (items.length === 0) {
        grid.innerHTML = '<div class="loading">モデルがありません</div>';
        return;
    }
    
    grid.innerHTML = items.map(item => `
        <div class="item-card" data-id="${item.id}">
            <div class="item-card-header">
                <div class="item-checkbox">
                    <input type="checkbox" ${state.selectedItems.has(item.id) ? 'checked' : ''} 
                           onchange="toggleSelect(${item.id}, this.checked)">
                </div>
                <div class="item-content">
                    <div class="item-title">${escapeHtml(item.title)}</div>
                    <div class="item-meta">${item.poly} | ${item.fileSize} | ${item.note}</div>
                    ${item.thumbPath ? `<img src="../development/${item.thumbPath}" style="width:100%; height:150px; object-fit:cover; margin-top:8px; border-radius:8px;">` : ''}
                </div>
            </div>
            <div class="item-actions">
                <a href="../development/${item.glbPath}" class="item-btn" download>GLBダウンロード</a>
                <button class="item-btn" onclick="editItem(${item.id})">編集</button>
                <button class="item-btn danger" onclick="deleteItem(${item.id})">削除</button>
            </div>
        </div>
    `).join('');
    
    initSortable('modelsGrid');
}

// Sortable初期化
function initSortable(gridId) {
    const grid = document.getElementById(gridId);
    if (!grid) return;
    
    // 既存のSortableを破棄
    if (state.sortableInstances[gridId]) {
        state.sortableInstances[gridId].destroy();
    }
    
    state.sortableInstances[gridId] = Sortable.create(grid, {
        animation: 150,
        ghostClass: 'sortable-ghost',
        handle: '.item-card',
        onEnd: function(evt) {
            saveOrder();
        }
    });
}

// 並び順保存
async function saveOrder() {
    const gridId = state.currentTab + 'Grid';
    const grid = document.getElementById(gridId);
    const cards = grid.querySelectorAll('.item-card');
    const orders = Array.from(cards).map(card => parseInt(card.dataset.id));
    
    const endpoint = `api/${state.currentTab}.php`;
    
    try {
        const response = await fetch(endpoint, {
            method: 'PATCH',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({orders})
        });
        
        const result = await response.json();
        if (result.status === 'success') {
            await loadAllData();
            showToast('並び順を保存しました', 'success');
        }
    } catch (error) {
        showToast('並び順の保存に失敗しました', 'error');
        console.error(error);
    }
}

// 選択トグル
function toggleSelect(id, checked) {
    if (checked) {
        state.selectedItems.add(id);
    } else {
        state.selectedItems.delete(id);
    }
    updateToolbar();
}

// すべて選択
function selectAll(checked) {
    const items = state.currentTab === 'news' ? state.news :
                  state.currentTab === 'posters' ? state.posters : state.models;
    
    if (checked) {
        items.forEach(item => state.selectedItems.add(item.id));
    } else {
        state.selectedItems.clear();
    }
    
    renderCurrentTab();
    updateToolbar();
}

// APIレスポンスの成功判定ヘルパー関数
function isApiSuccess(result) {
    // result.dataが存在する、またはresult.statusが存在してresult.errorが存在しない場合を成功とする
    return (result.data !== undefined) || (result.status && !result.error);
}

// 一括削除
async function bulkDelete() {
    if (state.selectedItems.size === 0) return;
    
    if (!confirm(`選択した${state.selectedItems.size}件を削除しますか？`)) return;
    
    const endpoint = `api/${state.currentTab}.php`;
    const ids = Array.from(state.selectedItems);
    
    try {
        const response = await fetch(endpoint, {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ids})
        });
        
        const result = await response.json();
        console.log('Bulk delete response:', result);
        console.log('isApiSuccess:', isApiSuccess(result));
        
        if (isApiSuccess(result)) {
            state.selectedItems.clear();
            await loadAllData();
            showToast('削除しました', 'success');
        } else {
            console.error('Bulk delete failed:', result);
            showToast(result.error || '削除に失敗しました', 'error');
        }
    } catch (error) {
        showToast('削除エラー', 'error');
        console.error('Bulk delete error:', error);
    }
}

// 単一削除
async function deleteItem(id) {
    if (!confirm('削除しますか？')) return;
    
    const endpoint = `api/${state.currentTab}.php`;
    
    try {
        const response = await fetch(endpoint, {
            method: 'DELETE',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ids: [id]})
        });
        
        const result = await response.json();
        console.log('Delete response:', result);
        console.log('isApiSuccess:', isApiSuccess(result));
        
        if (isApiSuccess(result)) {
            await loadAllData();
            showToast('削除しました', 'success');
        } else {
            console.error('Delete failed:', result);
            showToast(result.error || '削除に失敗しました', 'error');
        }
    } catch (error) {
        showToast('削除エラー', 'error');
        console.error('Delete error:', error);
    }
}

// モーダル初期化
function initModal() {
    const modal = document.getElementById('editModal');
    const closeBtn = document.getElementById('modalCloseBtn');
    const cancelBtn = document.getElementById('modalCancelBtn');
    const overlay = modal.querySelector('.modal-overlay');
    
    [closeBtn, cancelBtn, overlay].forEach(el => {
        el.addEventListener('click', closeModal);
    });
    
    const form = document.getElementById('editForm');
    form.addEventListener('submit', handleFormSubmit);
}

// モーダルを開く
function openAddModal() {
    const modal = document.getElementById('editModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('editForm');
    
    title.textContent = '新規追加';
    form.innerHTML = getFormHTML();
    form.dataset.mode = 'add';
    
    modal.classList.add('active');
}

// 編集モーダルを開く
async function editItem(id) {
    const modal = document.getElementById('editModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('editForm');
    
    let item;
    if (state.currentTab === 'news') {
        item = state.news.find(n => n.id === id);
    } else if (state.currentTab === 'posters') {
        item = state.posters.find(p => p.id === id);
    } else {
        item = state.models.find(m => m.id === id);
    }
    
    if (!item) return;
    
    title.textContent = '編集';
    form.innerHTML = getFormHTML(item);
    form.dataset.mode = 'edit';
    form.dataset.id = id;
    
    modal.classList.add('active');
}

// フォームHTML取得
function getFormHTML(item = null) {
    if (state.currentTab === 'news') {
        return `
            <div class="form-group">
                <label class="form-label required">日付</label>
                <input type="text" class="form-input" name="date" value="${item?.date || ''}" required placeholder="2025.01.31">
            </div>
            <div class="form-group">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                    <label class="form-label required" style="margin: 0; flex: 1;">日本語テキスト</label>
                    <button type="button" class="btn btn-secondary" id="btnTranslateNews" onclick="translateText()">確定</button>
                </div>
                <textarea class="form-textarea" name="textJa" required>${item?.text.ja || ''}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">英語テキスト</label>
                <textarea class="form-textarea" name="textEn">${item?.text.en || ''}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">中国語テキスト</label>
                <textarea class="form-textarea" name="textZh">${item?.text.zh || ''}</textarea>
            </div>
        `;
    } else if (state.currentTab === 'posters') {
        return `
            <div class="form-group">
                <label class="form-label required">コンテスト名</label>
                <input type="text" class="form-input" name="contestName" value="${item?.contestName || ''}" required>
            </div>
            <div class="form-group">
                <label class="form-label required">タイトル</label>
                <input type="text" class="form-input" name="title" value="${item?.title || ''}" required>
            </div>
            <div class="form-group">
                <label class="form-label required">年</label>
                <input type="number" class="form-input" name="year" value="${item?.year || new Date().getFullYear()}" required>
            </div>
            <div class="form-group">
                <label class="form-label required">種別</label>
                <select class="form-select" name="type" required>
                    <option value="ポスター" ${item?.type === 'ポスター' ? 'selected' : ''}>ポスター</option>
                    <option value="スライド資料" ${item?.type === 'スライド資料' ? 'selected' : ''}>スライド資料</option>
                    <option value="論文" ${item?.type === '論文' ? 'selected' : ''}>論文</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">説明</label>
                <textarea class="form-textarea" name="description">${item?.description || ''}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label ${item ? '' : 'required'}">PDFファイル</label>
                <input type="file" class="form-input" name="pdf" accept=".pdf" ${item ? '' : 'required'}>
                ${item ? '<p class="form-help">変更しない場合は選択不要</p>' : ''}
            </div>
            <div class="form-group">
                <label class="form-label">タグ（カンマ区切り）</label>
                <input type="text" class="form-input" name="tags" value="${(item?.tags || []).join(', ')}">
            </div>
        `;
    } else {
        const polyNum = item ? item.poly.match(/\d+\.?\d*/)?.[0] || '' : '';
        const hasTexture = item ? item.note.includes('テクスチャ有り') : false;
        
        return `
            <div class="form-group">
                <label class="form-label required">タイトル</label>
                <input type="text" class="form-input" name="title" value="${item?.title || ''}" required>
            </div>
            <div class="form-group">
                <label class="form-label required">カテゴリ</label>
                <select class="form-select" name="category" required>
                    <option value="high" ${item?.category === 'high' ? 'selected' : ''}>High Poly（高ポリ）</option>
                    <option value="mid" ${item?.category === 'mid' ? 'selected' : ''}>Mid Poly（中ポリ）</option>
                    <option value="low" ${item?.category === 'low' ? 'selected' : ''}>Low Poly（低ポリ）</option>
                    <option value="misc" ${item?.category === 'misc' ? 'selected' : ''}>Misc（その他）</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">ポリゴン数（万ポリ）</label>
                <input type="number" step="0.01" class="form-input" name="polyNum" value="${polyNum}">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="hasTexture" value="1" ${hasTexture ? 'checked' : ''}>
                    <span>テクスチャ有り</span>
                </label>
            </div>
            <div class="form-group">
                <label class="form-label">説明</label>
                <textarea class="form-textarea" name="description">${item?.description || ''}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label ${item ? '' : 'required'}">GLBファイル</label>
                <input type="file" class="form-input" name="glbFile" accept=".glb,.gltf" ${item ? '' : 'required'}>
                ${item ? '<p class="form-help">変更しない場合は選択不要</p>' : ''}
            </div>
            <div class="form-group">
                <label class="form-label ${item ? '' : 'required'}">サムネイル画像</label>
                <input type="file" class="form-input" name="thumbFile" accept=".png,.jpg,.jpeg,.webp" ${item ? '' : 'required'}>
                ${item ? '<p class="form-help">変更しない場合は選択不要</p>' : ''}
            </div>
        `;
    }
}

// AI翻訳
async function translateText() {
    const jaInput = document.querySelector('[name="textJa"]');
    const textJa = jaInput ? jaInput.value.trim() : '';
    if (!textJa) {
        showToast('日本語テキストを入力してください', 'error');
        return;
    }

    const translateBtn = document.getElementById('btnTranslateNews');
    showToast('高精度翻訳中…', 'info', 0);
    if (translateBtn) {
        translateBtn.disabled = true;
        translateBtn.textContent = '高精度翻訳中…';
    }

    try {
        const response = await fetch('api/translate.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({text: textJa})
        });

        const result = await response.json();

        console.log('Translation response:', result);

        if (result.data && typeof result.data === 'object' &&
            (result.data.en !== undefined || result.data.zh !== undefined)) {

            const enField = document.querySelector('[name="textEn"]');
            const zhField = document.querySelector('[name="textZh"]');

            if (enField) {
                enField.value = result.data.en || '';
            }
            if (zhField) {
                zhField.value = result.data.zh || '';
            }

            showToast('翻訳完了', 'success');
            return;
        }

        if (result.error) {
            showToast(result.error, 'error');
        } else {
            console.error('Unexpected response structure:', result);
            showToast('翻訳に失敗しました: 予期しないレスポンス形式', 'error');
        }
    } catch (error) {
        showToast('翻訳エラー', 'error');
        console.error(error);
    } finally {
        if (translateBtn) {
            translateBtn.disabled = false;
            translateBtn.textContent = '確定';
        }
    }
}

// フォーム送信
async function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const mode = form.dataset.mode;
    const id = form.dataset.id;
    
    const formData = new FormData(form);
    
    // ニュースの場合はJSON形式
    if (state.currentTab === 'news') {
        const data = {
            date: formData.get('date'),
            text: {
                ja: formData.get('textJa'),
                en: formData.get('textEn'),
                zh: formData.get('textZh')
            },
            tags: [] // タグは使用しない
        };
        
        if (mode === 'edit') data.id = parseInt(id);
        
        const method = mode === 'add' ? 'POST' : 'PUT';
        
        try {
            const response = await fetch('api/news.php', {
                method,
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            console.log('News save response:', result);
            console.log('isApiSuccess:', isApiSuccess(result));
            
            // 成功判定
            if (isApiSuccess(result)) {
                closeModal();
                await loadAllData();
                showToast(mode === 'add' ? '追加しました' : '更新しました', 'success');
            } else {
                console.error('Save failed:', result);
                showToast(result.error || '保存に失敗しました', 'error');
            }
        } catch (error) {
            showToast('保存エラー', 'error');
            console.error('Save error:', error);
        }
    } else {
        // ポスターとモデルはマルチパート
        if (mode === 'edit') {
            formData.append('_method', 'PUT');
            formData.append('id', id);
        }
        
        try {
            const response = await fetch(`api/${state.currentTab}.php`, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            // 成功判定
            if (isApiSuccess(result)) {
                closeModal();
                await loadAllData();
                showToast(mode === 'add' ? '追加しました' : '更新しました', 'success');
            } else {
                showToast(result.error || '保存に失敗しました', 'error');
            }
        } catch (error) {
            showToast('保存エラー', 'error');
            console.error(error);
        }
    }
}

// モーダルを閉じる
function closeModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('active');
}

// トースト表示（durationMs=0 のときは手動で次の showToast まで非表示にしない）
function showToast(message, type = 'success', durationMs = 3000) {
    const toast = document.getElementById('toast');
    if (showToast._hideTimer) {
        clearTimeout(showToast._hideTimer);
        showToast._hideTimer = null;
    }
    toast.textContent = message;
    toast.className = 'toast show ' + type;

    if (durationMs > 0) {
        showToast._hideTimer = setTimeout(() => {
            toast.classList.remove('show');
            showToast._hideTimer = null;
        }, durationMs);
    }
}

// ユーティリティ
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
