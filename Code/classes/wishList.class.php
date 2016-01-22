<?php


class WishList{


	public static function check(){

		$methods= ['createList' => TRUE, 'getList' => TRUE, 'addItem' => TRUE, 'addBlacklistItem' => TRUE,
		 'changeListName' => TRUE, 'ChangeListIcon' => TRUE, 'changeListImage' => TRUE, 'getBlacklist' => TRUE, 
		 'guestView' => FALSE];

		return $methods;
	}

	public static function createList(){
//Skapar ny lista med namn från POST
		if(isset($_POST['listName'])){
			$mysqli = DB::getInstance();//Startar databas uppkoppling
			$listName = $mysqli->real_escape_string($_POST['listName']);//Tvättar input från POST
			$uniqueString = substr(md5(microtime()),rand(0,26),5); //genererar unik sträng på 5 tecken.

			Sql::insertNewList($listName, $uniqueString); //Anropar metod som sparar ny lista i databasen
//Returnerar array som sedan renderas av Twig
			return ['newList' => TRUE, 'listName' => $listName, 'categories' =>Sql::category()];
		}
		return ['newList' => FALSE];
	}



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
	public static function addItem($params){

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

	public static function addBlacklistItem($params){	
		
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

		$iconClean= $mysqli->real_escape_string($_POST['icon']);
		Sql::updateListIcon($_SESSION['uniqueUrl'], $iconClean, $_SESSION['user']['id']);

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