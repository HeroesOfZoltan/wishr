<?php


class WishList{

//Returnerar array med permissions för varje metod. TRUE innebär att en måste vara inloggad för att få anropa den metoden
	public static function check(){

		$methods= ['createList' => TRUE, 'getList' => TRUE, 'addItem' => TRUE, 'addBlacklistItem' => TRUE,
		 'changeListName' => TRUE, 'ChangeListIcon' => TRUE, 'changeListImage' => TRUE, 'getBlacklist' => TRUE, 
		 'guestView' => FALSE];

		return $methods;
	}


//Skriver ut aktuell lista ifall user id och listans unika url i session kan matchas i databasen
	public static function getList($params){
		$mysqli = DB::getInstance();

		return ['items' => Sql::getListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']), 'categories' => Sql::category(), 'listInfo' => Sql::getListInfo($_SESSION['uniqueUrl']), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
	}

//Hämtar allt aktuellt innehåll till en users lista ifall den har en lista, annars returneras en lista utan items
	public static function myList(){
		$_SESSION['userPermission'] = Sql::getUserPermission($_SESSION['user']['id']);
		Sql::setUniqueUrl($_SESSION['user']['id']);

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
	public static function addItem($params){

		$valid = Sql::checkUser($_SESSION['uniqueUrl'], $_SESSION['user']['id']);


		if($valid == TRUE){
			$wish = new Wish($_SESSION['uniqueUrl'], $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'], $_POST['prio'],
				$_POST['cost'], NULL);
		return ['redirect' => "?/wishList/getList/"];
		}
		else{
			return ['redirect' => "?/wishList/getList/"];
		}
	}

	public static function addBlacklistItem($params){
		$uniqueUrl = $params[0];	
		
		$valid = Sql::checkUser($uniqueUrl, $_SESSION['user']['id']);

		if($valid == TRUE){
		$wish = new Wish($uniqueUrl, $_POST['wishName'], $_POST['wishDescription'], $_POST['wishCategory'],NULL,NULL, $_POST['blacklist']);
			
		return ['redirect' => "?/wishList/getBlacklist/$uniqueUrl"];
		}
		else{
		return ['redirect' => "?/wishList/getBlacklist/$uniqueUrl"];
		}

		/*$wish = new Wish($uniqueUrl, $_POST['wishName'], $_POST['wishDescription'], $_POST['wishCategory'],NULL,NULL, $_POST['blacklist']);
		return ['redirect' => "?/wishList/getBlacklist/$uniqueUrl"];*/
	}

	public static function newListName($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];
		$listNameClean = $mysqli->real_escape_string($_POST['newListName']);
		Sql::setListName($listNameClean, $uniqueUrl, $_SESSION['user']['id']);

		return['redirect' => '?/User/payUp/#pageContent1'];

	}

	public static function changeListName($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];
		$newListNameFirstClean= $mysqli->real_escape_string($_POST['newListNameFirst']);
		$newListNameSecondClean= $mysqli->real_escape_string($_POST['newListNameSecond']);
		Sql::updateListName($uniqueUrl, $newListNameFirstClean,$newListNameSecondClean, $_SESSION['user']['id']);

		return ['redirect' => "?/User/payUp/#pageContent1"];
	}

	public static function changeListIcon($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];
		$iconClean= $mysqli->real_escape_string($_POST['icon']);
		Sql::updateListIcon($uniqueUrl, $iconClean, $_SESSION['user']['id']);

		return ['redirect' => "?/User/payUp/#pageContent1"];
	}

	public static function changeListImage($params){
		$mysqli = DB::getInstance();
		$uniqueUrl = $params[0];

		if(isset($_POST['originalImage'])){
			Sql::updateListImage($uniqueUrl, 'flowers.jpg', $_SESSION['user']['id']);
		}
		else{
			$newListImageClean= $mysqli->real_escape_string($_POST['newListImage']);
			Sql::updateListImage($uniqueUrl, $newListImageClean, $_SESSION['user']['id']);
		}
		return ['redirect' => "?/User/payUp/#pageContent2"];
	}

	public static function getBlacklist() {		
		return ['blacklist' => TRUE, 'items' => Sql::getBlackListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']),'categories' =>Sql::category(), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
	}

}