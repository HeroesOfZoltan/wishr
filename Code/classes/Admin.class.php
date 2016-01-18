<?php

class Admin {

	

	public static function adminDash(){

		return Sql::dashboard();


		/*$arrays =  Self::arrayResult($query);

		if($arrays){
		foreach($arrays as $row ) {
	       	foreach($row as $k['permission'] => $v ) {
	            $dashArray['permissions'][] = $v;
	       }
		}
	}*/
		/*$array = Self::arrayResult($query);

		foreach($array as $permission => $value) {
			foreach($value as $key => $val) {
				if ($key=='number_of_permissions'){

					$val = $val / $dashArray[2]
					echo $val;
				}
			}
			//echo $permission;
		}*/
		//return ['dashboard' => Sql::dashboard()];
	}

	public static function createNewCategory() {
		$mysqli = DB::getInstance();
		$categoryClean = $mysqli->real_escape_string($_POST['newCategory']);

		Sql::insertNewCategory($categoryClean);
		return ['redirect' => '?/Admin/adminDash'];
	
	}
}