$(document).ready(function(){
	var id_category = $('select[name="id_category_default"] option:selected').val();
	if(id_category > 0) $('.categoryBox.id_category_default[value="'+id_category+'"]').prop('checked', true);
	 $('select[name="id_category_default"]').change(function(){
		var id_category = $('select[name="id_category_default"] option:selected').val();
		if(id_category > 0){
			$('.categoryBox.id_category_default').prop('checked', false);
			$('.categoryBox.id_category_default[value="'+id_category+'"]').prop('checked', true);
		}
	 });
	 
	$('.generate').click(function(){
		$.ajax({
			type: "POST",
			url: "/modules/newscore/ajax.php",
			data: { action: "generate", qty: generate_qty }
		}).done(function( msg ) {
			$('#productlist').val(msg);
		});
		return false;
	 });
})