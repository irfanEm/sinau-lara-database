create database sinau_lara_database;

use sinau_lara_database;

CREATE TABLE categories
(
    id varchar(100) NOT NULL PRIMARY KEY,
    name varchar(100) NOT NULL,
    description text,
    created_at TIMESTAMP
) engine = innodb;

DESCRIBE categories;