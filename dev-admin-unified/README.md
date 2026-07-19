# 統合管理システム

松山南高校 開発ポータルのニュース・ポスター・3Dモデルを統合管理するシステムです。

## 機能

### 管理機能
- **ニュース管理**: お知らせの追加・編集・削除、日本語入力でGemini APIによる自動翻訳（英語・中国語）
- **ポスター管理**: 発表ポスター・論文のPDFアップロード、メタデータ管理
- **3Dモデル管理**: GLBファイル・サムネイル画像のアップロード、ポリゴン数・テクスチャ情報管理

### UI機能
- Google App風のモダンなUI（Material Design 3ベース）
- 検索・フィルタ・ソート機能
- ドラッグ&ドロップによる並び替え
- 一括選択・一括削除
- レスポンシブデザイン対応

## セットアップ

### 1. Gemini APIキーの設定

`gemini_config.sample.php` を `gemini_config.php` にコピーして、APIキーを設定してください。

```bash
cp gemini_config.sample.php gemini_config.php
```

`gemini_config.php` を編集：

```php
<?php
define('GEMINI_API_KEY', 'YOUR_API_KEY_HERE');
```

**重要**: `gemini_config.php` は `.gitignore` に追加してください。

### 2. ディレクトリ構造

```
dev-admin-unified/
├── index.php              # 統合管理ダッシュボード
├── login.php              # ログインページ
├── logout.php             # ログアウト処理
├── config.php             # 認証設定
├── gemini_config.php      # Gemini APIキー設定（要作成）
├── api/
│   ├── news.php           # ニュースAPI（CRUD）
│   ├── posters.php        # ポスターAPI（CRUD、PDFアップロード）
│   ├── models.php         # 3DモデルAPI（CRUD、GLB/画像アップロード）
│   └── translate.php      # Gemini API翻訳エンドポイント
├── assets/
│   ├── admin.css          # Google App風スタイル
│   └── admin.js           # フロントエンド機能
└── README.md              # このファイル

data/
├── news.json              # ニュースデータ
└── posters.json           # ポスターデータ

development/assets/
├── models.json            # 3Dモデルデータ
├── docs/                  # GLB/PDFファイル
└── images/                # サムネイル画像
```

### 3. パーミッション

以下のディレクトリに書き込み権限が必要です：

- `data/` （ニュース・ポスターJSON保存用）
- `development/assets/docs/` （GLB・PDFファイル保存用）
- `development/assets/images/` （サムネイル画像保存用）
- `development/assets/models.json` （3Dモデルデータ保存用）

## ログイン情報

- **URL**: `https://your-domain.com/dev-admin-unified/`
- **ユーザー名**: `mmh-admin`
- **パスワード**: `mmh@2025@5431`

**重要**: 本番環境では `config.php` のパスワードを変更してください。

## 使用方法

### ニュース管理

1. 左サイドバーから「ニュース」を選択
2. 「新規追加」ボタンをクリック
3. 日付と日本語テキストを入力
4. 「AI翻訳」ボタンで英語・中国語を自動生成（Gemini API）
5. タグを追加（任意）
6. 「保存」をクリック

### ポスター管理

1. 左サイドバーから「ポスター」を選択
2. 「新規追加」ボタンをクリック
3. コンテスト名、タイトル、年、種別、説明を入力
4. PDFファイルをアップロード
5. タグを追加（任意）
6. 「保存」をクリック

### 3Dモデル管理

1. 左サイドバーから「3Dモデル」を選択
2. 「新規追加」ボタンをクリック
3. タイトル、カテゴリ、ポリゴン数、テクスチャ有無を設定
4. GLBファイルとサムネイル画像をアップロード
5. 「保存」をクリック

### 検索・フィルタ・ソート

- **検索**: 上部の検索ボックスでキーワード検索
- **フィルタ**: ドロップダウンでタグ・年・カテゴリによる絞り込み
- **ソート**: 新しい順・古い順・タイトル順でソート

### 並び替え

カードをドラッグ&ドロップして順序を変更できます。変更は自動保存されます。

### 一括操作

1. チェックボックスで複数のアイテムを選択
2. 「すべて選択」で全アイテムを選択
3. 「選択を削除」ボタンで一括削除

## 公開側への反映

管理システムでの変更は、以下のページに自動的に反映されます：

- **ニュース**: `index.html` のお知らせセクション（`data/news.json` から読み込み）
- **ポスター**: `development/posters/index.html`（`data/posters.json` から動的生成）
- **3Dモデル**: `development/modeling/index.html`（`development/assets/models.json` から読み込み）

## 技術スタック

- **バックエンド**: PHP 8.3.21
- **フロントエンド**: Vanilla JavaScript、SortableJS
- **スタイリング**: CSS（Material Design 3ベース）
- **外部API**: Google Gemini API（翻訳）
- **データ保存**: JSONファイル

## セキュリティ

- セッションベース認証
- CSRF対策（`session_regenerate_id`）
- ファイルタイプ検証（拡張子・MIMEタイプ）
- ファイルサイズ制限
- ファイル名のサニタイズ
- 入力値のエスケープ

## 注意事項

1. **Gemini APIキー**: `gemini_config.php` をGit管理から除外してください
2. **ファイルサイズ**: ストレージ容量（150GB）を考慮してアップロードしてください
3. **既存機能の互換性**: 既存の `dev-admin` は残っており、並行運用可能です
4. **バックアップ**: データファイル（JSON）は定期的にバックアップしてください

## トラブルシューティング

### ログインできない
- セッションが有効か確認
- `config.php` のユーザー名・パスワードを確認

### ファイルアップロードが失敗する
- ディレクトリの書き込み権限を確認
- `php.ini` の `upload_max_filesize` と `post_max_size` を確認

### 翻訳が動作しない
- `gemini_config.php` が存在し、APIキーが設定されているか確認
- Gemini APIの利用制限を確認

### データが表示されない
- ブラウザのコンソールでエラーを確認
- JSONファイルの構文エラーをチェック

## サポート

問題が発生した場合は、ブラウザの開発者ツールでコンソールエラーを確認してください。
