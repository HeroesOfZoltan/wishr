<?php


class WishList{

	public static function createList(){
//Skapar ny lista med namn från POST
		if(isset($_POST['listName'])){
			$mysqli = DB::getInstance();//Startar databas uppkoppling
			$listName = $mysqli->real_escape_string($_POST['listName']);//Tvättar input från POST

			Sql::insertNewList($listName); //Anropar metod som sparar ny lista i databasen
//Returnerar array som sedan renderas av Twig
			return ['newList' => TRUE, 'listName' => $listName, 'listId' =>$_SESSION['listId'], 'categories' =>Sql::category(),'user' => $_SESSION['user']];
		}
		return ['newList' => FALSE];
	}

//Tar emot ett id och kollar om det finns tillsammans med en inloggad user och skriver då ut aktuell lista
	public static function getList($params){
		$mysqli = DB::getInstance();

		$listId = $params[0];
		$userId = $_SESSION['user']['id'];

		return ['newList' => TRUE, 'items' => Sql::getListItems($listId, $userId), 'categories' => Sql::category(),'user' => $_SESSION['user'], 'listId' =>$_SESSION['listId'], 'listNames'=> Sql::listName($_SESSION['listId']['listId'])];
	}


//metod för att lägga till ett objekt i en lista
	public static function addItem($params){

		$listId = $_SESSION['listId']['listId'];
		$wish = new Wish($listId, $_POST['wishName'],$_POST['wishDescription'],$_POST['wishCategory'] );
		return ['redirect' => "?/wishList/getList/$listId"];
	}
}