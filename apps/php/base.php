<?php
class base {
	// Форматированный вывод инфы
	public static function sprint($arg, $depth = 0) {
		if (is_array($arg)) {
			foreach($arg AS $key => $value) {
				if (is_array($arg[$key]))  {
					echo("<div style='margin-left:" . $depth * 10 . "px; color: White; background: Black;'><span style='font-weight: bold; color: Red'>" . $key . "</span> : ");
						self::sprint($arg[$key], $depth + 1);
					echo("</div>");
				} else {
					echo("<div style='margin-left:" . $depth * 10 . "px; color: White; background: Black;'><span style='font-weight: bold; color: SkyBlue'>" . $key . "</span> : " . $value . "</div>");
				}
			}
		} else {
			echo("<div style='margin-left:" . $depth * 10 . "px; color: Lime; background: Black;'>" . $arg . "</div>");
		}
	}

	// Получить документы
	public static function getAllDocuments() {
		$documents = [];
		$res = scandir(DOCUMENTS_DIR);
		if (count($res)) {
			foreach ($res AS $k => $file) {
				if ($file == "." || $file == "..") {
					continue;
				} else {
					$document = [];
					$document["title"] = $file;
					// $document["title"] = mb_convert_encoding($file,'utf8', 'cp1251');
					$document["path"] = DOCUMENTS_DIR ."/". $document["title"];
					$document["size"] = filesize(DOCUMENTS_DIR ."/". $file);

					$documents[] = $document;
				}
			}
		}

		return $documents;
	}

	// Получить новости
	public static function getAllNews($limit = 50) {
		$query = "SELECT * FROM news ORDER BY news_dt DESC LIMIT ".$limit;
		$result = dbconn::query($query);
		return $result;
	}
	// Получить конкретную новость
	public static function getNews($news_id) {
		$query = "SELECT * FROM news WHERE news_id = '".$news_id."'";
		$result = dbconn::query($query);
		return $result;
	}

	// Добавить новость
	public static function addNews($news_data) {
		if (!count($news_data)) return ["status" => false, "result" => "ARGUMENTS_ERROR"];
		$keys = [];
		$values = [];
		foreach ($news_data AS $k => $v) {
			if (!$v) continue;
			$keys[] = $k;
			$values[] = "'".$v."'";
		}
		$query = "INSERT INTO news (".implode(", ", $keys).") VALUES (".implode(", ", $values).")";
		$result = dbconn::query($query);
		return $result;
	}
	// Обновить новость
	public static function updateNews($news_id, $news_data) {
		if (!$news_id || !count($news_data)) return ["status" => false, "result" => "ARGUMENTS_ERROR"];
		$data = [];
		foreach ($news_data AS $k => $v) {
			$data[] = $k . "= '" . $v . "'";
		}
		$query = "UPDATE news SET ".implode(", ", $data)." WHERE news_id = '".$news_id."'";
		$result = dbconn::query($query);
		return $result;
	}
	// Удалить новость
	public static function deleteNews($news_id) {
		if (!$news_id) return ["status" => false, "result" => "ARGUMENTS_ERROR"];
		$query = "DELETE FROM news WHERE news_id = '".$news_id."'";
		$result = dbconn::query($query);
		return $result;
	}

	// Получить константу
	public static function getData($data_code) {
		$query = "SELECT data_value FROM data WHERE data_code = '".$data_code."'";
		$result = dbconn::query($query);
		return $result;
	}

	// Записать константу
	public static function setData($data_code, $data_value) {
		$query = "UPDATE data SET data_value = '".$data_value."' WHERE data_code = '".$data_code."'";
		$result = dbconn::query($query);
		return $result;
	}
	
	public static function parse_bb_code($text)	{
    	$text = preg_replace('/\[(\/?)(b|i|u|s)\s*\]/', "<$1$2>", $text);
    	
    	$text = preg_replace('/\[code\]/', '<pre><code>', $text);
    	$text = preg_replace('/\[\/code\]/', '</code></pre>', $text);
    	
    	$text = preg_replace('/\[(\/?)quote\]/', "<$1blockquote>", $text);
    	$text = preg_replace('/\[(\/?)quote(\s*=\s*([\'"]?)([^\'"]+)\3\s*)?\]/', "<$1blockquote>Цитата $4:<br>", $text);
    	
    	$text = preg_replace('/\[url\](?:http:\/\/)?([a-z0-9-.]+\.\w{2,4})\[\/url\]/', "<a href=\"http://$1\">$1</a>", $text);
    	$text = preg_replace('/\[url\s?=\s?([\'"]?)(?:http:\/\/)?([a-z0-9-.]+\.\w{2,4})\1\](.*?)\[\/url\]/', "<a href=\"http://$2\">$3</a>", $text);
    	
    	
    	$text = preg_replace('/\[img\s*\]([^\]\[]+)\[\/img\]/', "<img src='$1'/>", $text);
    	$text = preg_replace('/\[img\s*=\s*([\'"]?)([^\'"\]]+)\1\]/', "<img src='$2'/>", $text);
    	
    	return $text;
    }
    
    public static function bb_parse($string) { 
        $tags = 'b|i|size|color|center|quote|url|img'; 
        while (preg_match_all('`\[('.$tags.')=?(.*?)\](.+?)\[/\1\]`', $string, $matches)) foreach ($matches[0] as $key => $match) { 
            list($tag, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]); 
            switch ($tag) { 
                case 'b': $replacement = "<strong>$innertext</strong>"; break; 
                case 'i': $replacement = "<em>$innertext</em>"; break; 
                case 'size': $replacement = "<span style=\"font-size: $param;\">$innertext</span>"; break; 
                case 'color': $replacement = "<span style=\"color: $param;\">$innertext</span>"; break; 
                case 'center': $replacement = "<div class=\"centered\">$innertext</div>"; break; 
                case 'quote': $replacement = "<blockquote>$innertext</blockquote>" . $param? "<cite>$param</cite>" : ''; break; 
                case 'url': $replacement = '<a href="' . ($param? $param : $innertext) . "\">$innertext</a>"; break; 
                case 'img': 
                    list($width, $height) = preg_split('`[Xx]`', $param); 
                    $replacement = "<img src=\"$innertext\" " . (is_numeric($width)? "width=\"$width\" " : '') . (is_numeric($height)? "height=\"$height\" " : '') . '/>'; 
                break; 
                case 'video': 
                    $videourl = parse_url($innertext); 
                    parse_str($videourl['query'], $videoquery); 
                    if (strpos($videourl['host'], 'youtube.com') !== FALSE) $replacement = '<embed src="http://www.youtube.com/v/' . $videoquery['v'] . '" type="application/x-shockwave-flash" width="425" height="344"></embed>'; 
                    if (strpos($videourl['host'], 'google.com') !== FALSE) $replacement = '<embed src="http://video.google.com/googleplayer.swf?docid=' . $videoquery['docid'] . '" width="400" height="326" type="application/x-shockwave-flash"></embed>'; 
                break; 
            } 
            $string = str_replace($match, $replacement, $string); 
        } 
        return $string; 
    } 
}
?>