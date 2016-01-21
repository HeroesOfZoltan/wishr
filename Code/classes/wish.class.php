<?php

class Wish{
//Tvättar och sparar ner ett önskeobjekt till databasen
	function __construct($uniqueUrl, $wish, $description, $wishCategory, $wishPrio="", $wishCost="", $wishBlacklist=""){

		$mysqli = DB::getInstance();

		$wishClean = $mysqli->real_escape_string($wish);
		$descriptionClean = $mysqli->real_escape_string($description);
		$wishCategoryClean = $mysqli->real_escape_string($wishCategory);
		$wishPrioClean = $mysqli->real_escape_string($wishPrio);
		$wishCostClean = $mysqli->real_escape_string($wishCost);
		$wishBlacklistClean = $mysqli->real_escape_string($wishBlacklist);

		Sql::insertNewItem($wishClean, $uniqueUrl,$descriptionClean,$wishCategoryClean, $wishPrioClean, $wishCostClean,$wishBlacklistClean);	
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
		$checkedByClean = $mysqli->real_escape_string($_POST['checkedBy']);
		$blacklistClean = $mysqli->real_escape_string($_POST['blacklist']);

		if(isset($_POST['updateBtn'])){
			Sql::updateItem($wishClean,$descriptionClean,$wishIdClean,$wishCategoryIdClean,$wishPrioClean,$wishCostClean);
		}

		if(isset($_POST['deleteBtn'])){
			Sql::deleteItem($wishIdClean,$wishClean, $uniqueUrl,$descriptionClean,$wishCategoryIdClean,$checkedByClean,$wishPrioClean,$wishCostClean,$blacklistClean);
		}


		if(isset($_POST['toBlacklist'])){
			return ['redirect' => "?/wishList/getBlacklist/$uniqueUrl"];
		}
		else{
			return ['redirect' => "?/wishList/getList/$uniqueUrl"];
		}

	}

		public static function itemDone($params) {
		$mysqli = DB::getInstance();
		$itemIdClean = $mysqli->real_escape_string($_POST['itemId']);
		$checkedByClean = $mysqli->real_escape_string($_POST['checkedBy']);

		Sql::itemDone($itemIdClean, $checkedByClean);

		$uniqueUrl = $params[0];
		return ['redirect' => "?/wishList/guestView/$uniqueUrl"];
	}
	public static function unDoneItem($params) {
		$mysqli = DB::getInstance();
		$itemIdClean = $mysqli->real_escape_string($_POST['itemId']);

		Sql::itemUnDone($itemIdClean);
		
		$uniqueUrl = $params[0];
		return ['redirect' => "?/wishList/guestView/$uniqueUrl"];
	}
}