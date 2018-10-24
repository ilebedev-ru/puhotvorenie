$(document).ready(function() {
	$(".question").click(function() {
		$.fancybox($('.backquest-layer').html(),
		{
			'autoDimensions'	: false,
			'width'         		: 500,
			'height'        		: 230,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		}
			); return false;
			
	});

	$(".backquest-form").live("submit", function() {
	var url = "/backquest.php";
	$.ajax({
		   type: "POST",
		   url: url,
		   data: $(this).serialize(),
		   success: function(data)
		   {
			   if(data == "1") $('.backquest-form').html('<p class="success">Мы скоро вам ответим</p>'); // show response from the php script.
			   else alert(data);
		   }
		 });

	return false; 
	});
});
function checkform(f){
	if($("#fancybox-content input#email").val().length < 3){
		alert('Вы не ввели email!');
		return false;
	}else f.submit();
}