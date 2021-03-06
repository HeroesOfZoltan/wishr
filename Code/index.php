<?php
session_start();
require_once("classes/DB.class.php");
require_once("classes/wish.class.php");
require_once("classes/User.class.php");
require_once("classes/sql.class.php");

if(isset($_POST['killSession'])){
	session_unset();
}

const GetAllPerms = 1;
const Blacklist = 2;
const UltimateWishes = 3;
const DONEDIDIT = 4;
const ChangeImage = 5;

error_reporting(0);

//anropar getUrlParts och skickar in url. url_parts blir en array med uppstyckad url. 
$url_parts = getUrlParts($_GET); 


//array_shift lägger in första värdet i $class osv.
if($url_parts!= null){
	$class = array_shift($url_parts); 
	$method = array_shift($url_parts);

//skickar in class och anropar dess statiska metod.
	require_once("classes/".$class.".class.php"); 

	$access = FALSE;

    if($class != 'sql'){

        $_permissions = $class::check();

        if($_permissions[$method] == TRUE && $_SESSION['user']){
            $access = TRUE;
        }elseif($_permissions[$method] == FALSE){
            $access = TRUE;
        }
    }

	if($access == TRUE){

		$data = $class::$method($url_parts);
		$data['_session'] = $_SESSION;

		if($method ==  'myList' || $method ==  'getList'){
			$template = 'myList.html';

			if( count($data["items"])<20|| in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(UltimateWishes, $_SESSION["userPermission"])){
				$data['wishForm'] = "newWishForm.html";
			}
			else{
				$data['wishForm'] = "paymentInfo.html";
			}
		}

		elseif($method ==  'payUp'){
			$template = 'payUp.html';

			if(in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(UltimateWishes, $_SESSION["userPermission"])){
				$data['unlimitedForm'] = "payedUnlimited.html";
			}
			else{
				$data['unlimitedForm'] = "unpayedUnlimited.html";
			}
			if(in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(Blacklist, $_SESSION["userPermission"])){
				$data['blacklistForm'] = "payedBlacklist.html";
			}
			else{
				$data['blacklistForm'] = "unpayedBlacklist.html";
			}
			if(in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(ChangeImage, $_SESSION["userPermission"])){
				$data['listImage'] = "payedChangeBg.html";
			}
			else{
				$data['listImage'] = "unpayedChangeBg.html";
			}
			if(in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(DONEDIDIT, $_SESSION["userPermission"])){
				$data['doneForm'] = "payedDonedidit.html";
			}
			else{
				$data['doneForm'] = "unpayedDonedidit.html";
			}
					
		}
		elseif($method ==  'ourProduct'){
			$template= "ourProduct.html";
		}

		elseif($method ==  'getBlacklist'){
			if(in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(Blacklist, $_SESSION["userPermission"])){
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
			if( in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(DONEDIDIT, $_SESSION["userPermission"])){
				$data['guestDonelist'] = "guestDonelist.html";

				$data['guestDoneForm'] = "guestDoneForm.html";
			}
			if( in_array(GetAllPerms, $_SESSION["userPermission"]) || in_array(Blacklist, $_SESSION["userPermission"])){
				$data['guestBlacklist'] = "guestBlacklist.html";
			}
		}
		elseif($method ==  'adminDash' AND $_SESSION['user']['role'] == GetAllPerms ){
			$template = 'adminDash.html';
		}

	}	//ends access if
	else{
		$template = 'login.html';
		$data = array();
	}

//redirectar sidan till valt destination.
	if(isset($data['redirect'])){
		header("Location: ".$data['redirect']);
	}
}	//ends if url_parts

else{
	$template = 'login.html';
	$data= array();//Här kan vi lägga t ex statestik om sidan som ska visas på förstasidan
}

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