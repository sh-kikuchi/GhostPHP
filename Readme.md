# Ghost PHP

## 1. Overview（概要）

- **Ghost PHP** は、私が開発したプライベートな PHP フレームワークです。
- [ドキュメントはこちら / You can find the documentation here](https://sh-revue.net/projects/ghostphp)

## 2. Set up（セットアップ）

### プロジェクトのクローンと依存パッケージのインストール / Clone & Install

```bash
git clone https://github.com/sh-kikuchi/GhostPHP.git
cd GhostPHP
composer install
```

### データベース接続設定 / Connect Database

- プロジェクト直下に `.env` ファイルを作成し、以下のように設定してください:

```env
DB_HOST = 'localhost'
DB_NAME = 'test'
DB_USER = 'root'
DB_PASS = ''
PASSWORD = 'password'
```

### マイグレーションとシーディング / Migration & Seeding

- テーブルを作成します:

```bash
php ghost migrate
```

- テーブルに初期データを挿入します:

```bash
php ghost seed
```

### CSVによるデータのインポート・エクスポート / CSV Import & Export

#### インポート（例：usersテーブル）

`Storage/csv` に `users.csv` を配置し、以下を実行:

```bash
php ghost importCSV users
```

#### エクスポート（例：postsテーブル）

```bash
php ghost exportCSV posts
```

## 3. Architecture（アーキテクチャ）

### 三層構造 / 3-Tier Architecture

このフレームワークは以下の3層構造に基づいています：

- **Model（モデル）**: データベース処理とビジネスロジックを担当  
  - *Entity*: テーブルを表すクラス  
  - *Repository*: CRUD操作をカプセル化

- **Service（サービス）**: Model と View の橋渡しをするビジネスロジック層

- **View（ビュー）**: ユーザーインターフェースを担当

> 各レイヤーを独立して開発・テスト・保守可能にすることで、コードの可読性と保守性が向上します。

さらに、Model や Service にインターフェースを設けることで、型安全でクリーンなコードを書くことができます。

## 4. Directory（ディレクトリ構成）

```txt
├─aura
│  ├─database       // DB接続
│  ├─https          // リクエスト・レスポンス管理
│  ├─routes         // ルーティング
│  └─utils
│      ├─commands    // CLIコマンド処理
│      └─functions   // 主にView用関数
├─config             // 設定ファイル
├─form_classes       // フォームデータ管理クラス
├─interfaces         // 各種インターフェース
│  ├─form_classes
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

### Sample App（サンプルアプリ）

- ユーザーと投稿テーブルを持つ簡易なCRUDアプリケーションを用意しています。

## 5. Dependencies（依存ライブラリ）

- `guzzlehttp/guzzle` - HTTPリクエストを簡易化するライブラリ
- `vlucas/phpdotenv` - 環境変数を読み込むライブラリ
- `phpunit/phpunit` - PHPテストフレームワーク

## 6. Testing（テスト）

- PHPUnit を使用してテストを実行できます。例：

```bash
vendor/bin/phpunit tests/form_classes/PostRequestTest.php
```

---

🎉 Thank you to all my friends, PHPers!  
