$(function() {
	$( "#sortable" ).sortable({
		connectWith: "#sortable",
		stop: function(event, ui) {
			$("#sortable li").each(function(i){
				$(".ordering", this).val(i);
			});
		}

	});
});
function position_exception_textchange()
{
	// TODO : Add &amp; Remove automatically the "custom pages" in the "em_list_x"
	var obj = $(this);
	var list = obj.parent().find('#em_list');
	var values = obj.val().split(',');
	var len = values.length;
	
	list.find('option').prop('selected', false);
	for (var i = 0; i < len; i++)
		list.find('option[value="' + $.trim(values[i]) + '"]').prop('selected', true);
}

function position_exception_listchange()
{
	var obj = $(this);
	var str = obj.val().join(', ');
	
	obj.parent().find('#em_text').val(str);
}

$(document).ready(function(){
	$('#em_text').each(function(){
		$(this).change(position_exception_textchange).change();
	});

	$('#em_list').each(function(){
		$(this).change(position_exception_listchange);
	});
	
});