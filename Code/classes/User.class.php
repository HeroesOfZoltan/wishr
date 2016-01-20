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
			Sql::insertNewList($firstnameClean,$lastnameClean, $uniqueString, $userId, 'fa fa-heart'); //Anropar metod som sparar ny lista i databasen

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
			return ['items' => $items, 'categories' => Sql::category(),'listName' => Sql::getListName($_SESSION['uniqueUrl']), 'listSubNames'=> Sql::getListSubName($_SESSION['uniqueUrl']), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
		}
		else if ($_SESSION['user']['id'])	{
			return ['listName' => Sql::getListName($_SESSION['uniqueUrl']), 'listSubNames'=> Sql::getListSubName($_SESSION['uniqueUrl']),'categories' =>Sql::category(),'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
		}
	}

	public static function payUp() {
		return ['listName' => Sql::getListName($_SESSION['uniqueUrl']), 'listSubNames'=> Sql::getListSubName($_SESSION['uniqueUrl']), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];

	} 
		public static function ourProduct() {
		return [];

	} 


	public static function getBlacklist() {
		
		return ['blacklist' => TRUE, 'items' => Sql::getBlackListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']),'categories' =>Sql::category(), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
	} 


	public static function payPermission($params) {
		$mysqli = DB::getInstance();
		$id = $params[0];
		$idClean = $mysqli->real_escape_string($id);
		Sql::insertUserPermission($idClean);
		$uniqueUrl = $_SESSION['uniqueUrl'];
		$_SESSION['userPermission'] = Sql::userPermission($_SESSION['user']['id']);
		return ['redirect' => "?/User/payUp/"];
	}


//Listvy för en gäst
	public static function guestView($params){
			$mysqli = DB::getInstance();

			$uniqueUrl = $params[0];
			$uniqueUrlClean = $mysqli->real_escape_string($uniqueUrl);
			Sql::getUserGuestPermission($uniqueUrlClean);
			return ['guestListItems' => Sql::listItemsGuest($uniqueUrlClean),'imageUrl' => Sql::getListImage($uniqueUrlClean), 'listName' => Sql::getListName($uniqueUrl), 'guestBlackListItems' => Sql::listBlackItemsGuest($uniqueUrlClean), 'listSubNames'=> Sql::getListSubName($uniqueUrl)];
		}

		public static function itemDone($params) {
			$mysqli = DB::getInstance();
			$itemIdClean = $mysqli->real_escape_string($_POST['itemId']);
			$checkedByClean = $mysqli->real_escape_string($_POST['checked_by']);

			Sql::itemDone($itemIdClean, $checkedByClean);

			$uniqueUrl = $params[0];
			return ['redirect' => "?/User/guestView/$uniqueUrl"];
		}
		public static function unDoneItem($params) {
			$mysqli = DB::getInstance();
			$itemIdClean = $mysqli->real_escape_string($_POST['itemId']);

			Sql::itemUnDone($itemIdClean);
			
			$uniqueUrl = $params[0];
			return ['redirect' => "?/User/guestView/$uniqueUrl"];
		}

}