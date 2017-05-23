<?php
namespace Khorinis;

class Game {
	private $realm = [];
	private $hero = [
		"t" => "Вы",
		"hp" => [10, 10],
		"lvl" => 1,
		"exp" => [0, 100],
		"items" => [],
		"actions" => ["heroRest", "heroRest2", "craftWeapon01", "craftSupply02"],
		"locId" => "chooseDifficulty",
		"questId" => null
	];

	public function __construct($heroData = null) {
		if (isset($heroData)) $this->hero = $heroData;
		
		$data = file_get_contents("apps/assets/realm.json", true);
		$this->realm = json_decode($data, true);
	}

	public function getRealmTitle() {
		$result = [];
		$result[] = "<h1>" . $this->realm["t"] . "</h1>";
		return $result;
	}

	public function getAllEntities() {
		$result = [];

		$result[] = "<h2>Локации</h2>";
		foreach ($this->realm["locations"] as $locationData) {
			$result[] = "<h3>" .$locationData["t"] . "</h3>";
			$result[] = "<div>" .$locationData["d"] . "</div>";
			$result[] = "<h2>Действия</h2>";

			$result[] = "<ul>";
			foreach ($locationData["actions"] as $locActionId) {
				foreach ($this->realm["actions"] as $actionId => $action) {
					if ($locActionId != $actionId) continue;

					$actionItemTypes = [];
					if (isset($action["questId"])) {
						$questData = $this->realm["quests"][$action["questId"]];
						$actionItemTypes[] = "<span class='lbl'>" .
							"<div class='icon " . ($questData["img"] ? $questData["img"] : "icon-quest") . "'><div class='tip'>".$questData["d"]."</div></div>" .
							"<span>" .$questData["t"] . "</span></span>";
					}
					
					if (isset($action["itemTypes"])) {
						foreach ($action["itemTypes"] as $itemType) {
							$itemTypeData = $this->realm["itemTypes"][$itemType["id"]];
							$actionItemTypes[] = "<span class='lbl'><div class='icon ".$itemTypeData["img"]."'><div class='tip'>" . $itemTypeData["t"] . "</div></div>".$itemType["qty"].
							"<div class='icon ".$this->realm["stats"]["item-lvl"]["img"]."'><div class='tip'>" . $this->realm["stats"]["item-lvl"]["t"] . "</div></div>" . $itemType["lvl"] .
							"</span>";
						}
					}
					$result[] = "<li>";
					$result[] = "<div class='icon ".$action["img"]."'></div> <span ".(isset($action["hide"])?"class='lbl-col-warning'":null).">".$action["t"]."</span> ".implode(" ", $actionItemTypes).
					(isset($action["lvl"])?" <span class='lbl'><div class='icon ".$this->realm["stats"]["lvl"]["img"]."'><div class='tip'>" . $this->realm["stats"]["lvl"]["t"] . "</div></div>".$action["lvl"]."</span>":null);
					$result[] = "</li>";
				}
			}
			$result[] = "</ul>";
		}

		$result[] = "<h2>Предметы</h2>";
		foreach ($this->realm["itemTypes"] as $itemType => $itemTypeData) {
			$result[] = "<div>";
			$result[] = "<span class='lbl'><div class='icon ".$itemTypeData["img"]."'><div class='tip'>" . $itemTypeData["t"] . "</div></div></span>";
			$result[] = "</br>";
			foreach ($this->realm["items"] as $itemData) {
				if ($itemData["itemType"] == $itemType) {
					$result[] = "<span class='lbl'><div class='icon ".$itemData["img"]." ".(isset($itemData["q"])?$itemData["q"]:null)."'><div class='tip'>" . $itemData["t"] . "</div></div>х1".
					"<div class='icon ".$this->realm["stats"]["item-lvl"]["img"]."'><div class='tip'>" . $this->realm["stats"]["item-lvl"]["t"] . "</div></div>" . $itemData["lvl"] .
					"</span>";
				} else {
					continue;
				}
			}
			$result[] = "</div>";
		}

		$result[] = "<h2>Действия</h2>";
		$result[] = "<ul>";
		foreach ($this->realm["actions"] as $action) {
			$actionItemTypes = [];
			if (isset($action["questId"])) {
				$questData = $this->realm["quests"][$action["questId"]];
				$actionItemTypes[] = "<span class='lbl'>" .
					"<div class='icon " . ($questData["img"] ? $questData["img"] : "icon-quest") . "'><div class='tip'>".$questData["d"]."</div></div>" .
					"<span>" .$questData["t"] . "</span></span>";
			}
			
			if (isset($action["itemTypes"])) {
				foreach ($action["itemTypes"] as $itemType) {
					$itemTypeData = $this->realm["itemTypes"][$itemType["id"]];
					$actionItemTypes[] = "<span class='lbl'><div class='icon ".$itemTypeData["img"]."'><div class='tip'>" . $itemTypeData["t"] . "</div></div>".$itemType["qty"].
					"<div class='icon ".$this->realm["stats"]["item-lvl"]["img"]."'><div class='tip'>" . $this->realm["stats"]["item-lvl"]["t"] . "</div></div>" . $itemType["lvl"] .
					"</span>";
				}
			}
			$result[] = "<li>";
			$result[] = "<div class='icon ".$action["img"]."'></div> <span ".(isset($action["hide"])?"class='lbl-col-warning'":null).">".$action["t"]."</span> ".implode(" ", $actionItemTypes).
			(isset($action["lvl"])?" <span class='lbl'><div class='icon ".$this->realm["stats"]["lvl"]["img"]."'><div class='tip'>" . $this->realm["stats"]["lvl"]["t"] . "</div></div>".$action["lvl"]."</span>":null);
			$result[] = "</li>";
		}
		$result[] = "</ul>";

		return implode(" ", $result);
	}

	public function getActionResult($actionId) {
		if ($this->hero["hp"][0] <= 0) return false;

		$result = [];

		// Обрабатываем действие
		$actionData = $this->realm["actions"][$actionId];
		$success = true;
		// Проверка наличия предметов
		if (isset($actionData["itemTypes"])) {
			$actionItems = [];
			foreach ($actionData["itemTypes"] as $itemType) {
				$itemTypeData = $this->realm["itemTypes"][$itemType["id"]];
				$foundItem = [];
				$heroItemKey = null;
				foreach ($this->hero["items"] as $k => $heroItem) {
					$heroItemData = $this->realm["items"][$heroItem["id"]];
					// Ищем подходящий тип предмета
					if ($this->realm["items"][$heroItem["id"]]["itemType"] == $itemType["id"]) {
						// Проверяем уровень и кол-во
						if ($heroItemData["lvl"] >= $itemType["lvl"] && $heroItem["qty"] * $heroItemData["lvl"]  >= $itemType["qty"] * $itemType["lvl"]) {
							// Если подходящий предмет был найден ранее, берем тот, у которого меньше уровень
							if (count($foundItem)) {
								if ($heroItem["qty"] * $heroItemData["lvl"] < $itemType["qty"] * $itemType["lvl"]) {
									$foundItem = $heroItem;
									$heroItemKey = $k;
								}
							} else {
								$foundItem = $heroItem;
								$heroItemKey = $k;
							}
						}
					}
				}
				if ($foundItem) {
					$itemData = $this->realm["items"][$foundItem["id"]];
					$lostQty = ceil($itemType["qty"] * $itemType["lvl"] / $this->realm["items"][$foundItem["id"]]["lvl"]);
					$this->hero["items"][$heroItemKey]["qty"] -= $lostQty;
					$itemImg = "";
					if (isset($itemTypeData["img"])) $itemImg = $itemTypeData["img"];
					if (isset($itemData["img"])) $itemImg = $itemData["img"];
					$result[] = "<span class='lbl lbl-col-bad'>-" .
						"<div class='icon ".$itemImg."'><div class='tip'>".$itemData["t"]."</div></div>" . "x" . $lostQty .
						"<div class='icon icon-item-lvl'><div class='tip'>".$this->realm["stats"]["item-lvl"]["t"]."</div></div>" . $itemData["lvl"] .
						"</span>";
				} else {
					$success = false;
					break;
				}
			}
		}
		
		// Проверка уровней
		if ($success && isset($actionData["lvl"])) {
			$lvl = intval(rand(1, $this->hero["lvl"]));
			$alvl = intval(rand(1, $actionData["lvl"]));
			// var_dump($lvl, $alvl);

			if ($lvl < $alvl) $success = false;
		}

		$result[] = "<h2>
			<span class='" . ($success? "lbl lbl-col-good'>" . $this->realm["stats"]["success"]["t"] : "lbl lbl-col-bad'>" . $this->realm["stats"]["fail"]["t"]) . "</span>" . 
			"</h2>";

		// Success
		if (isset($actionData["success"]) && $success) $resultData = $actionData["success"];
		// Fail
		if (isset($actionData["fail"]) && !$success) $resultData = $actionData["fail"];
		// Result
		if (isset($resultData["d"])) $result[] = "<div>".$resultData["d"]."</div>";
		if (isset($resultData["locId"])) $this->hero["locId"] = $resultData["locId"];
		if (isset($resultData["questId"]) && $this->hero["questId"] == $actionData["questId"]) {
			$this->hero["questId"] = $resultData["questId"];
			$result[] = "<span class='lbl lbl-col-good'>" .
				"<div class='icon " . $this->realm["quests"][$resultData["questId"]]["img"] . "'><div class='tip'>".$this->realm["quests"][$resultData["questId"]]["d"]."</div></div>" .
				$this->realm["quests"][$resultData["questId"]]["t"] . "</span>";
		}
		if (isset($resultData["hp"])) {
			$this->hero["hp"][0] += $resultData["hp"];
			$result[] = "<span class='lbl ".($resultData["hp"]>=1?"lbl-col-good'>+":"lbl-col-bad'>-") .
				"<div class='icon ".$this->realm["stats"]["hp"]["img"]."'><div class='tip'>".$this->realm["stats"]["hp"]["t"]."</div></div>" . abs($resultData["hp"]) .
				"</span>";
		}
		if (isset($resultData["exp"])) {
			$this->hero["exp"][0] += $resultData["exp"];
			$result[] = "<span class='lbl lbl-col-good'>+" . 
				"<div class='icon ".$this->realm["stats"]["exp"]["img"]."'><div class='tip'>".$this->realm["stats"]["exp"]["t"]."</div></div>" . $resultData["exp"] .
				"</span>";
		}
		if (isset($resultData["items"])) {
			foreach ($resultData["items"] as $item) {
				$itemData = $this->realm["items"][$item["id"]];

				$itemQty = 0;
				if (isset($item["qty"])) $itemQty += $item["qty"];
				if (isset($item["maxQty"])) $itemQty += round(rand(0, $item["maxQty"]));
				if ($itemQty) {
					$find = false;
					$itemTypeData = $this->realm["itemTypes"][$this->realm["items"][$item["id"]]["itemType"]];
					$itemData = $this->realm["items"][$item["id"]];
					$itemImg = "";
					if (isset($itemTypeData["img"])) $itemImg = $itemTypeData["img"];
					if (isset($itemData["img"])) $itemImg = $itemData["img"];
					$result[] = " <span class='lbl lbl-col-good'>+" .
						"<div class='icon " . $itemImg . (isset($itemData["q"])? " " . $itemData["q"]:null) . "'><div class='tip'>".$itemData["t"]."</div></div>" . "x" . $itemQty .
						"<div class='icon " . $this->realm["stats"]["item-lvl"]["img"] . "'><div class='tip'>".$this->realm["stats"]["item-lvl"]["t"]."</div></div>" . $itemData["lvl"] .
						"</span>";
					foreach ($this->hero["items"] as $k => $heroItem) {
						if ($heroItem["id"] == $item["id"]) {
							$find = true;
							if (isset($itemQty)) $this->hero["items"][$k]["qty"] += $itemQty;
							break;
						}
					}
					if (!$find) $this->hero["items"][] = ["id" => $item["id"], "qty" => $itemQty];
				}
			}
		}

		return $result;
	}

	public function getHeroData() {
		$result = [];
		if ($this->hero["hp"][0] <= 0) {
			$result[] = "
				<h2>Вы погибли</h2>
				<a href='?'>Да как так-то!?</a>
			";
			return $result;
		}
		if ($this->hero["hp"][0] > $this->hero["hp"][1]) $this->hero["hp"][0] = $this->hero["hp"][1];
		while ($this->hero["exp"][0] >= $this->hero["exp"][1]) {
			$this->hero["exp"][0] = $this->hero["exp"][0] - $this->hero["exp"][1];
			$this->hero["exp"][1] = ceil($this->hero["exp"][1] * 1.75);
			$this->hero["lvl"] += 1;
			$this->hero["hp"][1] += 5;
			$this->hero["hp"][0] = $this->hero["hp"][1];
			$result[] = "
				<h2><span class='lbl lbl-col-warning'>Новый уровень!</span></h2>
				<span class='lbl lbl-col-good'>+5 Hp</span>
				<span class='lbl lbl-col-warning'>+1 Lvl</span>
			";
		}
		$result[] = "<div><h3>".$this->hero["t"]."</h3>
			<span class='lbl'>" . 
				"<div class='icon ".$this->realm["stats"]["hp"]["img"]."'><div class='tip'>".$this->realm["stats"]["hp"]["t"]."</div></div>".
				$this->hero["hp"][0]."/".$this->hero["hp"][1] .
				"</span>
			<span class='lbl'>" . 
				"<div class='icon ".$this->realm["stats"]["lvl"]["img"]."'><div class='tip'>".$this->realm["stats"]["lvl"]["t"]."</div></div>" .
				$this->hero["lvl"] .
				"</span>
			<span class='lbl'>" . 
				"<div class='icon ".$this->realm["stats"]["exp"]["img"]."'><div class='tip'>".$this->realm["stats"]["exp"]["t"]."</div></div>" .
				$this->hero["exp"][0]."/".$this->hero["exp"][1] .
				"</span>
			<span class='lbl'>" .
				"<div class='icon " . ($this->realm["quests"][$this->hero["questId"]]["img"]?$this->realm["quests"][$this->hero["questId"]]["img"]:"icon-quest2") ."'><div class='tip'>".$this->realm["quests"][$this->hero["questId"]]["d"]."</div></div>" .
				$this->realm["quests"][$this->hero["questId"]]["t"] . "</span>
		</div>";
		$result[] = "<div>";
		foreach ($this->hero["items"] as $k => $item) {
			if (isset($item["qty"]) && !$item["qty"]) {
				array_splice($this->hero["items"], $k, 1);
				continue;
			}
			$itemTypeData = $this->realm["itemTypes"][$this->realm["items"][$item["id"]]["itemType"]];
			$itemData = $this->realm["items"][$item["id"]];
			$itemImg = "";
			if (isset($itemTypeData["img"])) $itemImg = $itemTypeData["img"];
			if (isset($itemData["img"])) $itemImg = $itemData["img"];
			$result[] = "<span class='lbl'>" .
				"<div class='icon ".$itemImg.(isset($itemData["q"])? " ".$itemData["q"]:null)."'><div class='tip'>".$itemData["t"]."</div></div>" .
			 	(isset($item["qty"])?"x".$item["qty"]:null) .
			 	"<div class='icon ".$this->realm["stats"]["item-lvl"]["img"]."'><div class='tip'>".$itemTypeData["t"]."</div></div>" . $itemData["lvl"] .
				"</span>";
		}
		$result[] = "</div>";
		return $result;
	}

	public function getLocationData() {
		if ($this->hero["hp"][0] <= 0) return false;

		$result = [];
		$locationData = $this->realm["locations"][$this->hero["locId"]];
		$result[] = "<h3>" .$locationData["t"] . "</h3>";
		$result[] = "<div>" .$locationData["d"] . "</div>";
		return $result;
	}

	public function getActions() {
		if ($this->hero["hp"][0] <= 0) return false;
		
		$result = [];
		$result[] = "<ul>";
		$locationData = $this->realm["locations"][$this->hero["locId"]];
		$locationData["actions"] = array_merge($locationData["actions"], $this->hero["actions"]);
		foreach ($locationData["actions"] as $action) {
			$actionData = $this->realm["actions"][$action];
			$actionLvl = (isset($actionData["lvl"])?$actionData["lvl"]:null);
			$show = true;
			$haveItems = true;
			$haveQuest = true;

			$actionItems = [];
			if (isset($actionData["questId"])) {
				if ($this->hero["questId"] == $actionData["questId"]) {
					//
				} else {
					$haveQuest = false;
					if (isset($actionData["hide"]) && $actionData["hide"]) $show = false;
				}
				$questData = $this->realm["quests"][$actionData["questId"]];
				$actionItems[] = "<span class='lbl'>" .
					"<div class='icon " . ($questData["img"] ? $questData["img"] : "icon-quest") . "'><div class='tip'>".$questData["d"]."</div></div>" .
					"<span class='" . ($haveQuest ? "lbl-col-good" : "lbl-col-bad") . "'>" .$questData["t"] . "</span></span>";
			}

			if ($show && isset($actionData["itemTypes"])) {
				foreach ($actionData["itemTypes"] as $itemType) {
					$itemTypeData = $this->realm["itemTypes"][$itemType["id"]];
					$foundItem = [];
					foreach ($this->hero["items"] as $heroItem) {
						$heroItemData = $this->realm["items"][$heroItem["id"]];
						// Ищем подходящий тип предмета
						if ($this->realm["items"][$heroItem["id"]]["itemType"] == $itemType["id"]) {
							// Проверяем уровень и кол-во
							if ($heroItemData["lvl"] >= $itemType["lvl"] && $heroItem["qty"] * $heroItemData["lvl"]  >= $itemType["qty"] * $itemType["lvl"]) {
								// Если подходящий предмет был найден ранее, берем тот, у которого меньше уровень
								if (count($foundItem)) {
									if ($heroItem["qty"] * $heroItemData["lvl"] < $itemType["qty"] * $itemType["lvl"]) $foundItem = $heroItem;
								} else {
									$foundItem = $heroItem;
								}
							}
						}
					}
					if ($foundItem) {
						// print_r($foundItem);
					} else {
						if (isset($actionData["hide"]) && $actionData["hide"]) $show = false;
						$haveItems = false;
					}
					$actionItems[] = "<span class='lbl ".(count($foundItem)?"lbl-col-good":"lbl-col-bad")."'>" .
						"<div class='icon ".$itemTypeData["img"]."'><div class='tip'>".$itemTypeData["t"]."</div></div>" . 
						(isset($itemType["qty"])?"x".$itemType["qty"]:null) .
						"<div class='icon ".$this->realm["stats"]["item-lvl"]["img"]."'><div class='tip'>".$this->realm["stats"]["item-lvl"]["t"]."</div></div>" . $itemType["lvl"] .
						"</span>";
				}
			}

			if ($actionLvl) $chance = round(($this->hero["lvl"] / $actionLvl) * 100);
			if ($chance > 100) $chance = 100;
			if ($chance >= 66) $color = "lbl-col-good";
			if ($chance < 66 && $chance >= 33) $color = "lbl-col-warning";
			if ($chance < 33 && $chance >= 15) $color = "lbl-col-bad";
			if ($chance < 15) $color = "lbl-col-danger";

			if ($show) {
				$actionHTML = "";
				$actionHTML .= "<li>";
				$actionHTML .= (($haveQuest && $haveItems) ? "<a href='?hero=" . base64_encode(json_encode($this->hero)) . "&actionId=" . $action . "'>" : "<span class='lbl-col-bad'>");

				$actionHTML .= (isset($actionData["img"]) ? "<div class='icon ".$actionData["img"]."'></div>" : null) . " " . $actionData["t"];
				$actionHTML .= (($haveQuest && $haveItems) ? "</a>" : "</span>");
				$actionHTML .= " " . implode(" ", $actionItems) .
					((isset($actionLvl) && $actionLvl)? " <span class='lbl " . $color . "'>" .
					($this->realm["stats"]["lvl"]["img"] ? "<div class='icon ".$this->realm["stats"]["lvl"]["img"]."'><div class='tip'>".$this->realm["stats"]["lvl"]["t"]."</div></div>" : null) .
					$actionLvl . " (~" . $chance . "%)</span>":null) . "</li>";

				$result[] = $actionHTML;
			}
		}
		$result[] = "</ul>";

		return $result;
	}
}
?>