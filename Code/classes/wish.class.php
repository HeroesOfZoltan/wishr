<?php

class Wish{
//Tvättar och sparar ner ett önskeobjekt till databasen
	function __construct($uniqueUrl, $wish, $description, $wishCategory){

		$mysqli = DB::getInstance();
		//$uniqueIdClean = $mysqli->real_escape_string($uniqueUrl);
		$wishClean = $mysqli->real_escape_string($wish);
		$descriptionClean = $mysqli->real_escape_string($description);
		$wishCategoryClean = $mysqli->real_escape_string($wishCategory);
		$query = 
			"INSERT INTO item 
			(wish, list_unique_string, description, category_id) 
			VALUES ('$wishClean', '$uniqueUrl','$descriptionClean','$wishCategoryClean')";
		$mysqli->query($query);
	}



	public static function updateItem($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];

		if(isset($_POST['updateBtn'])){
			$wishNameClean = $mysqli->real_escape_string($_POST['wishName']);
			$wishDescriptionClean = $mysqli->real_escape_string($_POST['wishDescription']);
			$wishIdClean = $mysqli->real_escape_string($_POST['wishId']);
			$wishCategoryIdClean = $mysqli->real_escape_string($_POST['wishCategoryId']);

			

			$query = "UPDATE item
				SET wish='$wishNameClean', description='$wishDescriptionClean', category_id ='$wishCategoryIdClean'
				WHERE id = $wishIdClean
			";
			
			$mysqli->query($query);

		}

		if(isset($_POST['deleteBtn'])){
			$wishClean = $mysqli->real_escape_string($_POST['wishName']);
			$descriptionClean = $mysqli->real_escape_string($_POST['wishDescription']);
			$wishCategoryClean = $mysqli->real_escape_string($_POST['wishCategoryId']);
			$wishIdClean = $mysqli->real_escape_string($_POST['wishId']);
			$uniqueUrlClean = $mysqli->real_escape_string($uniqueUrl);

			$query = 
				"INSERT INTO deletedItem
				(id, wish, list_unique_string, description, category_id) 
				VALUES ('$wishIdClean','$wishClean', '$uniqueUrl','$descriptionClean','$wishCategoryClean')
				";
			$mysqli->query($query);

			$query = 
				"DELETE FROM item
					WHERE item.id = $wishIdClean";
			$mysqli->query($query);


				
		}
return ['redirect' => "?/wishList/getList/$uniqueUrl"];
	}
}