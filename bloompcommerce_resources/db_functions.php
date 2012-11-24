<?php

function db_create_tables(){
	
	$sql_create_table = 'app/code/community/Bloompa/BloompCommerce/sql/install.sql';	

	echo "\n-------------------\n";
	fwrite(STDOUT, "Insert your BloompCommerce Token: ");
	$token = fgets(STDIN);

	echo "\n-------------------\n";
	echo "Database settings";
	echo "\n-------------------\n";

	fwrite(STDOUT, "Database Name: ");
	$db = fgets(STDIN);

	fwrite(STDOUT, "Database Host: ");
	$host = fgets(STDIN);

	fwrite(STDOUT, "Database User: ");
	$user = fgets(STDIN);

	fwrite(STDOUT, "Database Password: ");
	$pass = fgets(STDIN);

	// database connection
	$mysqli = new mysqli(trim($host), trim($user), trim($pass),trim($db));
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	// Query settings
	$handle = fopen($sql_create_table,'r');
	$query = fread($handle,filesize($sql_create_table));	
	$query = str_replace('[param]','token',$query);
	$query = str_replace('[value]',trim($token),$query);

	if($mysqli->multi_query($query)){
		echo "\nDatabase table successful created!\n";
	}else{
		echo "\nFAIL to create the database table!\n";
	}

	$mysqli->close();
}


function db_delete_tables(){
	
	$sql_delete_table = 'app/code/community/Bloompa/BloompCommerce/sql/uninstall.sql';

	echo "\n-------------------\n";
	echo "Database settings";
	echo "\n-------------------\n";

	echo "* This scripts ONLY removes the tables created by the installation.\n";

	fwrite(STDOUT, "Database Name: ");
	$db = fgets(STDIN);

	fwrite(STDOUT, "Database Host: ");
	$host = fgets(STDIN);

	fwrite(STDOUT, "Database User: ");
	$user = fgets(STDIN);

	fwrite(STDOUT, "Database Password: ");
	$pass = fgets(STDIN);

	// database connection
	$mysqli = new mysqli(trim($host), trim($user), trim($pass),trim($db));
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();	
	}	

	// Query settings
	$handle = fopen($sql_delete_table,'r');
	$query = fread($handle,filesize($sql_delete_table));	
	if($mysqli->multi_query($query)){ echo "\nDatabase tables successful removed!\n";}
	else{echo "\nFAIL to remove the database table!\n";}
	$mysqli->close();
}