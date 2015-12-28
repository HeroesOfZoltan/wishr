<?php
/*require_once("classes/wish.class.php");
require_once("classes/User.class.php");*/

class WishList{

	public static function createList(){

		if(isset($_POST['listName'])){
			$mysqli = DB::getInstance();
			$listName = $mysqli->real_escape_string($_POST['listName']);

			Sql::insertNewList($listName);
			/*$userId = $_SESSION['user']['id'];
			$query = "INSERT INTO list 
					  (listName, user_id) 
					  VALUES ('$listName', '$userId')
			";

			$mysqli->query($query);

			$lastId = $mysqli->insert_id;
			$_SESSION['listId']['listId']= $lastId;*/



			return ['newList' => TRUE, 'listName' => $listName, 'listId' =>$_SESSION['listId']['listId'], 'categories' =>Sql::category(),'user' => $_SESSION['user']];
			//redirect' => "?/wishList/getList/'$lastId'"];
		}

		return ['newList' => FALSE];
	}

	public static function getList($params){
		$mysqli = DB::getInstance();


		$listId = $params[0];
		$userId = $_SESSION['user']['id'];
		 

		 	//$query =Sql::getListItems($listId, $userId);
		 	/*$query = " SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.id = $listId
						 AND list.user_id = $userId
			 ";
		 }
		 else{
		 	$query = "SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.name = '$id'
						 AND list.user_id = $userId
			 ";
		 }	

		 if($result = $mysqli->query($query)){
		 	while($item = $result->fetch_assoc()){
			 	$items[] = $item;
			 	}
			}*/


			 //$GuestLoginData = User::getGuestFormLoginData($userId);

			 
			//var_dump($GuestLoginData);
				
			
		  return ['newList' => TRUE, 'items' => Sql::getListItems($listId, $userId), 'categories' => Sql::category(),'user' => $_SESSION['user'], 'listId' =>$_SESSION['listId']['listId']];
	}



	public static function addItem($params){
		if($_SESSION['listId']['listId']){
			$listId=$_SESSION['listId']['listId'];
		}
		else{
		$listId=$_SESSION['listId'];
	}
		$wish = new Wish($listId, $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'] );
		return ['redirect' => "?/wishList/getList/$listId"];
	}

/*
	public static function printGuestLoginForm($listId){
		return ['redirect' => "?/wishList/getList/$listId"];
	}

	public static function getGuestList($listId){
		$cleanedPassword = $mysqli->real_escape_string($_POST['guestVisitorPassword']);
		$cleanedListId = $mysqli->real_escape_string($listId);
		$query = "SELECT *
					FROM list
					AND list.password = '$cleanedPassword'
					AND list.id = $cleanedListId
			";

			if($result = $mysqli->query($query)){
		 	while($listItem = $result->fetch_assoc()){
			 	$listItems[] = $listItem;
			 	}
			 }
			 return ['items' => $listItems];
		 }	

*/
	}

