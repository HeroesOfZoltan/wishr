<?php
class User{

//Returnerar array med permissions för varje metod. TRUE innebär att en måste vara inloggad för att få anropa den metoden
	public static function check(){

		$methods= ['createUser' => FALSE,'logIn' => FALSE,'ourProduct' => FALSE, 'payUp' => TRUE,'payPermission' => TRUE, 
		'updateUserInfo' => TRUE];

		return $methods;
	}

//Skapar, tvättar, krypterar och sparar ner ny user till databasem
	public static function createUser($params){
		
		if(isset($_POST['email'])){
			$mysqli = DB::getInstance();
			$usernameClean = $mysqli->real_escape_string($_POST['email']);
			$passwordClean = $mysqli->real_escape_string($_POST['password']);
			$firstnameClean = $mysqli->real_escape_string($_POST['firstname']);
			$lastnameClean = $mysqli->real_escape_string($_POST['lastname']);

			$password = crypt($passwordClean,'$2a$'.sha1($usernameClean));
		//Tar emot värde för lyckad registrering
			$message = Sql::insertUser($password, $usernameClean, $firstnameClean, $lastnameClean);
			}
		//Tar user id från databasen som just gjordes och kopplar det till listan
			$userId = $mysqli->insert_id;
		//Skapar en unik string på tecken som blir primärnyckel för listan
			$uniqueString = substr(md5(microtime()),rand(0,26),5);
			Sql::insertNewList("Your name","Your partners name", $uniqueString, $userId, 'fa fa-heart');

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
//Om inloggning lyckas sparas user id och role in i session
			if($user['id']){
				$_SESSION['user']['id'] = $user['id'];
				$_SESSION['user']['role'] = $user['role'];

				Sql::getUserPermission($_SESSION['user']['id']);
				Sql::setUniqueUrl($_SESSION['user']['id']);
			//Role == 1 innebär Admin
				if ($user['role'] == 1) {
					return ['redirect' => "?/Admin/adminDash"];
				}
				return ['redirect' => "?/wishList/myList"];
			}
			else{
				return ['redirect' => "?/"];
			}	
		}
		return [];
	}
//Visar customize/betal-sidan
	public static function payUp() {
		return ['userInfo' => Sql::getUserInfo($_SESSION['user']['id']),'listInfo' => Sql::getListInfo($_SESSION['uniqueUrl']), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];

	} 
//Visar customize/betal-sidan för icke inloggad
	public static function ourProduct() {
		return [];

	} 
//Hanterar ett köp av funktion
	public static function payPermission($params) {
		$mysqli = DB::getInstance();

		Sql::insertUserPermission($_SESSION['user']['id']);
		$uniqueUrl = $_SESSION['uniqueUrl'];

		Sql::getUserPermission($_SESSION['user']['id']);
		return ['redirect' => "?/User/payUp/#pageContent2"];
	}
//Hanterar ändring av användaruppgifter
	public static function updateUserInfo($params){
		$mysqli = DB::getInstance();

		$firstNameClean = $mysqli->real_escape_string($_POST['newFirstName']);
		$LastNameClean = $mysqli->real_escape_string($_POST['newLastName']);
		$emailClean = $mysqli->real_escape_string($_POST['newEmail']);

		Sql::updateUserInfo($_SESSION['user']['id'], $firstNameClean, $LastNameClean, $emailClean);

		return ['redirect' => "?/User/payUp/#pageContent2"];
	}
}