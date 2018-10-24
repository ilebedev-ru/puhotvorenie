{if !$content_only}
</div>
</div>
  </div>
  <!-- footer -->
  <div class="footer">
    <div class="page nuclear">
      <ul class="f-mnu">
        <li><a href="/">Главная</a></li>
		<li><a href="/content/2-o-nas">О нас</a></li>
    	<li><a href="{$link->getPageLink('prices-drop.php')}">Спецпредложения</a></li>
    	<li><a href="/content/5-dostavka-i-oplata">Доставка и оплата</a></li>
    	<li><a href="/content/6-uhod-za-izdelijami">Уход за изделиями</a></li>
    	<li><a href="/content/7-sotrudnichestvo">Сотрудничество</a></li>
		<li><a href="{$link->getPageLink('opt.php')}">Оптом</a></li>
    	<li><a href="/content/4-kontakty">Контакты</a></li>
      </ul>
      <div class="copy">
		<div class="social-block">
                <a title="Вконтакте" class="social-vk" rel="nofollow" href="http://vk.com/puxov" target="_blank"></a>
                <a title="Одноклассники" rel="nofollow" class="social-odnoklassniki" href="http://odnoklassniki.ru/group/52998403522665" target="_blank"></a>
        </div>
        <span>Copyright © {$shop_name}</span>
        <span><noindex>Все права защищены. При использовании материалов с сайта ссылка на источник обязательна.</noindex></span>
      </div>
	  {if $page_name != "order-confirmation"}
	<script type="text/javascript">
	{if $cart->id}var yaParams = {literal}{'id_cart':'{/literal}{$cart->id}{literal}', 'id_guest':'{/literal}{$cart->id_guest}{literal}'}{/literal};
	{else} var yaParams = {literal}{}{/literal};
	{/if}
	</script>
	{/if}

      <div class="counter"><div class="knopki">
	  <div id="vk_like"></div>
<script type="text/javascript">{literal}
VK.Widgets.Like("vk_like", {type: "full"});
{/literal}
</script>
{literal}
<div id="ok_shareWidget"></div>
<script>
!function (d, id, did, st) {
  var js = d.createElement("script");
  js.src = "http://connect.ok.ru/connect.js";
  js.onload = js.onreadystatechange = function () {
  if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
    if (!this.executed) {
      this.executed = true;
      setTimeout(function () {
        OK.CONNECT.insertShareWidget(id,did,st);
      }, 0);
    }
  }};
  d.documentElement.appendChild(js);
}(document,"ok_shareWidget","http://puhotvorenie.ru","{width:170,height:30,st:'oval',sz:20,ck:3}");
</script>
{/literal}
</div>
        <!--LiveInternet counter--><script type="text/javascript"><!--
		document.write("<a rel='nofollow' href='http://www.liveinternet.ru/click' "+
		"target=_blank><img src='//counter.yadro.ru/hit?t17.8;r"+
		escape(document.referrer)+((typeof(screen)=="undefined")?"":
		";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
		screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
		";"+Math.random()+
		"' alt='' title='LiveInternet: показано число просмотров за 24"+
		" часа, посетителей за 24 часа и за сегодня' "+
		"border='0' width='88' height='31'><\/a>")
		//--></script><!--/LiveInternet-->
		<div class="copyright">
					<a href="http://altopromo.com/"><img src="/img/copyright.png" alt="Разработка интернет-магазина"></a>
					<span>продвижение и разработка интернет-магазинов</span>
		</div>
		{literal}
{*СЧЕТЧИКИ УДАЛИЛ ILjaAlt*}
			<!-- Yandex.Metrika counter -->
			<script type="text/javascript" >
                (function (d, w, c) {
                    (w[c] = w[c] || []).push(function() {
                        try {
                            w.yaCounter22742683 = new Ya.Metrika({
                                id:22742683,
                                clickmap:true,
                                trackLinks:true,
                                accurateTrackBounce:true,
                                webvisor:true,
                                ecommerce:"dataLayer"
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
			<noscript><div><img src="https://mc.yandex.ru/watch/22742683" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
			<!-- /Yandex.Metrika counter -->
{/literal}
{*
{if $smarty.get.utm_medium == "cpc" && !$smarty.cookies.pop}
{literal}
	<script type='text/javascript'>
	function setCookie(name, value, expires, path, domain, secure) {
	  document.cookie = name + "=" + escape(value) +
	  ((expires) ? "; expires=" + expires : "") +
	  ((path) ? "; path=" + path : "") +
	  ((domain) ? "; domain=" + domain : "") +
	  ((secure) ? "; secure" : "");
	}
	setTimeout(function() {
		sweetAlert({
			title: "Вам подарок! Купон на 202 Р.",
			text: "<b style=\"font-weight:bold\">MEGASKIDKA</b>",
			html:true,
			confirmButtonColor:"#ac3179",
			imageUrl: "/img/gift.png",
			confirmButtonText: "Ок, активировать!",
			closeOnConfirm: false
		},
		function(){
			var url = "/backcoupon.php";
			$.ajax({
			   type: "POST",
			   url: url,
			   data: "activate=MEGASKIDKA",
			   success: function(data)
			   {
				   if(data == "2") swal("Добавлен!", "Купон активирован в корзине.", "success"); // show response from the php script.
				   else swal("Добавлен!", data, "success");
				   setCookie('pop', '1');
			   }
			});
		  
		}
)
	}, 20000);
	{/literal}
	</script>
{/if}
*}
{literal}
<script type="text/javascript">
(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=qcQyqarxg*s0V6mz7JNsWktseoaVijEpq15ivcOELpzD9cUXYGRu4Uuk*uua*ydk5nFB7Adhv*MI0ksM0mttM9wkUqaZc3djjvuCVyPeSuC*kN5zh679lqF9guovP67uMXweXV3xWULDesMY3ap7gdsBfqP9rUVJZVWS5UHgZJg-';
</script>

<script type="text/javascript">
var _tmr = window._tmr || (window._tmr = []);
_tmr.push({id: "2709569", type: "pageView", start: (new Date()).getTime()});
(function (d, w, id) {
  if (d.getElementById(id)) return;
  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
  ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
})(document, window, "topmailru-code");
</script>
{/literal}
{/if}

</body>

</html>
