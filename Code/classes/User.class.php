<?php
class User{

//Skapar, tvättar, krypterar och sparar ner ny user till databasem
	public static function createUser($params){
		
		if(isset($_POST['email'])){
			$mysqli = DB::getInstance();
			$usernameClean = $mysqli->real_escape_string($_POST['email']);
			$passwordClean = $mysqli->real_escape_string($_POST['password']);
			$firstnameClean = $mysqli->real_escape_string($_POST['firstname']);
			$lastnameClean = $mysqli->real_escape_string($_POST['lastname']);

			$password = crypt($passwordClean,'$2a$'.sha1($usernameClean));
			Sql::insertUser($password, $usernameClean, $firstnameClean, $lastnameClean, 2);
			}	
		return [];			
	}

//Hanterar inloggning
	public static function login($params){
		
		if(isset($_POST['email'])){
			$mysqli = DB::getInstance();
			$usernameClean = $mysqli->real_escape_string($_POST['email']);
			$passwordClean = $mysqli->real_escape_string($_POST['password']);

			$password = crypt($passwordClean,'$2a$'.sha1($usernameClean));
			$user = Sql::logIn($usernameClean, $password);
//Om inloggning lyckas sparas user id in i session
			if($user['id']){
				$_SESSION['user']['id'] = $user['id'];
				$_SESSION['user']['role'] = $user['role'];		// <---------------------
			}
			$userId = $_SESSION['user']['id'];
//Borde kanske flytta nedan kod och ersätta med ett metodanrop som skriver ut listan istället?

			if ($_SESSION['user']['role'] == 1) {				// <---------------------
				return ['admin' => TRUE];
				
			}
			$items = Sql::listItems($userId);

			Sql::setListId($userId);

			if($items){
				return ['user' => $_SESSION['user'],'items' => $items, 'categories' => Sql::category(),'listId' =>$_SESSION['listId'],'listNames'=> Sql::listName($_SESSION['listId']['listId'])];
			}
			else if ($user['id'])	{
				return ['user' => $_SESSION['user']];
			}
		}
		return [];
	}

	public static function payUp() {
		return ['payment' => 'pending', 'userId' => $_SESSION['user']['id'], 'listId' => $_SESSION['listId']['listId']];

	} 

	public static function pay($id) {

		$mysqli = DB::getInstance();
		$id = $id[0];
		$idClean = $mysqli->real_escape_string($id);
		Sql::payTrue($idClean);
		$listId = $_SESSION['listId']['listId'];
		return ['redirect' => "?/wishList/getList/$listId"];

	}

//Listvy för en gäst
	public static function getGuestFormLoginData($params){
			$mysqli = DB::getInstance();

			$listId = $params[0];
			$listIdClean = $mysqli->real_escape_string($listId);

			return ['guestListItems' => Sql::listItemsGuest($listIdClean), 'listNames'=> Sql::listName($listIdClean)];
		}
}