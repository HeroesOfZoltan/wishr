<?php

class Wish{

	function __construct($listId, $wish){

		$mysqli = DB::getInstance();
			$listIdClean = $mysqli->real_escape_string($listId);
			$wishClean = $mysqli->real_escape_string($wish);
		
			$query = "INSERT INTO item 
				(wish, list_id) 
				VALUES ('$wishClean', '$listIdClean')
			";

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