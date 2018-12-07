<?php
http_response_code(503);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>システムメンテナンスのお知らせ - CMS管理画面</title>
    
    <link rel="stylesheet" href="/css/vendor/coreui.min.css">
    
</head>
<body class="app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h4 class="pt-3">システムメンテナンスのお知らせ</h4>
                <p>
                    只今、システムメンテナンスを行っております。<br>
                    大変ご迷惑おかけしますが、何卒ご理解いただけますようお願い申し上げます。
                </p>
                <p>終了時刻：○○時予定</p>
            </div>
        </div>
    </div>
    
    <script src="/js/vendor/jquery-3.3.1.min.js"></script>
    <script src="/js/vendor/popper.min.js"></script>
    <script src="/js/vendor/bootstrap.min.js"></script>
    <script src="/js/vendor/coreui.min.js"></script>
</body>
</html>
