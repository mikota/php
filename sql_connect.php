<?php
	$hostname = "nullbox.io";
	$username = "root";
	$password = "nullbox12";
	$dbname = "nullbox_grad";

	$conn= new mysqli($hostname,$username,$password,$dbname);

	if ($conn->connect_error){
		die("Connection failed: ".$conn->connect_error);
	}
?>