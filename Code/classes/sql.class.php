<?php

class Sql {

	private static function arrayResult($query) {
		$mysqli = DB::getInstance();

		if($result = $mysqli->query($query)){
			 	while($row = $result->fetch_assoc()){
				 	$arrayResult[] = $row;
				 	}
				 }
			return $arrayResult;
	}

	public static function category() {
		//$mysqli = DB::getInstance();


		$query = "SELECT * FROM category";


			return Self::arrayResult($query);

/*			if($result = $mysqli->query($query)){
			 	while($category = $result->fetch_assoc()){
				 	$categories[] = $category;
				 	}
				 }
			return $categories;*/
	}
	public static function listItems($userId) {

		$query = "SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.user_id = $userId
			 ";

		return Self::arrayResult($query);

	}

	public static function getListItems($listId, $userId){

		 	$query = " SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.id = $listId
						 AND list.user_id = $userId
			 ";

			 return Self::arrayResult($query);

		 }

	public static function listItemsGuest($listId) {

		$query = " 	SELECT *
					FROM list, item, category
					WHERE category.id = item.category_id
					AND item.list_id = list.id
					AND list.id =$listId
					";

		return Self::arrayResult($query);
	}

	public static function insertUser($pass, $name, $first, $last, $role) {
		$mysqli = DB::getInstance();

		$query = "INSERT INTO user
						(password, email, firstname, lastname, role)
						VALUES ('$pass','$name','$first','$last','$role')
			";
		$mysqli->query($query);
	}
	
	public static function logIn($user, $pass){
			$mysqli = DB::getInstance();

		$query = "SELECT id
				FROM user
				WHERE email = '$user'
				AND password = '$pass'
				LIMIT 1
			";

		$result = $mysqli->query($query);
		$user = $result->fetch_assoc();

		return $user;
	}

	public static function setListId($userId) {
		$mysqli = DB::getInstance();
		$query = "SELECT list.id as listId
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.user_id =$userId
						 LIMIT 1
			 ";
			 if($result = $mysqli->query($query)){
		 	$item = $result->fetch_assoc();
			 	$_SESSION['listId'] = $item;
			}
				
	}

	public static function insertNewList($listName){
		$mysqli = DB::getInstance();
		$userId = $_SESSION['user']['id'];
			$query = "INSERT INTO list 
					  (listName, user_id) 
					  VALUES ('$listName', '$userId')
			";

			$mysqli->query($query);

			$lastId = $mysqli->insert_id;
			$_SESSION['listId']['listId']= $lastId;
	}
}