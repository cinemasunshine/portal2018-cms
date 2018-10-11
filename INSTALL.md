# Install

## Webサーバ、DB、Storage

作成中。

[Wiki](https://m-p.backlog.jp/alias/wiki/508245)


## Application

アプリケーションのインストール方法です。

### １．composer

```sh
$ php composer install [--no-dev]
```

リポジトリにcomposer.lockがあるのでupdateコマンドではなくinstallコマンドを使います。

### ２．環境変数

パフォーマンスを考慮するならばサーバ等で設定します。

ローカル環境などパフォーマンスを気にしないのでであればルートディレクトリに *.env* ファイルを作成し、 *sample.env* ファイルを参考に設定します。

#### アプリケーション設定

Azure Web Appsのアプリケーション設定で設定する場合は **APPSETTING_** を省略します。

|名前|値|説明|
|:--|:--|:--|
|APPSETTING_ENV|'prod' or 'dev'|アプリケーションの実行環境|

#### 接続文字列

Azure Web Appsのアプリケーション設定で設定する場合は **MYSQLCONNSTR_** 等を省略します。

|名前|値|説明|
|:--|:--|:--|
|MYSQLCONNSTR_HOST|[host name]|MySQLのホスト名|
|MYSQLCONNSTR_PORT|[port]|MySQLのポート番号|
|MYSQLCONNSTR_NAME|[database name]|MySQLのデータベース名|
|MYSQLCONNSTR_USER|[user name]|MySQLのユーザ名|
|MYSQLCONNSTR_PASSWORD|[user password]|MySQLのユーザパスワード|
|MYSQLCONNSTR_SSL|'true' or 'false'|MySQLにSSL接続するか|
|CUSTOMCONNSTR_STORAGE_NAME|[storage name]|Azure Storage名|
|CUSTOMCONNSTR_STORAGE_KEY|[storage access key]|Azure Sotrageのアクセスキー|

### ３．.htaccess

ドキュメントルートに *.htaccess* を設置します。 *sample.htaccess* を参考にしてください。

Windowsサーバの場合は代わりに *Web.config* ファイルを設置します。
