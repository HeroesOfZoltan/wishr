<?php
class User{


	public static function createUser($params){
		
		if(isset($_POST['email'])){
			$mysqli = DB::getInstance();
			$usernameClean = $mysqli->real_escape_string($_POST['email']);
			$passwordClean = $mysqli->real_escape_string($_POST['password']);
			$firstnameClean = $mysqli->real_escape_string($_POST['firstname']);
			$lastnameClean = $mysqli->real_escape_string($_POST['lastname']);


			$password = crypt($passwordClean,'$2a$'.sha1($usernameClean));

			$query = "INSERT INTO user
						(password, email, firstname, lastname, role)
						VALUES ('$password','$usernameClean','$firstnameClean','$lastnameClean','2')
			";

				$mysqli->query($query);

				
			
			}	
			return [];			
		}



	public static function login($params){
		
		if(isset($_POST['email'])){
			$mysqli = DB::getInstance();
			$usernameClean = $mysqli->real_escape_string($_POST['email']);
			$passwordClean = $mysqli->real_escape_string($_POST['password']);

			$password = crypt($passwordClean,'$2a$'.sha1($usernameClean));

			$query = "SELECT id
				FROM user
				WHERE email = '$usernameClean'
				AND password = '$password'
				LIMIT 1
			";

			$result = $mysqli->query($query);
			$user = $result->fetch_assoc();
			if($user['id']){
				$_SESSION['user']['id'] = $user['id'];
			}	
			$userId =$_SESSION['user']['id'];

			$query = "SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.user_id = $userId
			 ";
		 if($result = $mysqli->query($query)){
		 	while($item = $result->fetch_assoc()){
			 	$items[] = $item;
			 	}
			}

			 $query2 = "SELECT * 
					FROM category
			";
			if($result = $mysqli->query($query2)){
		 	while($category = $result->fetch_assoc()){
			 	$categories[] = $category;
			 	}
			 }


			 $query = "SELECT list.id as listId
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.user_id = $userId
						 LIMIT 1
			 ";
		 if($result = $mysqli->query($query)){
		 	$item = $result->fetch_assoc();
			 	$_SESSION['list'] = $item;
			}

			if($items){
				
				return ['user' => $_SESSION['user'],'items' => $items, 'categories' => $categories];
			}
			else if ($user['id'])	{
				return ['user' => $_SESSION['user']];
			}

			//Sätter listId i session
			$query3 = " SELECT list.id  AS listId
							FROM list, item, category
							WHERE list.id = item.list_id
							AND item.category_id = category.id
							AND list.user_id =$userId
							LIMIT 1
			 ";
			 $result = $mysqli->query($query3);
		 	$listIdt= $result->fetch_assoc();
			$_SESSION['listId']=$listIdt;
		}
		return [];
	}

}