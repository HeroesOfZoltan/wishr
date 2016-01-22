<?php


class WishList{

//Returnerar array med permissions för varje metod. TRUE innebär att en måste vara inloggad för att få anropa den metoden
	public static function check(){

		$methods= ['createList' => TRUE, 'getList' => TRUE, 'addItem' => TRUE, 'addBlacklistItem' => TRUE,
		 'changeListName' => TRUE, 'ChangeListIcon' => TRUE, 'changeListImage' => TRUE, 'getBlacklist' => TRUE, 
		 'guestView' => FALSE];

		return $methods;
	}

//Hämtar allt aktuellt innehåll till en users lista ifall den har en lista, annars returneras en lista utan items
	public static function myList(){		

		$items = Sql::getListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']);
		if($items){
			return ['items' => $items, 'categories' => Sql::category(), 'listInfo' => Sql::getListInfo($_SESSION['uniqueUrl']), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
		}
		else if ($_SESSION['user']['id'])	{
			return ['listInfo' => Sql::getListInfo($_SESSION['uniqueUrl']), 'categories' =>Sql::category(),'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
		}
	}

	//Listvy för en gäst
	public static function guestView($params){
			$mysqli = DB::getInstance();

			$uniqueUrl = $params[0];
			$uniqueUrlClean = $mysqli->real_escape_string($uniqueUrl);

			Sql::setUserGuestPermission($uniqueUrlClean);
			return ['guestListItems' => Sql::getListItemsGuest($uniqueUrlClean), 'guestBlackListItems' => Sql::getBlacklistItemsGuest($uniqueUrlClean),'imageUrl' => Sql::getListImage($uniqueUrlClean), 'listInfo' => Sql::getListInfo($uniqueUrlClean)];

		}

//metod för att lägga till ett objekt i en lista
	public static function addItem(){

		$valid = Sql::checkUser($_SESSION['uniqueUrl'], $_SESSION['user']['id']);


		if($valid == TRUE){
			$wish = new Wish($_SESSION['uniqueUrl'], $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'], $_POST['prio'],
				$_POST['cost'], NULL);
		return ['redirect' => "?/wishList/myList/"];
		}
		else{
			return ['redirect' => "?/wishList/myList/"];
		}
	}

	public static function addBlacklistItem(){	
		
		$valid = Sql::checkUser($_SESSION['uniqueUrl'], $_SESSION['user']['id']);

		if($valid == TRUE){
			$wish = new Wish($_SESSION['uniqueUrl'], $_POST['wishName'], $_POST['wishDescription'], $_POST['wishCategory'],NULL,NULL, $_POST['blacklist']);	
			return ['redirect' => "?/wishList/getBlacklist/"];
		}
		else{
			return ['redirect' => "?/wishList/getBlacklist/"];
		}

		/*$wish = new Wish($uniqueUrl, $_POST['wishName'], $_POST['wishDescription'], $_POST['wishCategory'],NULL,NULL, $_POST['blacklist']);
		return ['redirect' => "?/wishList/getBlacklist/$uniqueUrl"];*/
	}

	public static function newListName(){
		$mysqli = DB::getInstance();

		$listNameClean = $mysqli->real_escape_string($_POST['newListName']);
		Sql::setListName($listNameClean, $_SESSION['uniqueUrl'], $_SESSION['user']['id']);

		return['redirect' => '?/User/payUp/#pageContent1'];
	}

	public static function changeListName(){
		$mysqli = DB::getInstance();

		$newListNameFirstClean= $mysqli->real_escape_string($_POST['newListNameFirst']);
		$newListNameSecondClean= $mysqli->real_escape_string($_POST['newListNameSecond']);
		Sql::updateListName($_SESSION['uniqueUrl'], $newListNameFirstClean,$newListNameSecondClean, $_SESSION['user']['id']);

		return ['redirect' => "?/User/payUp/#pageContent1"];
	}

	public static function changeListIcon(){
		$mysqli = DB::getInstance();

		$iconClean= $mysqli->real_escape_string($_POST['icon']);
		Sql::updateListIcon($_SESSION['uniqueUrl'], $iconClean, $_SESSION['user']['id']);

		return ['redirect' => "?/User/payUp/#pageContent1"];
	}

	public static function changeListImage(){
		$mysqli = DB::getInstance();

		if(isset($_POST['originalImage'])){
			Sql::updateListImage($_SESSION['uniqueUrl'], 'flowers.jpg', $_SESSION['user']['id']);
		}
		else{
			$newListImageClean= $mysqli->real_escape_string($_POST['newListImage']);
			Sql::updateListImage($_SESSION['uniqueUrl'], $newListImageClean, $_SESSION['user']['id']);
		}
		return ['redirect' => "?/User/payUp/#pageContent2"];
	}

	public static function getBlacklist() {		
		return ['blacklist' => TRUE, 'items' => Sql::getBlackListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']),'categories' =>Sql::category(), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
	}

}