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

			$query2 = "SELECT * 
					FROM category
			";

			if($result = $mysqli->query($query2)){
		 	while($category = $result->fetch_assoc()){
			 	$categories[] = $category;
			 	}

			}


			return ['newList' => TRUE, 'listName' => $listName, 'listId' =>$lastId, 'categories' =>$categories];
			//redirect' => "?/wishList/getList/'$lastId'"];
		}

		return ['newList' => FALSE];
	}

	public static function getList($params){
		$mysqli = DB::getInstance();

		$id = $params[0];

		 if(is_numeric($id)){
		 	$query = " SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.id = $id
			 ";
		 }
		 else{
		 	$query = "SELECT *
						 FROM list, item, category
						 WHERE list.id = item.list_id
						 AND item.category_id = category.id
						 AND list.name = '$id'
			 ";
		 }	


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

			
		  return ['newList' => TRUE,'items' => $items,'categories' =>$categories];
	}



	public static function addItem($listId){
		$lustId=$_SESSION['listId'];
		$wish = new Wish($lustId, $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'] );
		return ['redirect' => "?/wishList/getList/$lustId"];
	}


}