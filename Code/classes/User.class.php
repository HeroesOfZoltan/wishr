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

			/*$query = "INSERT INTO user
						(password, email, firstname, lastname, role)
						VALUES ('$password','$usernameClean','$firstnameClean','$lastnameClean','$roleIdClean')
			";
			$mysqli->query($query);*/

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

			/*$query = "SELECT id
				FROM user
				WHERE email = '$usernameClean'
				AND password = '$password'
				LIMIT 1
			";

			$result = $mysqli->query($query);
			$user = $result->fetch_assoc();
			*/

			$user = Sql::logIn($usernameClean, $password);

			if($user['id']){
				$_SESSION['user']['id'] = $user['id'];
			}	
			$userId = $_SESSION['user']['id'];

			/*$query = "SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.user_id = $userId
			 ";
		 if($result = $mysqli->query($query)){
		 	while($item = $result->fetch_assoc()){
			 	$items[] = $item;
			 	}
			}*/
			$items = Sql::listItems($userId);

			Sql::setListId($userId);

			 /*$query = "SELECT * 
					FROM category
			";
			if($result = $mysqli->query($query)){
			 	while($category = $result->fetch_assoc()){
				 	$categories[] = $category;
				 	}
			 }*/



			 /*$query = "SELECT list.id as listId
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.user_id =$userId
						 LIMIT 1
			 ";
		 if($result = $mysqli->query($query)){
		 	$item = $result->fetch_assoc();
			 	$_SESSION['listId'] = $item;
			}*/


		

			if($items){
				
				return ['user' => $_SESSION['user'],'items' => $items, 'categories' => Sql::category(),'listId' =>$_SESSION['listId']['listId']];
			}
			else if ($user['id'])	{
				return ['user' => $_SESSION['user']];
			}

			//Sätter listId i session
			/*$query4 = " SELECT list.id  AS listId
							FROM list, item, category
							WHERE list.id = item.list_id
							AND item.category_id = category.id
							AND list.user_id =$userId
							LIMIT 1
			 ";
			 $result = $mysqli->query($query4);
		 	$listIdt= $result->fetch_assoc();
			$_SESSION['listId']=$listIdt;*/
		}
		return [];


	}
	

	public static function getGuestFormLoginData($params){
			$mysqli = DB::getInstance();

			//$listId=$_SESSION['listId']['listId'];
			$listId = $params[0];
			$listIdClean = $mysqli->real_escape_string($listId);


			
			/*$query = " SELECT *
						FROM list, item, category
						WHERE category.id = item.category_id
						AND item.list_id = list.id
						AND list.id =$listIdClean
						";

			if($result = $mysqli->query($query)){
			 	while($item = $result->fetch_assoc()){
				 	$listItems[] = $item;
				 	}
			 }*/
			 //$listIdClean = $mysqli->real_escape_string($listId);
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