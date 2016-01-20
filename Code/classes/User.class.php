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
			$message = Sql::insertUser($password, $usernameClean, $firstnameClean, $lastnameClean);
			}

			$userId = $mysqli->insert_id;
			$uniqueString = substr(md5(microtime()),rand(0,26),5); //genererar unik sträng på 5 tecken.
			Sql::insertNewList("Your name","Your partners name", $uniqueString, $userId, 'fa fa-heart'); //Anropar metod som sparar ny lista i databasen

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
				
				return ['redirect' => "?/wishList/myList"];

			}else{
				return ['redirect' => "?/"];
			}
//Borde kanske flytta nedan kod och ersätta med ett metodanrop som skriver ut listan istället?
	
		}
		return [];
	}


	public static function payUp() {
		return ['listInfo' => Sql::getListInfo($_SESSION['uniqueUrl']), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];

	} 

	public static function ourProduct() {
		return [];

	} 

	public static function payPermission($params) {
		$mysqli = DB::getInstance();

		$id = $params[0];
		$idClean = $mysqli->real_escape_string($id);

		Sql::insertUserPermission($idClean);
		$uniqueUrl = $_SESSION['uniqueUrl'];

		$_SESSION['userPermission'] = Sql::getUserPermission($_SESSION['user']['id']);
		return ['redirect' => "?/User/payUp/#pageContent2"];
	}

}