jQuery(document).ready(function(){
	$('.pos-right').click(function(){
		$(this).parent().find('ul').toggle(800);
	});
	$("#noposition").css("height",$(".leotheme-layout").height() );
	
	$('.leo-container').each(function(){
		$(this).sortable( {
			forceHelperSize: true,
			forcePlaceholderSize: true,
			placeholder: 'placeholder',
			handle:".leo-editmodule",
			update: function(event, ui){
				params = 'hook=' + ui.item.attr('data-position') + '&id_shop=' + id_shop + '&secure_key=' + secure_key + '&editPosition=1&'+$(this).sortable("serialize");
				$.ajax({
					type: 'POST',
					url: '../modules/leomanagewidgets/ajax.php',
					data: params,
					error:function(){
						alert("Error!");
					},
					success:function(data){
						$("#serverResponse").html(data);
					}
				});
			}
		});
	});
	
	
	$('a.fancybox').fancybox({
		"width"		: 1200,
		"height"	: 550,	
		'type'		: 'iframe',
		"scrolling" : "auto",
		'titleShow'		: false
	});
	
});
