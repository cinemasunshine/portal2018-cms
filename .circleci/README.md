# CircleCI

[ドキュメント](https://circleci.com/docs/ja/)

## Environment Variables

Settings > cinemasunshine > portal2018 > Environment Variables

### Composer

| Name | Value |
|:---|:---|
|BACKLOG_USER |backlogのユーザ（プロジェクトSASAKIの権限が必要） |
|BACKLOG_PASSWORD |backlogユーザのパスワード |

### コード解析

| Name | Value |
|:---|:---|
|CI_DOTENV |.envファイルの設定をbase64エンコードした文字列 |

#### Windowsでのbase64エンコード

```dosbatch
> certutil -f -encode [input file] [output file]
```

上記のコマンドで出力されたファイルを開き、編集する。

- ヘッダー「-----BEGIN CERTIFICATE-----」削除
- フッター「-----END CERTIFICATE-----」削除
- 改行を除く（長いと一定文字数で改行が入る）

編集した１行の文字列をValueに設定する。

### デプロイ

| Name | Value |
|:---|:---|
|DEV_AAS_USER |開発環境デプロイユーザ |
|DEV_AAS_PASSWORD |開発環境デプロイユーザのパスワード |
|TEST_AAS_USER |開発環境デプロイユーザ |
|TEST_AAS_PASSWORD |開発環境デプロイユーザのパスワード |
|PROD_AAS_USER |運用環境デプロイユーザ |
|PROD_AAS_PASSWORD |運用環境デプロイユーザのパスワード |

デプロイユーザとパスワードはAzure App Serviceのプロパティ > デプロイの開始URL
