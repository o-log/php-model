array(
'create table demo_model(id int not null auto_increment primary key, title varchar(255) not null default "") default charset utf8',
'alter table demo_model add column test int  not null   default 0;',
'alter table demo_model add column test_varchar varchar(255)  not null   default "";',
'alter table demo_model add column text_nullable text  ;',
'alter table demo_model add column widget_name varchar(100)  not null   default "empty";',
'alter table demo_model add column dud int  not null   default 0;',
'create table modeldemonode (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand6474 */;',
'alter table modeldemonode add column body text   default ""  /* rand9947 */;',
'alter table modeldemonode add column title varchar(255)  not null   default ""  /* rand7236 */;',
)
