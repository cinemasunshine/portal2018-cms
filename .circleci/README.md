# CircleCI

[ドキュメント](https://circleci.com/docs/ja/)

## Contexts

cinemasunshine (Organizations) > Organization Settings > Contexts

### Docker Hub

| Name | Value |
|:---|:---|
|DOCKERHUB_ID |Docker Hub ユーザ |
|DOCKERHUB_ACCESS_TOKEN |Docker Hub アクセストークン |

## Environment Variables

cinemasunshine (Organizations) > portal2018-cms (Projects) > Project Settings > Environment Variables

### デプロイ

| Name | Value |
|:---|:---|
| ENV_VARIABLES_**\<ENVIRONMENT\>** | 環境変数を設定したyaml形式データをBase64エンコード |
| GCLOUD_SERVICE_KEY_**\<ENVIRONMENT\>** | Googleプロジェクトのフルサービス・キーJSONファイル |
| GOOGLE_COMPUTE_REGION_**\<ENVIRONMENT\>** | gcloud CLI のデフォルトとして設定する Google compute region |
| GOOGLE_PROJECT_ID_**\<ENVIRONMENT\>** | gcloud CLIのデフォルトとして設定するGoogleプロジェクトID |
