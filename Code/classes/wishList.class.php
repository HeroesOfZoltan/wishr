<?php


class WishList{

	public static function createList(){
//Skapar ny lista med namn från POST
		if(isset($_POST['listName'])){
			$mysqli = DB::getInstance();//Startar databas uppkoppling
			$listName = $mysqli->real_escape_string($_POST['listName']);//Tvättar input från POST
			$uniqueString = substr(md5(microtime()),rand(0,26),5); //genererar unik sträng på 5 tecken.

			Sql::insertNewList($listName, $uniqueString); //Anropar metod som sparar ny lista i databasen
//Returnerar array som sedan renderas av Twig
			return ['newList' => TRUE, 'listName' => $listName, 'uniqueUrl' =>$_SESSION['uniqueUrl'], 'categories' =>Sql::category(),'user' => $_SESSION['user']];
		}
		return ['newList' => FALSE];
	}

//Tar emot ett id och kollar om det finns tillsammans med en inloggad user och skriver då ut aktuell lista
	public static function getList($params){
		$mysqli = DB::getInstance();

		$uniqueUrl = $params[0];

		$userId = $_SESSION['user']['id'];

		return ['newList' => TRUE, 'items' => Sql::getListItems($uniqueUrl, $userId), 'categories' => Sql::category(),'user' => $_SESSION['user'], 'uniqueUrl' => $uniqueUrl, 'listNames'=> Sql::listName($uniqueUrl)];
	}


//metod för att lägga till ett objekt i en lista
	public static function addItem($params){

		$uniqueUrl = $params[0];
		
		$wish = new Wish($uniqueUrl, $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'], $_POST['prio'],
				$_POST['cost']);
		return ['redirect' => "?/wishList/getList/$uniqueUrl"];
	}
}