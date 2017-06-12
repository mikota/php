<?php
	include 'sql_connect.php';
	include 'init_datetime.php';

	$prices = array[1:100,2:200,3:500];

	class Igraliste {
		var $num,$price;

		function __construct(){
			$this->price = global $prices[$this->num];
		}
	}

	class Reservation {
		var $id,$grupa_id,$dt_start,$dt_end,$dt_created,$duration_mins,$confirmed,$igraliste;
		//taken directly from db

		var $active;

		function __construct(){
			$this->dt_start = new DateTime;
			$this->dt_end = new DateTime;
		}

		function isActive(){
			$this->active = ($confirmed and isActiveTime(global $dt_now));
			return $this->active;
		}

		function isActiveTime($_dt_now){
			return ($_dt_now > $this->dt_start and $_dt_now < $this->dt_end);
		}

		function timeLeftBeforeStart($_dt_now){ //mins
			$interval = $_dt_now->diff($this->dt_start);
			return intval($interval->format('m'));
		}

		function isTrumpable(){ //ako nisu potvrdili do 5 min prije neko drugi more rezervirat
			if ($this->timeLeftBeforeStart() <= 5 and $this->confirmed == 0){
				return True;
			} else {
				return False;
			}
		}

		function setDbValues(){
			$vals = "('{$this->id}','{$this->grupa_id}','{$this->dt_start}','{$this->dt_end}','{$this->dt_created}','{$this->duration_mins},'{$this->confirmed}','{$this->igraliste}')";
			$sql = "INSERT into rezervacije (grupa_id,datetime_start,datetime_end,datetime_created,dutaion_mins,confirmed) VALUES ".$vals;
			if ($conn->query($sql)==TRUE){

			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}

		function getDbValues($_id){
			$sql = "SELECT * from rezervacije";
			$result = $conn->query($sql);

			if ($result -> num_rows > 0){
				while($row = $result->fetch_assoc()){
					if ($row['id'] == $_id) {
						$this->id = $_id;
						$this->grupa_id = $row['grupa_id'];
						$this->dt_start = $row['dt_start'];
						$this->dt_end = $row['dt_end'];
						$this->dt_created = $row['dt_created'];
						$this->duration_mins = $row['duration_mins'];
						$this->confirmed = $row['confirmed'];
						$this->igraliste = $row['igraliste'];
					}
				}
			}
		}
	}
	$obj = new Reservation;
	$obj->getDbValues(1);
	var_dump(get_object_vars($obj));
?>