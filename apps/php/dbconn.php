<?php
class dbconn {
	private static $db = null;
	
	public static function getConnect() {
		if (self::$db == null) {
			self::$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
			self::$db->set_charset('utf8');
			if (mysqli_connect_errno(self::$db)) {
				return false;
			}
		}
		
		return self::$db;
	}

	public static function query($query) {
        $db = self::getConnect();
		if (!$db) {
			die();
		}
        
		// Безопасность
		$db->real_escape_string($query);
        
		$res = mysqli_query($db, $query);
        if ($res) {
            $result = [];
			if (gettype($res) == "boolean") {
				return ["status" => true, "result" => "SQL_SUCCESS"];
			} else {
				while ($row = mysqli_fetch_assoc($res)) {
					$result[] = $row;
				}
                
                return ["status" => true, "result" => "SQL_SUCCESS", "data" => $result];
			}
        } else {
            return ["status" => false, "result" => "SQL_ERROR"];
        }
	}
}
?>