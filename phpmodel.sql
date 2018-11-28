create table phpmodeldemo_demomodel (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand9097 */;
alter table phpmodeldemo_demomodel add column title varchar(255)   not null    /* rand930670 */;
create table tests_testmodel (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* sdfgsdf */;
alter table tests_testmodel add column title varchar(255)   not null    /* gsdkfhg */;
alter table tests_testmodel add column disable_delete int   not null    /* kjhgksdfg */;
alter table tests_testmodel add column throw_exception_after_delete int   not null    /* klhsbdfg */;
alter table tests_testmodel add column after_save_counter int   not null    /* iushdgg */;
create table tests_loadtestmodel (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* kjhgsdfg */;
alter table tests_loadtestmodel add column title varchar(255)   not null    /* sgrgsdfg */;
alter table tests_loadtestmodel add column extra_field int default 0   not null    /* gsdfgg */;
create table modeltestnode (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* u67u567uu */;
alter table modeltestnode add column title varchar(255) default ""   not null    /* 24t5we */;
alter table modeltestnode add column body varchar(255) default ""  not null    /* t65y6y456y */;
create table testterm (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* 6i73wetrtgew */;
create table testtermtonode (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* sbgrtbgsdfg */;
alter table testtermtonode add column term_id int not null    /* wefwerg */;
alter table testtermtonode add column node_id int not null    /* vssfgssd */;
alter table phpmodeldemo_demomodel add column bool_val tinyint   not null    /* rand503190 */;
create table phpmodeldemo_demomodel2 (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand1002 */;
alter table phpmodeldemo_demomodel add index INDEX_bool_val_14421302 (bool_val, created_at_ts)  /* rand456489 */;