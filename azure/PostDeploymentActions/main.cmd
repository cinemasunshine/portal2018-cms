@echo off

cd D:\home\site\wwwroot

php bin/console cache:clear:view

vendor\bin\doctrine orm:clear-cache:query --flush
vendor\bin\doctrine orm:clear-cache:metadata --flush
