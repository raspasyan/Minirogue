<?php
	// POST
	if (isset($_POST["action"])) {
		//
	}
	
	// Подготовка к маршрутизации
	$url = $_SERVER["REQUEST_URI"];
	// Убираем лишнее, экранируем
	$url = mb_strtolower($url, 'UTF-8');
	$url = str_replace("'", "", $url);
	$url = strip_tags($url);
	$url = stripslashes($url);
	$url = trim($url);
	$url = substr($url, 1);
	$endchar = substr($url, -1);
	if ($endchar == "/") $url = substr($url, 0, strlen($url) - 1);
	$url = explode("?",$url)[0];
	// Маршрутизация запроса
	$titleTag = "Текстовая RPG Minirogue";
	$data = [];
	switch($url) {
		default: 		{header("Location: http://".$_SERVER["HTTP_HOST"]."/404"); break;}
		case "": 		{$page = "main.php"; break;}
		case "donation":{$page = "donation.php"; break;}
		case "database":{$page = "database.php"; break;}
		case "404": 	{$titleTag = "404"; $page = "404.php"; break;}
	}

	// Подтягиваем главную вьюху
	if (isset($admin)) {
		//
	} else {
		require_once 'templates/template.php';
	}

	// Дропаем лишние сессии
	unset($_SESSION["success"]);
	unset($_SESSION["error"]);
?>
