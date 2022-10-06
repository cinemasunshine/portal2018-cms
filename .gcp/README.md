# Google Cloud Platform

## 環境変数

YAML形式のデータをBase64エンコードしてCircleCIの環境変数として設定する。それをデプロイ時にファイルに書き込む。

### Base64 encode

```
base64 -w 0 env.example.yaml > base64.txt
```

[複数行にわたる環境変数のエンコード
](https://circleci.com/docs/ja/set-environment-variable#encoding-multi-line-environment-variables)
