FROM mysql:8.0.26

ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_USER=drone123
ENV MYSQL_PASSWORD=1234

ADD mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
ADD init.sql /docker-entrypoint-initdb.d/
ADD privileges.sql /docker-entrypoint-initdb.d/

EXPOSE 3306

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["mysqld"]


