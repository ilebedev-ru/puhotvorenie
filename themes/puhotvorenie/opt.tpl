{capture name=path}{l s='Оптом'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}
<h1>Пуховые платки оптом</h1>
<!--{$cmscontent}-->
{literal}
<script type="text/javascript">
    $(function(){
        $('#optform').submit(function(){
            $.ajax({
                type: 'POST',
                headers: { "cache-control": "no-cache" },
                url: '{/literal}{$link->getPageLink('opt.php')}{literal}' + '?rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType : "json",
                data: 'ajax=true&' + $(this).serialize(),
                success: function(jsonData)
                {
                    if (jsonData.hasError)
                    {
                        var errors = '';
                        for(error in jsonData.errors)
                            //IE6 bug fix
                            if(error != 'indexOf')
                                errors += jsonData.errors[error] + "\n";
                        alert(errors);
                    }
                    else
                    {
                        $('<p class="success"></p>').insertBefore('#optform');
                        $('p.success').html('Заявка успешно отправлена');
                        $('#optform').fadeOut();
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                }
            });
            return false;
        });
    })
</script>
{/literal}
<div class="opt_page">
	<div class="opt_form">
		<h2>Заявка на прайс-лист<span></span></h2>
		<form class="std" method="POST" id="optform" action="{$link->getPageLink('opt.php')}">
			<p class="required">
				<label for="fio">Ваше имя</label>
				<input required type="text" id="fio" name="fio"/>
			</p>
			<p class="required">
				<label for="phone">Номер телефона</label>
				<input required type="text" id="phone" name="phone"/>
			</p>
			<p class="required">
				<label for="email">Ваш Email</label>
				<input required type="email" id="email" name="email"/>
			</p>
			<p>
				<label for="comment">Комментарий</label>
				<textarea name="comment" id="comment" cols="30" rows="5"></textarea>
			</p>
			<input type="hidden" name="submitMessage" value="1" />
			<p><!--<label>&nbsp;</label>--><input type="submit" id="submitMessage" name="submitMessage" value="Отправить заявку"></p>
		</form>
	</div>
	<h3>Наши преимущества</h3>
	<ul>
		<li class="opt_circle crcl1">Скидка от объёма</li>
		<li class="opt_circle crcl2">Высокое качество товара</li>
		<li class="opt_circle crcl3">Уникальная схема работы</li>
	</ul>
	<h3>Примеры продукции</h3>
	<div class="opt_form">
		<form class="std" method="POST" id="optform" action="{$link->getPageLink('opt.php')}">
			<p><input type="submit" id="submitMessage" name="submitMessage" value="Отправить заявку"></p>
		</form>
	</div>
</div>