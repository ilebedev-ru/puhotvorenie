<div id="_pwdeveloper">
	<div class="menu">
		<ul>
            {foreach from=$pwcontrollers key=controller item=name}
			<li><a class="iframe pwfancybox" href="{$link->getModuleLink('pwdeveloper', $controller)}">{$name}</a></li>
            {/foreach}
		</ul>
	</div>
</div>
<div id="_pwslide"></div>
<div id="_pwfloatblock">
	<div class="exit">Закрыть</div>
	<div class="content"></div>
</div>