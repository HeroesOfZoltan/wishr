<?php

class Sql {

	private static function arrayResult($query) {
		$mysqli = DB::getInstance();

		if($result = $mysqli->query($query)){
			 while($row = $result->fetch_assoc()){
			 	$arrayResult[] = $row;
			 	}
			}
		return $arrayResult;
	}

	public static function category() {
		$query = "SELECT * FROM category";
		return Self::arrayResult($query);
	}

	public static function insertNewItem($wishClean, $uniqueUrl,$descriptionClean,$wishCategoryClean, $wishPrioClean, $wishCostClean,$wishBlacklistClean){
		$mysqli = DB::getInstance();
		$query = 
				"INSERT INTO item 
				(wish, list_uniqueString, description, category_id, prio, cost, blacklist) 
				VALUES ('$wishClean', '$uniqueUrl','$descriptionClean','$wishCategoryClean', '$wishPrioClean', '$wishCostClean','$wishBlacklistClean')";

		$mysqli->query($query);	
	}
	public static function updateItem($wishClean,$descriptionClean,$wishIdClean,$wishCategoryIdClean,$wishPrioClean,$wishCostClean){
		$mysqli = DB::getInstance();
		$query = 
					"UPDATE item
					SET wish='$wishClean', description='$descriptionClean', category_id ='$wishCategoryIdClean',
					prio='$wishPrioClean', cost='$wishCostClean'
					WHERE id = $wishIdClean
					";
			
			$mysqli->query($query);

	}

		public static function deleteItem($wishIdClean,$wishClean, $uniqueUrl,$descriptionClean,$wishCategoryIdClean,$checkedByClean,$prioClean,$costClean,$blacklistClean){
			$mysqli = DB::getInstance();
			$query = 
					"INSERT INTO deletedItem
					(id, wish, list_uniqueString, description, category_id, checkedBy, prio, cost, blacklist) 
					VALUES ('$wishIdClean','$wishClean', '$uniqueUrl','$descriptionClean','$wishCategoryIdClean',
						'$checkedByClean','$prioClean','$costClean','$blacklistClean')
					";
			$mysqli->query($query);

			$query = 
					"DELETE FROM item
					WHERE item.id = $wishIdClean";

			$mysqli->query($query);

	}

	public static function getListItems($uniqueUrl, $userId){
		 	$userPerm[] = $_SESSION["userPermission"];
		if(in_array(1, $userPerm ) || in_array(3, $userPerm)){
		 	$query = 
		 		"SELECT wish, category_id, description, checkedBy, prio, cost, blacklist, categoryName, item.id as 'itemId'
				FROM list, item, category
				WHERE list.uniqueString = item.list_uniqueString
				AND item.category_id = category.id
				AND list.uniqueString = '$uniqueUrl'
				AND list.user_id = $userId
				AND item.blacklist = 0
				ORDER BY item.prio is null, item.prio = 0, item.prio asc, item.id";
			}
		else{
			$query = 
				"SELECT wish, category_id, description, checkedBy, prio, cost, blacklist, categoryName, item.id as 'itemId'
				FROM list, item, category
				WHERE list.uniqueString = item.list_uniqueString
				AND item.category_id = category.id
				AND list.uniqueString = '$uniqueUrl'
				AND list.user_id = $userId
				AND item.blacklist = 0
				ORDER BY item.prio is null, item.prio = 0, item.prio asc, item.id
				LIMIT 20";
		}

		
			return Self::arrayResult($query);
		 }
//ersätta * med det viktiga!
public static function getBlackListItems($uniqueUrl, $userId){
	
			$query = 
				"SELECT item.wish, item.description, item.id as 'itemId', item.blacklist
				FROM list, item
				WHERE list.uniqueString = item.list_uniqueString
				AND list.uniqueString = '$uniqueUrl'
				AND list.user_id = $userId
				AND item.blacklist = 1
				ORDER BY item.prio is null, item.prio = 0, item.prio asc, item.id";
		
			return Self::arrayResult($query);
		 }	 

	public static function getListImage($uniqueUrl){
			$mysqli = DB::getInstance();
			$query = 
				"SELECT imageUrl
				FROM list
				WHERE list.uniqueString = '$uniqueUrl'
				LIMIT 1";
		
			$result = $mysqli->query($query);
			$imageUrl = $result->fetch_assoc();

			return $imageUrl;
		 }	
		 
// lägga till 
		 //
	public static function getListItemsGuest($uniqueUrl) {
		$query = 
			"SELECT item.wish, item.description, item.blacklist, category.categoryName, item.id as itemId, 
			list.uniqueString as uniqueUrl, user.role, item.checkedBy, item.cost, list.listName, list.firstName, list.secondName, list.listIcon
			FROM list, item, category, user
			WHERE category.id = item.category_id
			AND item.list_uniqueString = list.uniqueString
			AND list.user_id = user.id
			AND list.uniqueString = '$uniqueUrl' 
			AND item.blacklist = 0
			ORDER BY item.prio is null, item.prio = 0, item.prio asc, item.id
			";		

		return Self::arrayResult($query);
	}

		public static function getBlacklistItemsGuest($uniqueUrl) {
		$query = 
			"SELECT item.wish, item.description, item.blacklist, item.id as itemId, 
			list.uniqueString as uniqueUrl, user.role, item.checkedBy, item.cost
			FROM list, item, user
			WHERE item.list_uniqueString = list.uniqueString
			AND list.user_id = user.id
			AND list.uniqueString = '$uniqueUrl' 
			AND item.blacklist = 1
			ORDER BY item.prio is null, item.prio = 0, item.prio asc, item.id
			";		

		return Self::arrayResult($query);
	}

	public static function getListInfo($uniqueUrl) {
		$query =
			"SELECT listName, firstName, secondName, listIcon
			FROM list
			WHERE list.uniqueString = '$uniqueUrl'
			LIMIT 1";

		return Self::arrayResult($query);
	}
	
	public static function getListName($uniqueUrl) {
		$query =
			"SELECT listName
			FROM list
			WHERE list.uniqueString = '$uniqueUrl'
			LIMIT 1";

		return Self::arrayResult($query);
	}
	
	public static function setListName($newListName, $uniqueUrl) {
		$mysqli = DB::getInstance();
		$query =
				"UPDATE list
				SET listName='$newListName'
				WHERE uniqueString = '$uniqueUrl'";
		$mysqli->query($query);
	}

	public static function checkUserName($email) {
		$mysqli = DB::getInstance();

		$query = 
			"SELECT email
			FROM user
			WHERE email = '$email'";

		$result = $mysqli->query($query);
		
		if($row = $result->fetch_assoc()){
				$exists = TRUE;
			}
		else {
			$exists = NULL;
		}
		return $exists;
	}

	public static function insertUser($pass, $email, $first, $last, $role) {
		$mysqli = DB::getInstance();

		$exists = Self::checkUserName($email);
		
		if($exists === TRUE){
			return 'Exists';
		}
		else {
			$query = 
				"INSERT INTO user
				(password, email, firstName, lastName)
				VALUES ('$pass','$email','$first','$last')";
			$mysqli->query($query);

			return 'NewUserCreated';
		}
	}
	
	public static function logIn($user, $pass){
		$mysqli = DB::getInstance();
		$query = 
			"SELECT id, role
			FROM user
			WHERE email = '$user'
			AND password = '$pass'
			LIMIT 1";
		$result = $mysqli->query($query);
		$user = $result->fetch_assoc();

		return $user;
	}

	public static function getUserPermission($userId){
		$mysqli = DB::getInstance();
		$query = 
				"SELECT permission_id
				FROM user, user_permission
				WHERE user.id = user_permission.user_id
				AND id = $userId";

		$arrays = Self::arrayResult($query);

		if($arrays){
			foreach($arrays as $row ) {
		       foreach($row as $k['permission_id'] => $v ) {
		            $userPermission[] = $v;
		       }
			}
		}
		
		return  $userPermission;
	}

	public static function setUserGuestPermission($uniqueUrl) {
		$query = 
			"SELECT user_permission.permission_id
			FROM user_permission, list
			WHERE list.user_id = user_permission.user_id
			AND list.uniqueString = '$uniqueUrl'
			";

		$arrays =  Self::arrayResult($query);

		if($arrays){
		foreach($arrays as $row ) {
	       	foreach($row as $k['permission_id'] => $v ) {
	            $userPermission[] = $v;
	       }
		}
	}
		$_SESSION['userPermission'] =  $userPermission;
	}

	public static function setUniqueUrl($userId) {
		$mysqli = DB::getInstance();
		$query = 
			"SELECT list.uniqueString as uniqueUrl
			FROM list
			WHERE list.user_id = '$userId'
			LIMIT 1";

			if($result = $mysqli->query($query)){
		 		$item = $result->fetch_assoc();

			 	$_SESSION['uniqueUrl'] = $item['uniqueUrl'];
			}			
	}

	public static function insertNewList($firstName, $secondName, $uniqueUrl, $userId, $icon){
		$mysqli = DB::getInstance();
		
			$query =
				"INSERT INTO list 
				(firstName, secondName, user_id, uniqueString, imageUrl, listIcon, listName) 
				VALUES ('$firstName','$secondName', '$userId', '$uniqueUrl', 'flowers.jpg','$icon', 'Please add a listname!')";

			$mysqli->query($query);
	}

	public static function insertUserPermission($id){
		$mysqli = DB::getInstance();
		$permissionTypeClean = $mysqli->real_escape_string($_POST['permissionType']);
		$idClean = $mysqli->real_escape_string($id);

		$query = 
				"INSERT INTO user_permission (user_id, permission_id)
				VALUES ($idClean, $permissionTypeClean)";

		$mysqli->query($query);
	}

	public static function itemDone($itemId, $checkedBy){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE item
				SET checkedBy = '$checkedBy'
				WHERE id = $itemId";

		$mysqli->query($query);
	}

	public static function itemUnDone($itemId){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE item
				SET checkedBy = NULL
				WHERE id = $itemId";

		$mysqli->query($query);
	}

	public static function getDashBoard() {

		$mysqli = DB::getInstance();
		$dashArray=[];
		$query = 
				"SELECT COUNT(id) as lists
				FROM list
				LIMIT 1";

		$result = $mysqli->query($query);
		$dashArray['lists'] = $result->fetch_assoc();
		$query = 
				"SELECT COUNT(id) as users
				FROM user
				LIMIT 1";
		
		$result = $mysqli->query($query);
		$dashArray['users'] = $result->fetch_assoc();			
		$query = 
				"SELECT COUNT(distinct user_id) as customers
				FROM user_permission"; 
		
		$result = $mysqli->query($query);
		$dashArray['customers'] = $result->fetch_assoc();

		$query =
				"SELECT permission_id as permission, count(permission_id) as number_of_permissions
				FROM user_permission, user
				WHERE user_permission.user_id = user.id
				GROUP BY permission_id";
				
		$dashArray['permissions'] = Self::arrayResult($query);

		return $dashArray;
	}

	public static function insertNewCategory($categoryName) {
		$mysqli = DB::getInstance();

		$query=
				"INSERT INTO category (categoryName)
				VALUES('$categoryName')";

		$mysqli->query($query);
	}

	public static function deleteCategory($category) {
		$mysqli = DB::getInstance();

		$query = 
				"DELETE FROM category
				WHERE id = '$category'";

		$mysqli->query($query);	
	}

	public static function updateListName($uniqueUrl, $newNameFirst, $newNameSecond){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE list
				SET firstName = '$newNameFirst', secondName = '$newNameSecond'
				WHERE uniqueString = '$uniqueUrl'";

		$mysqli->query($query);
	}

	public static function updateListIcon($uniqueUrl, $listIcon){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE list
				SET listIcon = '$listIcon'
				WHERE uniqueString = '$uniqueUrl'";

		$mysqli->query($query);
	}

	public static function updateListImage($uniqueUrl, $newImage){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE list
				SET imageUrl = '$newImage'
				WHERE uniqueString = '$uniqueUrl'";
				
		$mysqli->query($query);
	}

	public static function getUserInfo($userId){
		$query =
			"SELECT firstName, lastName, email
			FROM user
			WHERE user.id = $userId
			LIMIT 1";

		return Self::arrayResult($query);
	}

	public static function updateUserInfo($id, $firstName, $lastName, $email){
		$mysqli = DB::getInstance();
		
		$query = 
				"UPDATE user
				SET firstName = '$firstName', lastName = '$lastName', email = '$email'
				WHERE id = $id";

		$mysqli->query($query);

	}
}