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

CREATE TABLE counters
(
    id varchar(100) NOT NULL PRIMARY key,
    counter int NOT NULL default 0
) engine innodb;

insert into counters(id, counter) values('sample', 0);

select * from counters;

create table products(
    id varchar(100) not null primary key,
    name varchar(100) not null,
    description text null,
    price int not null,
    category_id varchar(100) not null,
    created_at timestamp not null default current_timestamp,
    constraint fk_category_id foreign key(category_id) references categories(id)
) engine innodb;

select * from products;