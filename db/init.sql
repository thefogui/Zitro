CREATE DATABASE IF NOT EXISTS myapp;
USE myapp;

SOURCE /docker-entrypoint-initdb.d/schema.sql;
