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