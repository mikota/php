<?php
	$hostname = "nullbox.io";
	$username = "nullbox_grad";
	$password = "nullboxgrad";
	$dbname = "nullbox_grad";

	$conn= mysqli_connect($hostname,$username,$password,$dbname) or die(mysqli_error($con));
	$conn->set_charset("utf8");

?>