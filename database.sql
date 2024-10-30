CREATE DATABASE IF NOT EXISTS symfony_master;
USE symfony_master;

CREATE TABLE IF NOT EXISTS user(
id          int(255) auto_increment not null,
role        varchar(50),
name        varchar(100),
surname     varchar(200),
email       varchar(255),
password    varchar(255),
created_at  datetime,
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

INSERT INTO user VALUES (NULL, 'USER', 'Vicente', 'Corts', 'v@gmail.com', 'password', CURTIME() );
INSERT INTO user VALUES (NULL, 'USER', 'Maria', 'Gomollón', 'm@gmail.com', 'password', CURTIME() );
INSERT INTO user VALUES (NULL, 'USER', 'AA', 'AA', 'a@gmail.com', 'password', CURTIME() );

CREATE TABLE IF NOT EXISTS task(
id          int(255) auto_increment not null,
user_id     int(255) not null,
title       varchar(255),
content     text,
priority    varchar(20),
hours       int(100),
created_at  datetime,
CONSTRAINT pk_tasks PRIMARY KEY(id),
CONSTRAINT fk_task_user FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;

INSERT INTO task VALUES (NULL, 1, 'Tarea 1', 'Contenido de prueba 1', 'High',  40 , CURTIME() );
INSERT INTO task VALUES (NULL, 1, 'Tarea 2', 'Contenido de prueba 2', 'Low',  20 , CURTIME() );
INSERT INTO task VALUES (NULL, 2, 'Tarea 3', 'Contenido de prueba 3', 'Medium',  10 , CURTIME() );
INSERT INTO task VALUES (NULL, 3, 'Tarea 4', 'Contenido de prueba 4', 'High',  50 , CURTIME() );
