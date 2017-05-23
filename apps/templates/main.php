<div style="position: absolute; right: 8px;"><a href="/"><div class="icon icon-death"><div class="tip">Покончить с этим..</div></div></a></div>
<div style="position: absolute; right: 52px;"><a href="/database"><div class="icon icon-database"><div class="tip">База знаний</div></div></a></div>
<?php
$hero = null;
if (isset($_GET["hero"])) $hero = json_decode(base64_decode($_GET["hero"]), true);

$game = new Khorinis\Game($hero);

$realmTitle = $game->getRealmTitle();
if (isset($realmTitle)) echo(implode(" ", $realmTitle));

if (isset($_GET["actionId"])) $actionResult = $game->getActionResult($_GET["actionId"]);
if (isset($actionResult) && $actionResult) echo(implode(" ", $actionResult));

$heroData = $game->getHeroData();
if ($hero && isset($heroData) && $heroData) echo(implode(" ", $heroData));

$locationData = $game->getLocationData();
if (isset($locationData) && $locationData) echo(implode(" ", $locationData));

$actions = $game->getActions();
if (isset($actions) && $actions) echo(implode(" ", $actions));
?>