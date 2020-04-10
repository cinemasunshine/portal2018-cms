# Install

アプリケーションのインストールについてです。

データベース等の各サービスを用意したうえでの手順を記載しています。

各サービスの構築についてはwikiを参照してください。

[wiki](https://m-p.backlog.jp/alias/wiki/508245)

## 手順

### １．データベース

データベース作成、ユーザ作成を行います。

手順はwikiを参照してください。

https://m-p.backlog.jp/alias/wiki/568643

※ テーブル作成、マスターデータ登録は後ほど実施します。

### ２．ストレージ

Azure Storageについてです。次のBlob Containerを作成します。

- editor （公開アクセスレベル：public read access for blobs only）
- file （公開アクセスレベル：public read access for blobs only）
- admin-log

### ３．composer

composerコマンドで依存ライブラリをインストールします。

※ composerがインストールされている必要があります。

[Download Composer](https://getcomposer.org/download/)

```sh
$ php composer install [--no-dev] [-o|--optimize-autoloader]
```

※ リポジトリにcomposer.lockがあるのでupdateコマンドではなくinstallコマンドを使います。

※ 運用環境ではno-dev、optimize-autoloaderオプションを推奨。

### ４．環境変数

ルートディレクトリに *.env* ファイルを作成し、 *.env.example* ファイルを参考に設定します。

※ パフォーマンスを考慮するならば.envファイルは作成せず、サーバ等で設定します。

#### アプリケーション設定

Azure Web Appsのアプリケーション設定で設定する場合はプレフィックス（ **APPSETTING_** ）を省略します。

|名前|値|必須|説明|
|:--|:--|:--|:--|
|APPSETTING_ENV|*String*|○|アプリケーションの実行環境|
|APPSETTING_DEBUG|*Boolean*|-|デバッグ設定（デフォルト： false）|

#### 接続文字列

Azure Web Appsのアプリケーション設定で設定する場合はプレフィックス（ **MYSQLCONNSTR_** 等）を省略します。

|名前|値|必須|説明|
|:--|:--|:--|:--|
|MYSQLCONNSTR_HOST|*String*|○|MySQLのホスト名|
|MYSQLCONNSTR_PORT|*Integer*|○|MySQLのポート番号|
|MYSQLCONNSTR_NAME|*String*|○|MySQLのデータベース名|
|MYSQLCONNSTR_USER|*String*|○|MySQLのユーザ名|
|MYSQLCONNSTR_PASSWORD|*String*|○|MySQLのユーザパスワード|
|MYSQLCONNSTR_SSL|*Boolean*|○|MySQLにSSL接続するか|
|CUSTOMCONNSTR_STORAGE_SECURE|*Boolean*|-|HTTPS接続するか。デフォルト: true|
|CUSTOMCONNSTR_STORAGE_NAME|*String*|○|Azure Storage名|
|CUSTOMCONNSTR_STORAGE_KEY|*String*|○|Azure Sotrageのアクセスキー|
|CUSTOMCONNSTR_STORAGE_BLOB_ENDPOINT|*String*|-|Blob エンドポイント|
|CUSTOMCONNSTR_STORAGE_PUBLIC_ENDOPOINT|*String*|-|パブリック アクセス エンドポイント|

### ５．Doctrine

#### Schema生成

データベースにテーブルを生成します。

すでに作成済みのデータベースに接続する場合は不要です。

```sh
$ vendor/bin/doctrine orm:schema-tool:create
```

#### Proxy生成

開発環境**以外**は手動で生成が必要です。

```sh
$ vendor/bin/doctrine orm:generate-proxies
```

### ６．マスターデータ登録

[wiki](https://m-p.backlog.jp/alias/wiki/568643)

#### 管理ユーザのパスワード

コンソールコマンドから任意のパスワードを暗号化し、
マスターデータSQLのadmin_user.passwordに設定します。

```sh
$ bin/console admin-user:encrypt-psw <password>
```

### ７．その他

#### .htaccess または Web.config
ドキュメントルートに *.htaccess* もしくは *Web.config* を設置します。

内容はサンプルを参考にしてください。

#### PHP設定

sample.user.ini を参考に必要な設定を行います。

※ 直接 php.ini を編集するなど方法は環境によって適宜選択してくだい。
