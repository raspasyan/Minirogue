<?php
$hero = null;
if (isset($_GET["hero"])) $hero = json_decode(base64_decode($_GET["hero"]), true);
$action = "donation";
$actionData = [
    "t" => "Получить сладкий рулет!",
    "success" => [
        "d" => "Торт это ложь, простите. Возьмите лучше пару блестяшек.", "hp" => 100, "exp" => 1000, "items" => [
            ["id" => "gems", "qty" => 2]
        ]
    ],
    "img" => "icon-donation"
];
?>
<div style='text-align: center;'>
    Братан, из души в душу! Спасибо!
    <img src=apps/assets/donat.png></img>
</div>
<ul>
    <li>
        <a href="/?hero=<?=base64_encode(json_encode($hero)) . "&actionId=" . $action?>"><div class="icon <?=$actionData["img"]?>"></div> <?=$actionData["t"]?></a>
    </li>
</ul>