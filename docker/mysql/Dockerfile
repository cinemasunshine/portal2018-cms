# MySQL用Dockerfile

# https://hub.docker.com/_/mysql/
FROM mysql:5.7

# volumesでマウントすると権限の問題で反映されない。
# [Warning] World-writable config file '/etc/mysql/conf.d/mysqld.cnf' is ignored.
COPY my.cnf /etc/mysql/conf.d
RUN chmod 644 /etc/mysql/conf.d/my.cnf
