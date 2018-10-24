<h1>Список контроллеров</h1>
<ul>
{foreach from=$controllers key=controller item=name}
<li><i class="fa fa-chevron-right"></i>&nbsp;<a class="pwfancybox" href="{$link->getModuleLink('pwdeveloper', $controller)}">{$name}</a></li>
{/foreach}
</ul>
<script>
if (typeof $.fancybox == 'function'){
    $('.pwfancybox').fancybox({
		type: 'iframe',
	});
}
</script>