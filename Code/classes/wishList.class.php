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

//Tar emot ett id och kollar om det finns tillsammans med en inloggad user och skriver då ut aktuell lista
	public static function getList($params){
		$mysqli = DB::getInstance();
		$uniqueUrl = $params[0];
		return ['items' => Sql::getListItems($uniqueUrl, $_SESSION['user']['id']), 'categories' => Sql::category(), 'listInfo' => Sql::getListInfo($_SESSION['uniqueUrl']), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
	}

	public static function mylist(){
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
		$uniqueUrl = $params[0];
		$wish = new Wish($uniqueUrl, $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'], $_POST['prio'],
				$_POST['cost'], NULL);
		return ['redirect' => "?/wishList/getList/$uniqueUrl"];
	}

	public static function addBlacklistItem($params){
		$uniqueUrl = $params[0];		
		$wish = new Wish($uniqueUrl, $_POST['wishName'], $_POST['wishDescription'], $_POST['wishCategory'],NULL,NULL, $_POST['blacklist']);
		return ['redirect' => "?/wishList/getBlacklist/$uniqueUrl"];
	}

	public static function newListName($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];
		$listNameClean = $mysqli->real_escape_string($_POST['newListName']);
		Sql::setListName($listNameClean, $uniqueUrl);

		return['redirect' => '?/User/payUp/#pageContent1'];

	}

	public static function changeListName($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];
		$newListNameFirstClean= $mysqli->real_escape_string($_POST['newListNameFirst']);
		$newListNameSecondClean= $mysqli->real_escape_string($_POST['newListNameSecond']);
		Sql::updateListName($uniqueUrl, $newListNameFirstClean,$newListNameSecondClean);

		return ['redirect' => "?/User/payUp/#pageContent1"];
	}

	public static function changeListIcon($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];
		$iconClean= $mysqli->real_escape_string($_POST['icon']);
		Sql::updateListIcon($uniqueUrl, $iconClean);

		return ['redirect' => "?/User/payUp/#pageContent1"];
	}

	public static function changeListImage($params){
		$mysqli = DB::getInstance();
		$uniqueUrl = $params[0];

		if(isset($_POST['originalImage'])){
			Sql::updateListImage($uniqueUrl, 'flowers.jpg');
		}
		else{
			$newListImageClean= $mysqli->real_escape_string($_POST['newListImage']);
			Sql::updateListImage($uniqueUrl, $newListImageClean);
		}
		return ['redirect' => "?/User/payUp/#pageContent2"];
	}

	public static function getBlacklist() {		
		return ['blacklist' => TRUE, 'items' => Sql::getBlackListItems($_SESSION['uniqueUrl'], $_SESSION['user']['id']),'categories' =>Sql::category(), 'imageUrl' => Sql::getListImage($_SESSION['uniqueUrl'])];
	}

}