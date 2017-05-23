<?php
    ini_set("display_errors", 1);
	session_start();
	
	// Подтягиваем функции
	require_once "apps/php/libmail.php";
	require_once "apps/php/TextGame.class.php";

	// Ajax контроллер
	if (isset($_POST["ajax"])) {
		require_once "apps/co_ajax.php";
	} else {
		require_once "apps/co_main.php";
	}
?>
