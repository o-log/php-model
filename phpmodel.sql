array(
'create table demo_model(id int not null auto_increment primary key, title varchar(255) not null default "") default charset utf8',
'create table modeldemonode (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand6474 */;',
'alter table modeldemonode add column body text   not null  /* rand9947 */;',
'alter table modeldemonode add column title varchar(255)  not null   default ""  /* rand7236 */;',
'create table demoterm (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand5893 */;',
'create table demotermtonode  (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand5893 */;',
'alter table demotermtonode add column term_id int  not null    /* rand28764 */;',
'alter table demotermtonode add column node_id int  not null    /* rand464119 */;',
'alter table demotermtonode add foreign key(term_id) references demoterm(id);',
'alter table demotermtonode add foreign key(node_id) references modeldemonode(id);',
'alter table modeldemonode add column term_id int  not null    /* rand592832 */;',
'alter table modeldemonode drop column term_id /* rand59283255 */;',
'alter table modeldemonode add column term_id int  /* rand59283243 */;',
'alter table modeldemonode add constraint FK_term_id_239910 foreign key (term_id)  references demoterm (id)  on delete cascade /* rand434915 */;',
'create table tests_testmodel (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand3534 */;',
'alter table tests_testmodel add column title varchar(255)  not null   default ""  /* rand919378 */;',
'alter table tests_testmodel add column disable_delete int  not null   default 0  /* rand943732 */;',
'alter table tests_testmodel add column throw_exception_after_delete int  not null   default 0  /* rand631526 */;',
'create table modeltestnode (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand6474 */;',
'alter table modeltestnode add column body text   not null  /* rand9947 */;',
'alter table modeltestnode add column title varchar(255)  not null   default ""  /* rand7236 */;',
'create table testtermtonode  (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand5893 */;',
'alter table testtermtonode add column term_id int  not null    /* rand28764 */;',
'alter table testtermtonode add column node_id int  not null    /* rand464119 */;',
'alter table testtermtonode add foreign key(term_id) references demoterm(id);',
'alter table testtermtonode add foreign key(node_id) references modeltestnode(id);',
'create table testterm (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand5893 */;',
'alter table tests_testmodel add column after_save_counter int  not null   default 0  /* rand334647 */;',
'create table phpmodeldemo_consttest (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand4311 */;',
'alter table phpmodeldemo_consttest add column title varchar(255)  not null    /* rand54733 */;',
'alter table phpmodeldemo_consttest add column body text    /* rand767091 */;',
'alter table phpmodeldemo_consttest add column weight int  not null   default 0  /* rand76196 */;',
'alter table demo_model add column weight int  not null    /* rand209183 */;',
'alter table demo_model change weight weight int not null default 0   /* rand209183 */;',
'alter table demo_model add column collate_test varchar(255)   not null    /* rand203401 */;',
'alter table demo_model add column collate_test_2 varchar(255)  collate utf8_bin  not null    /* rand546130 */;',
'alter table demo_model add column collate_test_3 int   not null    /* rand174880 */;',
'alter table demo_model add column demo_node_id int   not null    /* rand518678 */;',
'alter table demo_model add constraint FK_demo_node_id_978168 foreign key (demo_node_id)  references modeldemonode (id) /* rand636538 */;',
'create table phpmodeldemo_somemodel (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand3279 */;',
'alter table testtermtonode drop foreign key testtermtonode_ibfk_1 /* rand2342342 */;',
'alter table testtermtonode add constraint testtermtonode_ibfk_1 foreign key (term_id) references testterm (id) /* rand636123123538 */;',
'create table tests_loadtestmodel (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand7303 */;',
'alter table tests_loadtestmodel add column extra_field int     /* rand58321 */;',
'alter table tests_loadtestmodel add column title varchar(255)     /* rand34340 */;',
'alter table phpmodeldemo_consttest add column test_int int     /* rand269034 */;',
'create table phpmodeldemo_test2 (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand2267 */;',
'create table phpmodeldemo_test3 (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand7542 */;',
'alter table phpmodeldemo_test3 add column title varchar(255)   not null    /* rand498948 */;',
)
