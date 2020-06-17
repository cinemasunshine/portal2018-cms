# cinemasunshine/portal2018-cms

シネマサンシャイン ポータルサイト2018 CMS管理画面

## 概要

2018年リリースのポータルサイトCMS管理画面です。

## システム要件

- PHP: 7.2
- MySQL: 5.7
- Azure App Service (Windows)
- Azure Blob Storage

## EditorConfig

[EditorConfig](https://editorconfig.org/) でコーディングスタイルを定義しています。

利用しているエディタやIDEにプラグインをインストールしてください。

[Download a Plugin](https://editorconfig.org/#download)

## CircleCI

CIツールとして [CircleCI](https://circleci.com) を導入してます。

## Docker

ローカル環境としてDockerが利用できます。

※ [Docker](https://www.docker.com/)をインストールしてください。

※ 現状では開発環境としての利用のみを想定してます。

※ AzureはWindowsサーバですが、こちらはLinuxサーバです。

※ Storageエミュレーターはpreview版です。必要に応じてAzureプラットフォームで別途作成してください。

web: http://localhost:8000/

phpmyadmin: http://localhost:8080/

### docker-compose コマンド例

コンテナを作成し、起動する。

```sh
$ docker-compose up
```

## アプリケーション コンソール コマンド

```sh
$ php bin/concole help
```

## その他 コマンド

### PHP Lint

```sh
$ composer phplint
```

### PHP CodeSniffer

```sh
$ composer phpcs
```

### PHPStan

```sh
$ composer phpstan
```

### PHPUnit

```sh
$ composer phpunit
```
