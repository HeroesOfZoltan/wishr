<?php


class WishList{

	public static function createList(){

		if(isset($_POST['listName'])){
			$mysqli = DB::getInstance();
			$listName = $mysqli->real_escape_string($_POST['listName']);

			Sql::insertNewList($listName);

			return ['newList' => TRUE, 'listName' => $listName, 'listId' =>$_SESSION['listId'], 'categories' =>Sql::category(),'user' => $_SESSION['user']];
			//redirect' => "?/wishList/getList/'$lastId'"];
		}
		return ['newList' => FALSE];
	}
	

	public static function getList($params){
		$mysqli = DB::getInstance();

		$listId = $params[0];
		$userId = $_SESSION['user']['id'];

		  return ['newList' => TRUE, 'items' => Sql::getListItems($listId, $userId), 'categories' => Sql::category(),'user' => $_SESSION['user'], 'listId' =>$_SESSION['listId']];
	}



	public static function addItem($params){

	$listId=$_SESSION['listId']['listId'];
		$wish = new Wish($listId, $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'] );
		return ['redirect' => "?/wishList/getList/$listId"];
	}


	}

