array(
'create table demo_model(id int not null auto_increment primary key, title varchar(255) not null default "") default charset utf8',
'create table modeldemonode (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand6474 */;',
'alter table modeldemonode add column body text   default ""  /* rand9947 */;',
'alter table modeldemonode add column title varchar(255)  not null   default ""  /* rand7236 */;',
'create table demoterm (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand5893 */;',
'alter table demotermtonode add column term_id int  not null    /* rand28764 */;',
'alter table demotermtonode add column node_id int  not null    /* rand464119 */;',
'alter table demotermtonode add foreign key(term_id) references demoterm(id);',
'alter table demotermtonode add foreign key(node_id) references modeldemonode(id);',
)
