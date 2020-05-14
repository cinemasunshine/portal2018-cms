@echo off

cd D:\home\site\wwwroot

php bin/console cache:clear:view

@rem WinCacheのキャッシュはWebとCLIが別になっていて、コンソールからはクリアできないらしい
@rem vendor\bin\doctrine orm:clear-cache:query --flush
@rem vendor\bin\doctrine orm:clear-cache:metadata --flush
