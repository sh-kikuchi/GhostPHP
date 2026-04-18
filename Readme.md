# Ghost PHP

## 1. Overview（概要）

- **Ghost PHP** は、私が開発したプライベートな PHP フレームワークです。
- [ドキュメントはこちら / You can find the documentation here](https://sh-revue.net/projects/ghostphp)

## 2. Architecture（アーキテクチャ）

```
[ Client ]
     │
     ▼
[ Router (aura/routes) ]
     │
     ▼
[ Request (requests) ]
     │   └─ Validation / Data shaping
     ▼
[ Service (services) ]   ← ★ Core（ユースケース）
     │   └─ Business Logic
     │
     │   (fetch / persist via Repository)
     ▼
   ( domain data )
     │
     ▼
[ Response ]
     │   └─ Transform data into response
     │
     ├─ JSON Response (API)
     └─ Template (HTML)
     │
     ▼
[ Client ]

----------------------------------------

[ Repository (models/repositories) ]
     │
     │   ┌────────────────────────────┐
     │   │   uses Entity              │
     │   │   - hydrate (DB → Entity)  │
     │   │   - persist (Entity → DB)  │
     │   └────────────────────────────┘
     ▼
[ Database (aura/database) ]
```

### ■ Layer Responsibilities
- Request
  - 入力データの検証・整形
- Service
  - ユースケース単位のビジネスロジック
  - Repository を通じてデータを取得・保存
- Response
  - Serviceの返したデータを最終形式（JSON / HTML）に変換
- Repository
  - データアクセス（CRUD）をカプセル化
  - Entity を用いてDBとデータをやり取りする
- Entity
  - データベースとやり取りするデータ構造


### ■ Thin Framework Philosophy

Ghost PHP は Thin Framework を志向しています。

最小限の構造のみを提供しつつ、強いレイヤー分離と拡張前提の設計を採用しています。

従来の開発現場では、Controller と Service の両方にロジックが分散し、責務が曖昧になるケースが見られることがありました。Ghost PHP ではこの構造を整理し、ビジネスロジックを Service に集約することで、責務の明確化や可読性の向上を目指しました。

また現代の開発環境では、AIによるコード生成、ライブラリ選定の高速化、開発スタイルの多様化が進んでいます。

そのため Ghost PHP は、すべてを内包するフレームワークではなく、拡張可能な最小限の基盤を提供することを目的としています。


## 3. Directory（ディレクトリ構成）

```txt
├─aura
│  ├─database       // DB接続
│  ├─https          // リクエスト・レスポンス管理
│  ├─routes         // ルーティング
│  └─utils
│      ├─commands    // CLIコマンド処理
│      └─functions   // 主にView用関数
├─config             // 設定ファイル
├─requests       // フォームデータ管理クラス
├─interfaces         // 各種インターフェース
│  ├─requests
│  ├─models
│  └─services
├─logs               // ログ
├─migrations         // マイグレーション
│  ├─csv
│  ├─migrate
│  └─seed
├─models             // モデル層
│  ├─entities
│  └─repositories
├─public             // 公開用アセット
│  └─assets
│      ├─css
│      ├─img
│      └─js
├─services           // サービス層
├─storage            // 一時ファイルなど
│  ├─csv
│  └─doc
└─templates          // テンプレート
    ├─errors         // エラーページ
    └─layouts        // ヘッダー・フッター
```

## 4. Dependencies
- guzzlehttp/guzzle
- vlucas/phpdotenv
- phpunit/phpunit
- phpmailer/phpmailer
  
## 5. Set up（セットアップ）

### ■ プロジェクトのクローンと依存パッケージのインストール / Clone & Install

```bash
git clone https://github.com/sh-kikuchi/GhostPHP.git
cd GhostPHP
composer install
```

### ■ データベース接続設定 / Connect Database

- プロジェクト直下に `.env` ファイルを作成し、以下のように設定してください:

```env
DB_HOST = 'localhost'
DB_NAME = 'test'
DB_USER = 'root'
DB_PASS = ''
PASSWORD = 'password'
```

### ■ マイグレーションとシーディング / Migration & Seeding

- テーブルを作成します:

```bash
php ghost migrate
```

- テーブルに初期データを挿入します:

```bash
php ghost seed
```

### ■ CSVによるデータのインポート・エクスポート / CSV Import & Export

#### インポート（例：usersテーブル）

`Storage/csv` に `users.csv` を配置し、以下を実行:

```bash
php ghost importCSV users
```

#### エクスポート（例：postsテーブル）

```bash
php ghost exportCSV posts
```

### ■ メール設定
- Ghost PHP はデフォルトで SMTP によるメール送信に対応しています。
- 開発環境では smtp4dev を使用し、ローカルでメール送信をエミュレートする構成を推奨しています（事前セットアップが必要です）。
- .env に以下の設定を記述してください。

```
MB_LANGUAGE=Japanese
MB_INTERNAL_ENCODING=UTF-8

SMTP_HOST=localhost
SMTP_PORT=2525

SMTP_USER=
SMTP_PASS=

SMTP_AUTH=false
SMTP_SECURE=false

MAIL_FROM=test@example.com
```