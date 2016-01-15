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
			$message = Sql::insertUser($password, $usernameClean, $firstnameClean, $lastnameClean, 2);
			}

			$userId = $mysqli->insert_id;
			$uniqueString = substr(md5(microtime()),rand(0,26),5); //genererar unik sträng på 5 tecken.
			Sql::insertNewList($firstnameClean, $uniqueString, $userId); //Anropar metod som sparar ny lista i databasen

		return ['message' => $message];			
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
				$_SESSION['user']['role'] = $user['role'];
				if ($user['role'] == 1) {
					return ['redirect' => "?/Admin/adminDash"];
				}
				
				return ['redirect' => "?/User/myList"];

			}else{
				return ['redirect' => "?/"];
			}
//Borde kanske flytta nedan kod och ersätta med ett metodanrop som skriver ut listan istället?
	
		}
		return [];
	}

	public static function mylist(){

		$_SESSION['userPermission'] = Sql::userPermission($_SESSION['user']['id']);
		Sql::setUniqueUrl($_SESSION['user']['id']);

		$items = Sql::getListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']);

		
		if($items){
			return ['items' => $items, 'categories' => Sql::category(),'listNames'=> Sql::listName($_SESSION['uniqueUrl'])];
		}
		else if ($_SESSION['user']['id'])	{
			return ['listNames'=> Sql::listName($_SESSION['uniqueUrl']),'categories' =>Sql::category()];
		}
	}

	public static function payUp() {
		return [];

	} 
		public static function ourProduct() {
		return [];

	} 


	public static function getBlacklist() {
		
		return ['blacklist' => TRUE, 'items' => Sql::getBlackListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']),'categories' =>Sql::category(), Sql::getListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']),];
	} 


	public static function payPermission($params) {
		$mysqli = DB::getInstance();
		$id = $params[0];
		$idClean = $mysqli->real_escape_string($id);
		Sql::insertUserPermission($idClean);
		$uniqueUrl = $_SESSION['uniqueUrl'];
		$_SESSION['userPermission'] = Sql::userPermission($_SESSION['user']['id']);
		return ['redirect' => "?/wishList/getList/$uniqueUrl"];
	}


//Listvy för en gäst
	public static function guestView($params){
			$mysqli = DB::getInstance();

			$uniqueUrl = $params[0];
			$uniqueUrlClean = $mysqli->real_escape_string($uniqueUrl);

			return ['guestListItems' => Sql::listItemsGuest($uniqueUrlClean), 'guestBlackListItems' => Sql::listBlackItemsGuest($uniqueUrlClean), 'listNames'=> Sql::listName($uniqueUrlClean), 'userPermission' => Sql::getUserGuestPermission($uniqueUrlClean)];
		}

		public static function itemDone($params) {

			Sql::itemDone($_POST['itemId'], $_POST['checked_by']);
			$uniqueUrl = $params[0];

			return ['redirect' => "?/User/guestView/$uniqueUrl"];
		}
		public static function unDoneItem($params) {

			Sql::itemUnDone($_POST['itemId']);
			$uniqueUrl = $params[0];

			return ['redirect' => "?/User/guestView/$uniqueUrl"];
		}

}