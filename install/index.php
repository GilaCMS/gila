<?php

if($_SERVER['REQUEST_METHOD'] === 'POST') {
	$host=$_POST['db_host'];$db_user=$_POST['db_user'];
	$db_pass=$_POST['db_pass'];$db_name=$_POST['db_name'];
	$link = mysqli_connect($host,$db_user,$db_pass,$db_name);
	if (!$link) {
		echo "<div class='alert'><span class='closebtn' onclick='this.parentElement.style.display=\"none\";'>&times;</span>Error: Unable to connect to MySQL.".PHP_EOL;
		echo "<br>#".mysqli_connect_errno().PHP_EOL." : ".mysqli_connect_error().PHP_EOL."</div>";
	} else if ($link->query("SHOW TABLES LIKE 'user';")) {
		echo "<div class='alert'>Error: Table `user` already exists</div>";
	} else {
		$link->query('CREATE TABLE IF NOT EXISTS user(id int(11) NOT NULL auto_increment,name varchar(80),email varchar(80),pass varchar(60),PRIMARY KEY (id));');
		$link->query('CREATE TABLE IF NOT EXISTS user_meta(id int(11) NOT NULL auto_increment,user_id int(11),vartype varchar(25),value varchar(80),PRIMARY KEY (id));');
		$_user=$_POST['adm_user'];
		$_email=$_POST['adm_email'];
		$_pass=$_POST['adm_pass'];
		$link->query("INSERT INTO user VALUES(1,'$_user','$_email','$_pass');");
		$link->query("INSERT INTO user_meta VALUES(1,1,'privilege','admin');");
		echo "<div class='alert success'>Installation executed successfully!</div>";
		echo "<div class='alert success'>Installation executed successfully!</div>";
	}
}
?>

<head>
	<link type="text/css" rel="stylesheet" href="../libs/rj.css"/>
	<script src="../libs/rj.js"></script>
</head>
<!--div class="col-5"></div-->
<div class="col-6 display-flex margin-auto align-items-center " style="height:100%">
<form method='post' class="col-12 border box-shadow">
	<div class="col-6">
	<label>Hostname</label>
	<input name="db_host" value="localhost"/>
	<label>Database</label>
	<input name="db_name"/>
	<label>DB Username</label>
	<input name="db_user"/>
	<label>DB Password</label>
	<input name="db_pass" type="password"/>
	</div>
	<div class="col-6">
	<label>Admin Username</label>
	<input name="adm_user"/>
	<label>Admin Email</label>
	<input name="adm_email" type="email"/>
	<label>Admin Password</label>
	<input name="adm_pass" type="password"/>
	<label>Admin Password</label>
	<input name="adm_pass" type="password"/>
	</div>
	<div class="col-12"><input class="btn" type="submit"/></div>
</form>
</div>
<!--div class="col-5"></div-->
