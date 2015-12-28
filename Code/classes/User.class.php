<?php
class User{


	public static function createUser($params){
		
		if(isset($_POST['email'])){
			$mysqli = DB::getInstance();
			$usernameClean = $mysqli->real_escape_string($_POST['email']);
			$passwordClean = $mysqli->real_escape_string($_POST['password']);
			$firstnameClean = $mysqli->real_escape_string($_POST['firstname']);
			$lastnameClean = $mysqli->real_escape_string($_POST['lastname']);
			$roleIdClean = $mysqli->real_escape_string($_POST['roleId']);

			$password = crypt($passwordClean,'$2a$'.sha1($usernameClean));


			Sql::insertUser($password, $usernameClean, $firstnameClean, $lastnameClean, $roleIdClean);
			}	
			return [];			
		}



	public static function login($params){
		
		if(isset($_POST['email'])){
			$mysqli = DB::getInstance();
			$usernameClean = $mysqli->real_escape_string($_POST['email']);
			$passwordClean = $mysqli->real_escape_string($_POST['password']);

			$password = crypt($passwordClean,'$2a$'.sha1($usernameClean));



			$user = Sql::logIn($usernameClean, $password);

			if($user['id']){
				$_SESSION['user']['id'] = $user['id'];
			}	
			$userId = $_SESSION['user']['id'];

			$items = Sql::listItems($userId);

			Sql::setListId($userId);




		

			if($items){
				
				return ['user' => $_SESSION['user'],'items' => $items, 'categories' => Sql::category(),'listId' =>$_SESSION['listId']['listId']];
			}
			else if ($user['id'])	{
				return ['user' => $_SESSION['user']];
			}


		}
		return [];


	}
	

	public static function getGuestFormLoginData($params){
			$mysqli = DB::getInstance();


			$listId = $params[0];
			$listIdClean = $mysqli->real_escape_string($listId);



			$query = " SELECT listName
						FROM list, item
						WHERE list.id = $listIdClean
						LIMIT 1
						";

			if($result = $mysqli->query($query)){
				$listName = $result->fetch_assoc();
			}

			return ['guestListItems' => Sql::listItemsGuest($listIdClean), 'listNames'=>$listName];
		}
}