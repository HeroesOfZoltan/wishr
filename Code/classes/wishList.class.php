<?php
require_once("classes/wish.class.php");

class WishList{

	public static function createList(){

		if(isset($_POST['listName'])){
			$mysqli = DB::getInstance();
			$listName = $mysqli->real_escape_string($_POST['listName']);

			$query = "INSERT INTO list 
					  (listName) 
					  VALUES ('$listName')
			";

			$mysqli->query($query);

			$lastId = $mysqli->insert_id;
			$_SESSION['listId']= $lastId;


			return ['newList' => TRUE, 'listName' => $listName, 'listId' =>$lastId];
			//redirect' => "?/wishList/getList/'$lastId'"];
		}

		return ['newList' => FALSE];
	}

	public static function getList($params){
		$mysqli = DB::getInstance();

		$id = $params[0];

		 if(is_numeric($id)){
		 	$query = " SELECT *
						 FROM list, item
						 WHERE list.id = item.list_id
						 AND list.id = $id
			 ";
		 }
		 else{
		 	$query = "SELECT *
						 FROM list, item
						 WHERE list.id = item.list_id
						 AND list.name = '$id'
			 ";
		 }	


		 if($result = $mysqli->query($query)){
		 	while($item = $result->fetch_assoc()){
			 	$items[] = $item;
			 	}

			}
			
		  return ['newList' => TRUE,'items' => $items];
	}



	public static function addItem($listId){
		$lustId=$_SESSION['listId'];
		$wish = new Wish($lustId, $_POST['wishName']);
		return ['redirect' => "?/wishList/getList/$lustId"];
	}


}