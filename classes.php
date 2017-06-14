<?php
	require_once("init.php");

	class Igraliste {
		var $num,$price,$curr_res_id;

		function __construct($num){
			$this->num = $num;
			$this->getDbValues($num);
		}
		function getDbValues($id) {
			global $conn;
			$sql = "SELECT * from igralista";
			$result = $conn->query($sql);
			if ($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					if ($row['id'] == $id) {
						$this->num = $id;
						$this->curr_res_id = $row['current_rezervacija_id'];
						$this->price = $row['price'];
					}
				}				
			}
		}
	}

	class Rezervacija {
		var $id,$grupa_id,$dt_start,$dt_end,$dt_created,$duration_mins,$confirmed,$igraliste;
		//taken directly from db
		var $db_vals;

		var $active;

		function __construct($id = 0){
			$this->dt_start = new DateTime;
			$this->dt_end = new DateTime;
			$this->dt_created = new DateTime;
			$this->db_vals = array();
			if ($id != 0){
				$this->getDbValues($id);
				$this->setDateTimes();
			}
		}

		function setDateTimes(){
			$this->dt_start = $this->db_vals['datetime_start'];
			$this->dt_end = $this->db_vals['datetime_end'];
			$this->dt_created = $this->db_vals['datetime_created'];
		}

		function isActive(){
			global $dt_now;
			$this->active = ($confirmed and isActiveTime($dt_now));
			return $this->active;
		}

		function isExpired() {
			//dali je proslo vrijeme zavrsetka rezervacije
			global $dt_now;
			return $dt_now > $this->dt_end;
		}

		function isActiveTime($_dt_now){
			//jeli neko vrijeme (argument) unutar aktivnog vremena rezervacije
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
			//ubaci vrijednosti objekta u bazu podataka
			global $conn;
			$vals = "";
			$sql_cols = "";
			$i = 1;
			foreach ($this->db_vals as $key => $value){
				$sql_cols = $sql_cols."{$key}";
				$vals = $vals."'{$value}'";
				if ($i < count($this->db_vals)){
					$sql_cols = $sql_cols.",";
					$vals = $vals.",";
				}
				$i += 1;
			}
			$sql = "INSERT into rezervacije ({$sql_cols}) VALUES ({$vals});";
			if ($conn->query($sql)==TRUE){

			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}

		function getDbValues($_id){
			//ubaci vrijednosti baze podataka (trazi po id-u) u objekt
			global $conn;
			$sql = "SELECT * from rezervacije";
			$result = $conn->query($sql);

			if ($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					if ($row['id'] == $_id) {
						$this->db_vals = $row;
					}
				}
			}
		}
	}

	class Grupa {
		var $id, $members, $dt_created, $curr_res_id,$bodovi;
		//taken directly from db

		var $db_vals;

		var $members_array,$leader_id, $others;
		//taken from $members

		/*
		$members sadrzi string sa svim id brojevima clanova grupe, odvojeni zarezom
		$members_array je array tih brojeva
		$leader_id je prvi element $members_arraya, a to je leader grupe
		$others je $members_array bez prvog clana, tj. bez lidera.
		*/

		function __construct($id) {
			$this->dt_created = new DateTime;
			$this->db_vals = array();
			if ($id != 0){
				$this->getDbValues($id);
				$this->setMembersVars();
				$this->setDateTimes();
			}		
		}

		function getMembersArray(){
			return explode(',',$this->db_vals['members']);
		}

		function setMembersVars(){
			$this->members_array = $this->getMembersArray();
			$this->leader_id = $this->members_array[0];
			$this->others = array_slice($this->members_array,1);
		}

		function setDbValues(){
			//ubaci vrijednosti objekta u bazu podataka
			global $conn;
			$vals = "";
			$sql_cols = "";
			$i = 1;
			foreach ($this->db_vals as $key => $value){
				$sql_cols = $sql_cols."{$key}";
				$vals = $vals."'{$value}'";
				if ($i < count($this->db_vals)){
					$sql_cols = $sql_cols.",";
					$vals = $vals.",";
				}
				$i += 1;
			}
			$sql = "INSERT into rezervacije ({$sql_cols}) VALUES ({$vals});";
			if ($conn->query($sql)==TRUE){

			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}

		function getDbValues($_id){
			//ubaci vrijednosti baze podataka (trazi po id-u) u objekt
			global $conn;
			$sql = "SELECT * from rezervacije";
			$result = $conn->query($sql);

			if ($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					if ($row['id'] == $_id) {
						$this->db_vals = $row;
					}
				}
			}
		}
	}

	//tests
	$obj = new Grupa;
	$obj->getDbValues(1);
	var_dump(get_object_vars($obj));
?>