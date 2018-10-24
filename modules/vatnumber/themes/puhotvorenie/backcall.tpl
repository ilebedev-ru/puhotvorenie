<script>
	$(document).ready(function() {
		$(".call-me").click(function() {
		$.fancybox($('.backcall-layer').html(),
		{
				'autoDimensions'	: false,
			'width'         		: 500,
			'height'        		: 140,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		}
			); return false;
				
		});
	
	$(".backcall-form").live("submit", function() {
		var url = "/backcall.php";
		$.ajax({
			   type: "POST",
			   url: url,
			   data: $(this).serialize(),
			   success: function(data)
			   {
				   if(data == "1") $('.backcall-form').html('<p class="success">Мы скоро вам перезвоним</p>'); // show response from the php script.
				   else alert(data);
			   }
			 });

		return false; 
	});
	});
	function checkform(f){
		if($("#fancybox-content input#phone").val().length < 6){
			alert('Введите номер!');
			return false;
		}else f.submit();
	}
</script>	
<div style="display:none;text-align:left;" class="backcall-layer">
<form id="backcall-form" action="/backcall.php" method="POST" class="backcall-form">
	<span>Перезвоните мне на номер:</span><br /><br />
	<center>
		<input type="text" value="+7" id="phone" name="phone" size="25"><br>
		<input type="submit" id="submitNumber" name="submitNumber" onclick="yaCounter22742683.reachGoal('backcall'); return true;" class="button" value="Жду">
	</center>
	</form>
</div>	