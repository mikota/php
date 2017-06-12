<?php
	class Reservation {
		var $id,$grupa_id,$dt_start,$dt_end,$dt_created,$duration_mins,$confirmed;
		//taken directly from db

		function __construct(){
			$this->id = $this->returnNum(3);
		}
		function returnNum($num){
			return $num;
		}
	}
	$obj = new Reservation;
	echo $obj->id;
?>