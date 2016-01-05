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
		$query = "SELECT * FROM category";
		return Self::arrayResult($query);
	}

	public static function listItems($userId) {
		$query = 
			"SELECT *
			FROM list, item, category
			WHERE list.id = item.list_id
			AND item.category_id = category.id
			AND list.user_id = $userId";

		return Self::arrayResult($query);
	}

	public static function getListItems($listId, $userId){
		 	$query = 
		 		"SELECT *
				FROM list, item, category
				WHERE list.id = item.list_id
				AND item.category_id = category.id
				AND list.id = $listId
				AND list.user_id = $userId";

			return Self::arrayResult($query);
		 }

	public static function listItemsGuest($listId) {
		$query = 
			"SELECT item.wish, item.description, category.categoryName, item.isChecked, item.id as itemId, 
			list.id as listId, list.paid_list
			FROM list, item, category
			WHERE category.id = item.category_id
			AND item.list_id = list.id
			AND list.id =$listId";		

		return Self::arrayResult($query);
	}

	public static function listName($listId) {
		$query =
			"SELECT listName
			FROM list, item
			WHERE list.id = $listId
			LIMIT 1";

		return Self::arrayResult($query);
	}

	public static function checkUserName($email) {
		$mysqli = DB::getInstance();

		$query = 
			"SELECT email
			FROM user
			WHERE email = '$email'";

		$result = $mysqli->query($query);
		
		if($row = $result->fetch_assoc()){
				$exists = TRUE;
			}
		else {
			$exists = NULL;
		}
		return $exists;
	}

	public static function insertUser($pass, $email, $first, $last, $role) {
		$mysqli = DB::getInstance();

		$exists = Self::checkUserName($email);
		
		if($exists === TRUE){
			echo 'User already exists!';
		}
		else {
			$query = 
				"INSERT INTO user
				(password, email, firstname, lastname, role)
				VALUES ('$pass','$email','$first','$last','$role')";
			$mysqli->query($query);

			echo "New user $email created! Please log in to start your list.";
		}
	}
	
	public static function logIn($user, $pass){
		$mysqli = DB::getInstance();
		$query = 
			"SELECT id, role
			FROM user
			WHERE email = '$user'
			AND password = '$pass'
			LIMIT 1";
		$result = $mysqli->query($query);
		$user = $result->fetch_assoc();

		return $user;
	}

	public static function setListId($userId) {
		$mysqli = DB::getInstance();
		$query = 
			"SELECT list.id as listId
			FROM list, item, category
			WHERE list.id = item.list_id
			AND item.category_id = category.id
			AND list.user_id =$userId
			LIMIT 1";

			if($result = $mysqli->query($query)){
		 		$item = $result->fetch_assoc();
			 	$_SESSION['listId'] = $item;
			}			
	}

	public static function insertNewList($listName){
		$mysqli = DB::getInstance();
		$userId = $_SESSION['user']['id'];
			$query =
				"INSERT INTO list 
				(listName, user_id) 
				VALUES ('$listName', '$userId')";
			$mysqli->query($query);
			$lastId = $mysqli->insert_id;
			$_SESSION['listId']['listId'] = $lastId;
	}

	public static function payTrue($id){
		$mysqli = DB::getInstance();
		$query = "UPDATE user
				SET role=3
				WHERE id=$id";

		$mysqli->query($query);
	}
	public static function itemDone($itemId){
		$mysqli = DB::getInstance();
		
		$query = "UPDATE item
				SET isChecked=1
				WHERE id = $itemId";
		$mysqli->query($query);
	}

	public static function itemUnDone($itemId){
		$mysqli = DB::getInstance();
		
		$query = "UPDATE item
				SET isChecked=NULL
				WHERE id = $itemId";
		$mysqli->query($query);
	}

	public static function dashBoard() {
	$mysqli = DB::getInstance();
	$dashArray=[];
	$query = "SELECT COUNT(id) as lists
				 FROM list
				 LIMIT 1";
$result = $mysqli->query($query);
		$dashArray[] = $result->fetch_assoc();
	$query = "SELECT COUNT(id) as users
				FROM user
				LIMIT 1";
	
	$result = $mysqli->query($query);
		$dashArray[] = $result->fetch_assoc();			
	$query = "SELECT COUNT(*) as customers
	FROM user WHERE user.role=3
	LIMIT 1"; 
	
	$result = $mysqli->query($query);
		$dashArray[] = $result->fetch_assoc();	

		return $dashArray;
	}
}