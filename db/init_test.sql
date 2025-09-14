CREATE DATABASE IF NOT EXISTS myapp_test;
GRANT ALL PRIVILEGES ON myapp_test.* TO 'myuser'@'%';
FLUSH PRIVILEGES;

USE myapp_test;

SOURCE /docker-entrypoint-initdb.d/schema.sql;
