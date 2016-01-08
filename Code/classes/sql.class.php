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
			"SELECT *, item.id as 'itemId'
			FROM list, item, category
			WHERE list.unique_string = item.list_unique_string
			AND item.category_id = category.id
			AND list.user_id = '$userId'";

		return Self::arrayResult($query);
	}

	public static function getListItems($uniqueUrl, $userId){
		 	$query = 
		 		"SELECT *, item.id as 'itemId'
				FROM list, item, category
				WHERE list.unique_string = item.list_unique_string
				AND item.category_id = category.id
				AND list.unique_string = '$uniqueUrl'
				AND list.user_id = $userId";

			return Self::arrayResult($query);
		 }

	public static function listItemsGuest($uniqueUrl) {
		$query = 
			"SELECT item.wish, item.description, category.categoryName, item.isChecked, item.id as itemId, 
			list.unique_string as uniqueUrl, user.role, item.checked_by
			FROM list, item, category, user
			WHERE category.id = item.category_id
			AND item.list_unique_string = list.unique_string
			AND list.user_id = user.id
			AND list.unique_string = '$uniqueUrl' ";		

		return Self::arrayResult($query);
	}

	public static function listName($uniqueUrl) {
		$query =
			"SELECT listName
			FROM list
			WHERE list.unique_string = '$uniqueUrl'
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

	public static function setUniqueUrl($userId) {
		$mysqli = DB::getInstance();
		$query = 
			"SELECT list.unique_string as uniqueUrl
			FROM list
			WHERE list.user_id = '$userId'
			LIMIT 1";

			if($result = $mysqli->query($query)){
		 		$item = $result->fetch_assoc();

			 	$_SESSION['uniqueUrl'] = $item['uniqueUrl'];
			}			
	}

	public static function insertNewList($listName, $uniqueUrl){
		$mysqli = DB::getInstance();
		$userId = $_SESSION['user']['id'];
			$query =
				"INSERT INTO list 
				(listName, user_id, unique_string) 
				VALUES ('$listName', '$userId', '$uniqueUrl')";
			$mysqli->query($query);
			//$lastId = $mysqli->insert_id;
			$_SESSION['uniqueUrl'] = $uniqueUrl;
	}

	public static function payTrue($id){
		$mysqli = DB::getInstance();
		$query = "UPDATE user
				SET role=3
				WHERE id=$id";

		$mysqli->query($query);
	}
	public static function itemDone($itemId, $checkedBy){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE item
				SET isChecked = 1, checked_by = '$checkedBy'
				WHERE id = $itemId";
		$mysqli->query($query);
	}

	public static function itemUnDone($itemId){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE item
				SET isChecked=NULL, checked_by = NULL
				WHERE id = $itemId";
		/*"UPDATE item
				SET isChecked=NULL
				WHERE id = $itemId";*/
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