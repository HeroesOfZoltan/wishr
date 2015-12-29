<?php

class Wish{
//Tvättar och sparar ner ett önskeobjekt till databasen
	function __construct($listId, $wish, $description, $wishCategory){

		$mysqli = DB::getInstance();
		$listIdClean = $mysqli->real_escape_string($listId);
		$wishClean = $mysqli->real_escape_string($wish);
		$descriptionClean = $mysqli->real_escape_string($description);
		$wishCategoryClean = $mysqli->real_escape_string($wishCategory);
		$query = 
			"INSERT INTO item 
			(wish, list_id, description, category_id) 
			VALUES ('$wishClean', '$listIdClean','$descriptionClean','$wishCategoryClean')";
		$mysqli->query($query);
	}
/*
	function updateItem($wishId,$wishName){
		$mysqli = DB::getInstance();
		$itemIdClean = $mysqli->real_escape_string($wishId);
		$wishTxtClean = $mysqli->real_escape_string($wishName);

		$query = "UPDATE item
			(wish)
			VALUES ('$wishTxtClean')
			WHERE id = $itemIdClean
		";


	}*/
}