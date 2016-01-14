<?php

class Wish{
//Tvättar och sparar ner ett önskeobjekt till databasen
	function __construct($uniqueUrl, $wish, $description, $wishCategory, $wishPrio="", $wishCost="", $wishBlacklist=""){

		$mysqli = DB::getInstance();
		//$uniqueIdClean = $mysqli->real_escape_string($uniqueUrl);
		$wishClean = $mysqli->real_escape_string($wish);
		//clean([&$wish,$description]);
		$descriptionClean = $mysqli->real_escape_string($description);
		$wishCategoryClean = $mysqli->real_escape_string($wishCategory);
		$wishPrioClean = $mysqli->real_escape_string($wishPrio);
		$wishCostClean = $mysqli->real_escape_string($wishCost);
		$wishBlacklistClean = $mysqli->real_escape_string($wishBlacklist);

		$query = 
			"INSERT INTO item 
			(wish, list_unique_string, description, category_id, prio, cost, blacklist) 
			VALUES ('$wishClean', '$uniqueUrl','$descriptionClean','$wishCategoryClean', '$wishPrioClean', '$wishCostClean','$wishBlacklistClean')";
		$mysqli->query($query);
	}



	public static function updateItem($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];

		$wishClean = $mysqli->real_escape_string($_POST['wishName']);
		$descriptionClean = $mysqli->real_escape_string($_POST['wishDescription']);
		$wishIdClean = $mysqli->real_escape_string($_POST['wishId']);
		$wishCategoryIdClean = $mysqli->real_escape_string($_POST['wishCategoryId']);
		$wishPrioClean = $mysqli->real_escape_string($_POST['prio']);
		$wishCostClean = $mysqli->real_escape_string($_POST['cost']);

		if(isset($_POST['updateBtn'])){

			$query = 
					"UPDATE item
					SET wish='$wishClean', description='$descriptionClean', category_id ='$wishCategoryIdClean',
					prio='$wishPrioClean', cost='$wishCostClean'
					WHERE id = $wishIdClean
					";
			
			$mysqli->query($query);
		}

		if(isset($_POST['deleteBtn'])){

			$query = 
					"INSERT INTO deletedItem
					(id, wish, list_unique_string, description, category_id) 
					VALUES ('$wishIdClean','$wishClean', '$uniqueUrl','$descriptionClean','$wishCategoryIdClean')
					";
			$mysqli->query($query);

			$query = 
					"DELETE FROM item
					WHERE item.id = $wishIdClean";

			$mysqli->query($query);
		}


		if(isset($_POST['toBlacklist'])){
			return ['redirect' => "?/User/getBlacklist/$uniqueUrl"];
		}
		else{
			return ['redirect' => "?/wishList/getList/$uniqueUrl"];
		}

	}
}