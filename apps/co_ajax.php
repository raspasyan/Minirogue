<?php
	// Обрабатываем логику AJAX запросов
	if (isset($_POST["ajax"])) {
		$answer = ["status" => true, "result" => "AJAX_REQUEST_TEST", "data" => $_POST];

		switch($_POST["ajax"]) {
			default: {
				echo(json_encode($answer));
				break;
			}
		}	
	}
?>
