/*

user ****** pass ******
//database mewplog
use mewplogdb

//table mewplog //could not having status cause errors?? who knows
create table mewplog (id int(6) primary key auto_increment, asset varchar(5), description varchar(50), name varchar(50), clockno varchar(5), jobno varchar(40), timeout datetime, timein timestamp);
describe mewplog;
+-------------+-------------+------+-----+---------------------+-------------------------------+
| Field       | Type        | Null | Key | Default             | Extra                         |
+-------------+-------------+------+-----+---------------------+-------------------------------+
| id          | int(6)      | NO   | PRI | NULL                | auto_increment                |
| asset       | varchar(5)  | YES  |     | NULL                |                               |
| description | varchar(50) | YES  |     | NULL                |                               |
| name        | varchar(50) | YES  |     | NULL                |                               |
| clockno     | varchar(5)  | YES  |     | NULL                |                               |
| timeout     | datetime    | YES  |     | NULL                |                               |
| timein      | timestamp   | NO   |     | current_timestamp() | on update current_timestamp() |
+-------------+-------------+------+-----+---------------------+-------------------------------+


//table assets
//create table assets (id int(6) primary key auto_increment, asset varchar(5), description varchar(50));
create table assets (asset varchar(5) primary key, description varchar(50), name varchar(50), clockno varchar(5), jobno varchar(40), status tinyint(1), timeout timestamp);

describe assets;
+-------------+-------------+------+-----+---------------------+-------------------------------+
| Field       | Type        | Null | Key | Default             | Extra                         |
+-------------+-------------+------+-----+---------------------+-------------------------------+
| asset       | varchar(5)  | NO   | PRI | NULL                |                               |
| description | varchar(50) | YES  |     | NULL                |                               |
| name        | varchar(50) | YES  |     | NULL                |                               |
| clockno     | varchar(5)  | YES  |     | NULL                |                               |
| status      | tinyint(1)  | YES  |     | NULL                |                               |
| timeout     | timestamp   | NO   |     | current_timestamp() | on update current_timestamp() |
+-------------+-------------+------+-----+---------------------+-------------------------------+

//table comments

create table comments (id int(6) primary key auto_increment, asset varchar(5), status varchar(20), statusdate timestamp, defect varchar(500));
describe comments;
+------------+--------------+------+-----+---------------------+-------------------------------+
| Field      | Type         | Null | Key | Default             | Extra                         |
+------------+--------------+------+-----+---------------------+-------------------------------+
| id         | int(6)       | NO   | PRI | NULL                | auto_increment                |
| asset      | varchar(5)   | YES  |     | NULL                |                               |
| status     | varchar(20)  | YES  |     | NULL                |                               |
| statusdate | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
| defect     | varchar(500) | YES  |     | NULL                |                               |
+------------+--------------+------+-----+---------------------+-------------------------------+

insert into comments (asset, status, defect) values ('M7449', 'Waiting Action', 'test comment, here is extra padding for length');

*/
