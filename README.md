# COACHTECH(フリマサイト)※pro入会テスト

## 更新情報

- 2026年3月15日：取引チャット機能、相互評価システム、および自動メール通知機能を追加しました。

## 主要機能（2026年3月15日 追加分）

以下の機能を実装しました。

- **取引チャット機能**
  - 商品購入後、プロフィール（マイページ）から専用のチャット画面へ遷移可能。
  - テキストメッセージに加え、画像（.png / .jpeg）の投稿が可能。
  - 投稿済みのメッセージの編集・削除機能。
  - JavaScriptによる自動スクロール、入力下書き保存機能等のUI。
- **相互評価システム**
  - 取引完了後、購入者、出品者双方による取引相手の評価機能。
  - ユーザープロフィールでの平均評価点の表示。
- **通知機能**
  - 取引完了時、出品者へ完了を知らせる自動メール送信機能（mailhog）。

## 前提条件

本環境を構築するには、以下のツールが必要です。

- Git: 2.x 以上  
  確認方法: `git --version`
- Docker: 20.x 以上  
  確認方法: `docker --version`
- Docker Compose: 2.x 以上  
  確認方法: `docker compose version`
- Composer: 2.x 以上  
  確認方法: `composer -V`
- PHP: 8.1 以上（推奨 8.4.x）  
  確認方法: `php -v`

## 環境構築

**ディレクトリ構成**

```
assignment1
├── docker
│   ├── mysql
│   │   ├── data
│   │   └── my.cnf
│   ├── nginx
│   │   └── default.conf
│   └── php
│       ├── Dockerfile
│       └── php.ini
├── src
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/ ※出品画像、プロフィール画像、チャットで送信された画像を格納
│   └── test/
├── .env.example
├── .gitignore
├── README.md
└── docker-compose.yml

```

※src 以下のディレクトリは、主要なものに絞って記載しています。

**Docker ビルド**

1. ディレクトリ(assignment1)の作成
2. docker-compose.yml の作成
3. Nginx(default.conf) の設定
4. PHP(Dockerfile,php.ini) の設定
5. MySQL(my.cnf) の設定
6. phpMyAdmin(docker-compose.yml) の設定
7. DockerDesktop アプリを立ち上げる
8. `docker-compose up -d --build`でビルド

**Laravel 環境構築**

1. コンテナに入ります。

   ```bash
   docker compose exec php bash
   ```

2. 依存パッケージをインストールします。

   ```bash
   composer install
   ```

3. .env.example から新しい環境設定ファイルを作成します。

   ```bash
   cp .env.example .env
   ```

4. .env に以下の環境変数を追加

   **Stripeの設定について**

   本プロジェクトの購入機能にはStripeを使用しています。動作確認を行う際は、Stripeのダッシュボードから取得したテスト用APIキーを設定してください。キーが未設定の場合、購入処理時にエラーが発生します。

   ```text
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel_db
   DB_USERNAME=laravel_user
   DB_PASSWORD=laravel_pass
   (中略)
   STRIPE_KEY=your_public_key
   STRIPE_SECRET=your_secret_key
   ```

5. アプリケーションキーの作成

   ```bash
   php artisan key:generate
   ```

6. マイグレーションの実行

   ```bash
   php artisan migrate
   ```

7. シーディングの実行

   ```bash
   php artisan db:seed
   ```

8. ストレージリンクの作成

   アップロード画像を正しく参照するために以下を実行してください

   ```bash
    php artisan storage:link
   ```

## セットアップ後の確認方法

- `http://localhost/` にアクセスし、トップ画面が表示されることを確認してください。
- ログイン画面 (`http://localhost/login/`) や会員登録画面 (`http://localhost/register/`) が表示されることを確認してください。
- phpMyAdmin (`http://localhost:8080/`) に接続できることを確認してください。
- MailHog (`http://localhost:8025/`) にアクセスし、メール送信テストが確認できることを確認してください。

## テストユーザー（開発環境専用）

`php artisan db:seed` を実行すると、以下のテスト用データが生成されます。
仕様に基づき、出品状況の異なる3名のユーザーを用意しています。

1. **ユーザー1**
   - Email: `test@example.com`
   - Password: `abab1234`
   - 状態: 商品CO01~CO05（5品）を出品中。
2. **ユーザー2**
   - Email: `test2@example.com`
   - Password: `1234abab`
   - 状態: 商品CO6~CO10（5品）を出品中。「腕時計」の購入者。
3. **ユーザー3**
   - Email: `test3@example.com`
   - Password: `abcd4321`
   - 状態: 出品・購入なし。

**テスト実行方法**

1. `.env.testing.example` をコピーして `.env.testing` を作成します。

   ```bash
   cp .env.testing.example .env.testing
   ```

2. 必要な環境変数を .env.testing に設定してください。

   **＊注意**

   .env.testing の DB_DATABASE は、開発用（laravel_db）とは別の名前（例: demo_test）に設定し、あらかじめphpMyAdmin等でそのデータベースを作成しておいてください。

3. テスト用データベースをマイグレーションします.

   ```bash
   php artisan migrate --env=testing
   ```

4. テストを実行します。

   ```bash
   php artisan test
   ```

## よく使うコマンド

- コンテナに入る

  ```bash
   docker-compose exec php bash
  ```

- キャッシュクリア

  ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
  ```

- マイグレーションリセット & シーディング

  ```bash
    php artisan migrate:fresh --seed
  ```

## 使用技術(実行環境)

- git:2.50.1
- Docker:28.4.0
- Docker compose:2.39.2
- Composer:2.9.2
- PHP:8.4.12
- Laravel:8.83.29
- nginx:1.21.1
- MySQL:8.0.26

## ER 図（最終更新日2026年3月15日）

![alt](ER2.png)

## URL

- 開発環境(トップ画面)：http://localhost/
- 認証（ログイン画面）：http://localhost/login/
- 認証（会員登録画面）：http://localhost/register/
- phpMyAdmin ：http://localhost:8080/
- MailHog :http://localhost:8025/

## 補足情報

本プロジェクトでは、仕様書に明記されていない JavaScript を一部の Blade で使用しています。
用途は UI の補助的な動作に限定しており、以下の点に留意しています。

- 認証処理 (Fortify) やバリデーション (FormRequest) には利用していません。
- その他、仕様書で特定の技術が指定済みの箇所でも JavaScript は使用していません。

### 使用箇所

- `layout.blade.php` : ハンバーガーメニューのボタン開閉
- `detail.blade.php` : 「いいね」ボタン押下時の色・カウントの変更
- `purchase.blade.php` : 支払い方法選択時の購入確認欄の表示切替え
- `sell.blade.php` : 出品商品の画像アップロード時のプレビュー表示切替え
- `edit.blade.php` : プロフィール編集画面で画像アップロード時のプレビュー表示切替え
- `chat.blade.php` :（追加分）自動スクロール、入力内容の下書き保存、メッセージの編集モード切り替え、送信画像のプレビュー表示

### バリデーションとファイル形式の制御について

要件シート記載のとおり、メッセージ投稿および画像アップロードに対し、`FormRequest`（ChatRequest）を用いたバリデーションを実装しています。

- **画像形式の制限**:
  Blade側（フロントエンド）にて `accept="image/png, image/jpeg"` を指定し、ファイル選択時に指定外の形式（PDF等）が表示されないようあらかじめ制御しています。
- **バックエンドでの整合性確保**:
  万が一、フロントエンドの制限を回避して指定外のファイルが送信された場合でも、サーバー側の `FormRequest` バリデーションにより、指定どおりのエラーメッセージが表示され処理が中断されることを確認済みです。
