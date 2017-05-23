<!DOCTYPE html>
<html>
	<head>
	    <title><?=$titleTag?></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Текстовая ролевая игра RPG РПГ Minirogue - браузерная текстовая онлайн игра на выживание в мрачном фэнтези-мире со стратегическим походовым игровым процессом в виде интерактивной книги"/>
		<meta name="yandex-verification" content="f759fbdc0221fc48" />
		<meta name="robots" content="index,follow">
		<link rel="shortcut icon" href="apps/assets/favicon.png" type="image/png">
		<link href="apps/styles/style.css" rel="stylesheet">
	</head>
	<body>
		<div class="game-cont">
			<?php require_once 'apps/templates/'.$page;?>
			<hr/>
			<div class="donation">
				<div style='font-size: .7rem; color: goldenrod; font-weight: bold; text-shadow: 0 1px 0 black;'>Поддержать автора</div>
				<iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/quickpay/shop-widget?account=410015254845190&quickpay=shop&payment-type-choice=on&mobile-payment-type-choice=on&writer=seller&targets=%D0%9D%D0%B0+%D1%81%D0%BB%D0%B0%D0%B4%D0%BA%D0%B8%D0%B9+%D1%85%D0%BB%D0%B5%D0%B1%D1%83%D1%88%D0%B5%D0%BA&targets-hint=&default-sum=49&button-text=04&successURL=https%3A%2F%2Fminirogue.ru%2Fdonation" width="450" height="198"></iframe>	
			</div>
		</div>
		<!-- Скрипты -->
		<script src="apps/js/jquery/jquery-3.1.1.js"></script>
		<script src="apps/js/script.js" async></script>
    	<!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function() {
                    try {
                        w.yaCounter44734654 = new Ya.Metrika({
                            id:44734654,
                            clickmap:true,
                            trackLinks:true,
                            accurateTrackBounce:true
                        });
                    } catch(e) { }
                });
        
                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () { n.parentNode.insertBefore(s, n); };
                s.type = "text/javascript";
                s.async = true;
                s.src = "https://mc.yandex.ru/metrika/watch.js";
        
                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else { f(); }
            })(document, window, "yandex_metrika_callbacks");
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/44734654" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->
	</body>
</html>

