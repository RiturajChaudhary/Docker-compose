FROM mysql:8.0

ENV MYSQL_ROOT_PASSWORD=rootpassword
ENV MYSQL_DATABASE=crudapp
ENV MYSQL_USER=cruduser
ENV MYSQL_PASSWORD=crudpassword

COPY init.sql /docker-entrypoint-initdb.d/init.sql
