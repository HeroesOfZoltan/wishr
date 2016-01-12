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
				$_SESSION['user']['role'] = $user['role'];		// <---------------------
			}
			$userId = $_SESSION['user']['id'];

			$_SESSION['userPermission'] = Sql::userPermission($userId);
//Borde kanske flytta nedan kod och ersätta med ett metodanrop som skriver ut listan istället?

			if ($_SESSION['user']['role'] == 1) {				// <---------------------
				return ['admin' => TRUE, 'dashboard' => Sql::dashBoard()];
				
			}
			
			Sql::setUniqueUrl($userId);

			$items = Sql::getListItems($_SESSION['uniqueUrl'], $userId);

			
			if($items){
				return ['user' => $_SESSION['user'], 'userPermission' => $_SESSION['userPermission'],'items' => $items, 'categories' => Sql::category(),'uniqueUrl' =>$_SESSION['uniqueUrl'],'listNames'=> Sql::listName($_SESSION['uniqueUrl'])];
			}
			else if ($user['id'])	{
				return ['user' => $_SESSION['user'], 'userPermission' => $_SESSION['userPermission']];
			}
		}
		return [];
	}

	public static function payUp() {
		return ['payment' => 'pending', 'userId' => $_SESSION['user']['id'], 'uniqueUrl' => $_SESSION['uniqueUrl'], 'userPermission' => $_SESSION['userPermission']];

	} 


	public static function getBlacklist($params) {

		$uniqueUrl = $params[0];

		$userId = $_SESSION['user']['id'];
		return ['blacklist' => TRUE, 'items' => Sql::getListItems($uniqueUrl, $userId), 'user' => $_SESSION['user']['id'], 'uniqueUrl' => $_SESSION['uniqueUrl'], 'userPermission' => $_SESSION['userPermission'], 'categories' =>Sql::category(), Sql::getListItems($uniqueUrl, $userId),];

		return ['redirect' => "?/User/getBlacklist/$userId"];
	} 


	public static function payPermission($params) {
		$mysqli = DB::getInstance();
		$id = $params[0];
		$idClean = $mysqli->real_escape_string($id);
		Sql::insertUserPermission($idClean);
		$uniqueUrl = $_SESSION['uniqueUrl'];
		$_SESSION['userPermission'] = Sql::userPermission($userId);
		return ['redirect' => "?/wishList/getList/$uniqueUrl"];
	}


//Listvy för en gäst
	public static function guestView($params){
			$mysqli = DB::getInstance();

			$uniqueUrl = $params[0];
			$uniqueUrlClean = $mysqli->real_escape_string($uniqueUrl);

			return ['guestListItems' => Sql::listItemsGuest($uniqueUrlClean), 'listNames'=> Sql::listName($uniqueUrlClean), 'userpermission' => Sql::getUserGuestPermission($uniqueUrlClean)];
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