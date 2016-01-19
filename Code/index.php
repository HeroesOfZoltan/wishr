<?php
session_start();
require_once("classes/DB.class.php");
require_once("classes/wish.class.php");
require_once("classes/User.class.php");
require_once("classes/sql.class.php");

if(isset($_POST['killSession'])){
	session_unset();

}

error_reporting(0);

//anropar getUrlParts och skickar in url. url_parts blir en array med uppstyckad url. 
$url_parts = getUrlParts($_GET); 

//var_dump($url_parts);
//array_shift lägger in första värdet i $class osv.
if($url_parts!= null){
	$class = array_shift($url_parts); 
	$method = array_shift($url_parts);

//skickar in class och anropar dess statiska metod.
	require_once("classes/".$class.".class.php"); 
	$data = $class::$method($url_parts);
	$data['_session'] = $_SESSION;


	if($method ==  'myList' || $method ==  'getList'){
		$template = 'myList.html';
		if( count($data["items"])<20|| in_array(1, $_SESSION["userPermission"]) || in_array(3, $_SESSION["userPermission"])){
			$data['payment'] = "newWishForm.html";
		}
		else{
			$data['payment'] = "paymentInfo.html";
		}
	}

	elseif($method ==  'payUp'){
		$template = 'payUp.html';

		if( $_SESSION["user"]){
				if(in_array(1, $_SESSION["userPermission"]) || in_array(3, $_SESSION["userPermission"])){
						$data['unlimitedForm'] = "payedUnlimited.html";
				}
				else{
						$data['unlimitedForm'] = "unpayedUnlimited.html";
				}
				if(in_array(1, $_SESSION["userPermission"]) || in_array(2, $_SESSION["userPermission"])){
						$data['blacklistForm'] = "payedBlacklist.html";
				}
				else{
						$data['blacklistForm'] = "unpayedBlacklist.html";
				}
				if(in_array(1, $_SESSION["userPermission"]) || in_array(5, $_SESSION["userPermission"])){
						$data['listImage'] = "payedChangeBg.html";
				}
				else{
						$data['listImage'] = "unpayedChangeBg.html";
				}
				if(in_array(1, $_SESSION["userPermission"]) || in_array(4, $_SESSION["userPermission"])){
						$data['doneForm'] = "payedDonedidit.html";
				}
				else{
						$data['doneForm'] = "unpayedDonedidit.html";
				}
				
				$data['payView'] = "paymentForm.html";
			}
		else{
				$data['payView'] = "ourProduct.html";
		}
	}

	elseif($method ==  'getBlacklist'){
		if(in_array(1, $_SESSION["userPermission"]) || in_array(2, $_SESSION["userPermission"])){
				$template = 'blacklist.html';
				$data['payView'] = "paymentForm.html";
		}
		else{
			$data['redirect']= "?/User/payUp/";
		}
	}

	elseif($method ==  'createUser'){
		$template = 'login.html';
	}

	elseif($method ==  'guestView'){
		$template = 'guestView.html';

		if( in_array(1, $_SESSION["userPermission"]) || in_array(4, $_SESSION["userPermission"])){
			$data['guestDonelist'] = "guestDonelist.html";

			$data['guestDoneForm'] = "guestDoneForm.html";
		}
		if( in_array(1, $_SESSION["userPermission"]) || in_array(2, $_SESSION["userPermission"])){
			$data['guestBlacklist'] = "guestBlacklist.html";
		}
	}


	elseif($method ==  'adminDash' AND $_SESSION['user']['role'] == 1 ){
		$template = 'adminDash.html';
	}


//redirectar sidan till valt destination.
	if(isset($data['redirect'])){
		header("Location: ".$data['redirect']);
	}

}
else{
	$template = 'login.html';
	$data= array();//Här kan vi lägga t ex statestik om sidan som ska visas på förstasidan
}

//var_dump($data);
$twig = startTwig();
echo $twig->render($template, $data);


function getUrlParts($get){
	$get_params = array_keys($get);//plockar key värden ur get-arrayen
	$url = $get_params[0];
	$url_parts = explode("/",$url);
	foreach($url_parts as $k => $v){
		if($v) $array[] = $v;
	}
	$url_parts = $array;
	return $url_parts; 
}

function startTwig(){
	require_once('Twig/lib/Twig/Autoloader.php');
	Twig_Autoloader::register();
	$loader = new Twig_Loader_Filesystem('templates/');
	return $twig = new Twig_Environment($loader);
}

/*function clean(&$var) {
	$mysqli = DB::getInstance();


	if (is_array($var)) {
		foreach($var as $key => $val) {
			$mysqli->real_escape_string($val);
		}
	}
	else{
		$mysqli->real_escape_string($var);
	}
	//$_SESSION['test'] = 'test';
}
*/



