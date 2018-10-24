
$(document).ready(function(){
	$('.change_button').click(function(){
		var percent = $('input.change_price').val();
		percent = percent.replace('+', '');
		console.log(percent);
		$('.product_price').each(function(){
			$(this).val(Math.round($(this).val()*(percent/100 + 1)));
		});
		return false;
	});

})