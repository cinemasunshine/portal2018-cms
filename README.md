# cinemasunshine/portal2018-admin

シネマサンシャインポータルサイト2018 CMS管理画面

## 概要

ポータルサイト2018のCMS管理画面です。

## システム要件

- PHP: 7.2
- MySQL: 5.7

## Docker

ローカル環境としてDockerが利用できます。

※ [Docker](https://www.docker.com/)をインストールしてください。

※ 現状では開発環境としての利用のみを想定すてます。

※ AzureはWindowsサーバですが、こちらはLinuxサーバです。

※ StorageはAzureプラットフォームで別途作成してください。

web: http://localhost:8000/

phpmyadmin: http://localhost:8080/

### コマンド例

コンテナを作成し、起動する。

```sh
$ docker-compose up
```

# その他
## PHP CodeSniffer
### コードチェック

```sh
$ vendor/bin/phpcs --standard=phpcs.xml
```

### 自動修正

修正あれた箇所は適宜確認してください。

```sh
$ composer phpcs
```
