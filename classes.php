<?php
	include 'sql_connect.php';
	include 'init_datetime.php';

	$prices = array(1=>100,2=>200,3=>500);

	class Igraliste {
		var $num,$price;

		function __construct($_num,$prices){
			$this->num = $_num;
			$this->price = $prices[$this->num];
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
			global $dt_now;
			$this->active = ($confirmed and isActiveTime($dt_now));
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
			global $conn;
			$vals = "('{$this->grupa_id}','{$this->dt_start}','{$this->dt_end}','{$this->dt_created}','{$this->duration_mins}','{$this->confirmed}','{$this->igraliste}')";
			$sql = "INSERT into rezervacije (grupa_id,datetime_start,datetime_end,datetime_created,duration_mins,confirmed,igraliste) VALUES ".$vals;
			if ($conn->query($sql)==TRUE){

			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}

		function getDbValues($_id){
			global $conn;
			$sql = "SELECT * from rezervacije";
			$result = $conn->query($sql);

			if ($result -> num_rows > 0){
				while($row = $result->fetch_assoc()){
					if ($row['id'] == $_id) {
						$this->id = $_id;
						$this->grupa_id = $row['grupa_id'];
						$this->dt_start = $row['datetime_start'];
						$this->dt_end = $row['datetime_end'];
						$this->dt_created = $row['datetime_created'];
						$this->duration_mins = $row['duration_mins'];
						$this->confirmed = $row['confirmed'];
						$this->igraliste = $row['igraliste'];
					}
				}
			}
		}
	}

	class Grupa {
		var $id, $members, $dt_created, $curr_res_id,$bodovi;
		//taken directly from db

		var $leader_id, $others;
		//taken from $members

		function __construct() {
			$this->dt_created = new DateTime;
		}

		function $setDbValues() {
			global $conn;
			$vals = "('{$this->members}','{$this->dt_created}','{$this->curr_res_id}')";
		}

	}

	/*
	$obj = new Reservation;
	$obj->getDbValues(1);
	$obj->setDbValues();
	$obj2 = new Reservation;
	$obj2->getDbValues(2);
	var_dump(get_object_vars($obj));
	*/
?>