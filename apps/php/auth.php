<?php
class auth {
	public static function securityCheck() {
		if (isset($_SESSION["user_id"]) && isset($_SESSION["user_token"])) {
			$query = "SELECT user_token FROM users WHERE user_id = ".$user_id;
			$result = dbconn::query($query);
			base::sprint($result);
		} else {
			return false;
		}
	}
	public static function logIn($login, $pass) {
		// Фильтрация
	    $login = trim($login);
	    $login = strip_tags($login);
        $login = htmlspecialchars($login);
		$pass = trim($pass);
		$pass = strip_tags($pass);
        $pass = htmlspecialchars($pass);
        
		// Проверка введенных данных
		if (!$login) return ["status" => 0, "result" => "Не указан логин!"];
		if (mb_strlen($login, "UTF-8") < 6) return ["status" => 0, "result" => "Логин должен содержать не менее 6 символов."];
		if (!$pass) return ["status" => 0, "result" => "Не указан пароль!"];
		if (mb_strlen($pass, "UTF-8") < 6) return ["status" => 0, "result" => "Пароль должен содержать не менее 6 символов."];
		
		// Обращение к БД, берем пароль и код
		$query = "
			SELECT *
			FROM users
			WHERE user_login = '".$login."';";
		$result = dbconn::query($query);
		if ($result["status"] && isset($result["data"][0]["user_pass"])) {
			$salt = substr($result["data"][0]["user_pass"], 0, 10);
			$truePass = $result["data"][0]["user_pass"];
			$pwd = $salt.md5($salt.$pass);
			if ($truePass == $pwd) {
				$_SESSION["user_id"] = $result["data"][0]["user_id"];
    			$_SESSION["user_token"] = $result["data"][0]["user_token"];

				return ["status" => 1, "result" => "Добро пожаловать", "data" => $result["data"][0]];
			} else {
				return ["status" => 0, "result" => "Неверное имя пользователя или пароль"];
			}
		} else {
			return ["status" => 0, "result" => "Пользователь не зарегистрирован"];
		}
	}
	
	public static function regAcc($login, $pass) {
	    // Фильтрация
	    $login = trim($login);
	    $login = strip_tags($login);
        $login = htmlspecialchars($login);
		$pass = trim($pass);
		$pass = strip_tags($pass);
        $pass = htmlspecialchars($pass);
		
		// Проверка введенных данных
		if (!$login) return ["status" => 0, "result" => "Не указан логин!"];
		if (mb_strlen($login, "UTF-8") < 6) return ["status" => 0, "result" => "Логин должен содержать не менее 6 символов."];
		if (!$pass) return ["status" => 0, "result" => "Не указан пароль!"];
		if (mb_strlen($pass, "UTF-8") < 6) return ["status" => 0, "result" => "Пароль должен содержать не менее 6 символов."];
		if (self::checkLogin($login)) return ["status" => 1, "result" => "Логин используется."];
		
		// Создание пароля
		$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
		$numChars = strlen($chars);
		$salt = '';
		for ($i = 0; $i < 10; $i++) {
			$salt .= substr($chars, rand(1, $numChars) - 1, 1);
		}
		$pwd = $salt.md5($salt.$pass);
		
		// Создание кода безопасности
		$user_token = md5($salt);
		
		// Запись в бд
		$query = "
			INSERT INTO users (user_login, user_pass, user_token)
			VALUES ('".$login."', '".$pwd."', '".$user_token."')";
		$result = dbconn::query($query);
		
		if ($result["status"]) {
		    // self::logIn($login, $pass);
			return ["status" => 1, "result" => "Вы успешно зарегистрировались."];
		} else {
			return ["status" => 0, "result" => "Произошла ошибка во время регистрации."];
		}
	}
	
	public static function checkLogin($login) {
		$query = "
			SELECT user_id 
			FROM users 
			WHERE user_login = '".$login."'";
		$result = dbconn::query($query);
		if (isset($result["data"][0]["id"])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public static function logOff() {
		unset($_SESSION["user_id"]);
	    unset($_SESSION["user_token"]);
	}
}
?>