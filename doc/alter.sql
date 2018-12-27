CREATE DATABASE lumina;

USE lumina;

CREATE TABLE users(
id int not null auto_increment ,
username varchar(100) NULL,
password varchar(255) NULL,
email varchar(200) NULL,
PRIMARY KEY(id)
);

CREATE TABLE categories(
id int not null auto_increment,
name varchar(100) NULL,
PRIMARY KEY(id)
);

CREATE TABLE questions(
id int not null auto_increment,
categories_id int not NULL,
users_id int not NULL,
question varchar(100) NULL,
PRIMARY KEY(id)
);

CREATE TABLE answers(
id int not null auto_increment,
users_id varchar(100) NULL,
questions_id varchar(100) NULL,
answer varchar(100) NULL,
PRIMARY KEY(id)
);