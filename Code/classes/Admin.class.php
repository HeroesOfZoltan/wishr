<?php

class Admin {

	public static function check(){

		$methods= ['adminDash' => TRUE, 'createNewCategory' => TRUE, 'deleteCategory' => TRUE];

		return $methods;
	}

	public static function adminDash(){

		Sql::setUniqueUrl($_SESSION['user']['id']);

		$dashboard = Sql::getDashboard();

	// Räknar om värden från databasen till en procentsats som sedan läggs in i return arrayen
		foreach($dashboard['permissions'] as $permission => $value) {
			foreach($value as $key => $val){
				if($key == 'number_of_permissions') {
					$val = round($val / $dashboard['customers']['customers'] * 100);
					$percent[] = $val;
				}
			}
		}
		return ['users' => $dashboard['users'], 'lists' => $dashboard['lists'], 'customers' => $dashboard['customers'], 'percent' => $percent, 'categories' => Sql::category(), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
	}

	public static function createNewCategory() {
		$mysqli = DB::getInstance();
		$categoryClean = $mysqli->real_escape_string($_POST['newCategory']);
		Sql::insertNewCategory($categoryClean);

		return ['redirect' => '?/Admin/adminDash'];
	
	}
	public function deleteCategory(){
		$category = $_POST['category'];
		Sql::deleteCategory($category);
		
		return ['redirect' => '?/Admin/adminDash'];
	}
}