<?php
session_start();
require_once("classes/DB.class.php");
require_once("classes/wish.class.php");
require_once("classes/User.class.php");
require_once("classes/sql.class.php");

if(isset($_POST['killSession'])){
	session_unset();

}
error_reporting();
//anropar getUrlParts och skickar in url. url_parts blir en array med uppstyckad url. 
$url_parts = getUrlParts($_GET); 

//array_shift lägger in första värdet i $class osv.
if($url_parts!= null){
	$class = array_shift($url_parts); 
	$method = array_shift($url_parts);

//skickar in class och anropar dess statiska metod.
	require_once("classes/".$class.".class.php"); 
	$data = $class::$method($url_parts);
	//$data['_session'] = $_SESSION;

//var_dump($data);
print_r($_SESSION);

//redirectar sidan till valt destination.
	if(isset($data['redirect'])){
		header("Location: ".$data['redirect']);
	}
}
else{
	$data= array(1,2,3);//för att inte det ska bli error
}

$twig = startTwig();

$template = 'index.html';
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

function clean(&$var) {
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
//print_r($data); /*För felsökning av arrayen som skickas till Twig */