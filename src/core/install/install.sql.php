<?php
use Gila\TableSchema;
use Gila\DB;

require_once 'src/core/classes/TableSchema.php';

TableSchema::update(include 'src/core/tables/post.php');

DB::query('ALTER TABLE post ADD  FULLTEXT KEY `title` (`title`,`post`);');

TableSchema::update(include 'src/core/tables/postcategory.php');

TableSchema::update(include 'src/core/tables/page.php');

TableSchema::update(include 'src/core/tables/user.php');

TableSchema::update(include 'src/core/tables/user_notification.php');

TableSchema::update(include 'src/core/tables/sessions.php');

TableSchema::update(include 'src/core/tables/widget.php');

TableSchema::update(include 'src/core/tables/menu.php');

TableSchema::update(include 'src/core/tables/tableschema.php');

TableSchema::update(include 'src/core/tables/event_log.php');

TableSchema::update(include 'src/core/tables/redirect.php');

DB::query('CREATE TABLE IF NOT EXISTS `option` (
  `option` varchar(80) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

TableSchema::update(include 'src/core/tables/usergroup.php');

TableSchema::update(include 'src/core/tables/userrole.php');

$_user = $_POST['adm_user'];
$_email = $_POST['adm_email'];
$_pass = password_hash($_POST['adm_pass'], PASSWORD_BCRYPT);

DB::query("REPLACE INTO userrole(id,userrole,`level`) VALUES(1,'Admin',10);");
DB::query("INSERT INTO user(id,username,email,pass,active)
  VALUES(1,?,?,?,1);", [$_user,$_email,$_pass]);
DB::query("INSERT INTO usermeta VALUES(1,1,'role',1);");
DB::query("INSERT INTO post(id,`user_id`,title,slug,`description`,post,publish,`language`)
  VALUES(1,1,'Hello World!','hello_world','This is the first post','This is the first post. You can edit it from administration',1,'en');");
DB::query("INSERT INTO page(id,title,slug,blocks,publish,template,`language`)
  VALUES(1,'About','about','[{\"_type\":\"text\",\"text\":\"This is a page to describe your website. You can edit this text in page editor\"}]',1,'','en');");

// preinstall widgets on dashboard
$wtext1 = '{"text":"<ol><li><a href=\\\"admin\\\/content\\\/postcategory\\\">Create Categories<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/page\\\">Edit About Page<\\\/a><\\\/li><li><a href=\\\"admin\\\/content\\\/post\\\">Add Posts<\\\/a><\\\/li><li><a href=\\\"admin\\\/settings\\\">Set Basic Settings<\\\/a><\\\/li><\\\/ol>"}';
$wtext2 = '{"text":"<ul><li><a href=\\\\\"https:\\\/\\\/gila-cms.readthedocs.io\\\" target=\\\"_blank\\\">Documentation<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/www.facebook.com\\\/gilacms\\\/\\\\\" target=\\\\\"_blank\\\\\">Facebook Page<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/github.com\\\/GilaCMS\\\/gila\\\\\" target=\\\\\"_blank\\\\\">Github Repo<\\\/a><\\\/li><li><a href=\\\\\"https:\\\/\\\/tinyletter.com\\\/gilacms\\\\\">Dev Newsletter<\\\/a><\\\/li><\\\/ul>"}';
$wtext3 = '{"text":"<p>We want to hear from you!<br>Send us your questions and thoughts at <a href=\\\"mailto:contact@gilacms.com\\\">contact@gilacms.com<\\\/a><\\\/p>"}';

DB::query("INSERT INTO widget(id,widget,title,area,active,pos,data)
VALUES(1,'core-counters','','dashboard',1,1,'[]'),
(2,'paragraph','Start Blogging','dashboard',1,2,'".$wtext1."'),
(3,'paragraph','Links','dashboard',1,3,'".$wtext2."'),
(4,'paragraph','Feedback','dashboard',1,4,'".$wtext3."');");
