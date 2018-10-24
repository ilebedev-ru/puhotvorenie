$(document).ready(function()
{
	$('#_pwslide').click(function(){
		$('#_pwdeveloper').slideToggle();
	});
	$('#_pwfloatblock .exit').click(function(){
		$('#_pwfloatblock').hide();
	});
	$('.pwfancybox').fancybox({
		type: 'iframe',
	});
});
function editableTable(data)
{
	var theTable = document.createElement('table');
	var thead = document.createElement('thead');
	var tbody = document.createElement('tbody');
	var tr = document.createElement('tr');
	$.each(data[0], function(key, val){
		var th = document.createElement('th');
		th.appendChild(document.createTextNode(key));
		tr.appendChild(th);
	});
	thead.appendChild(tr);
	$.each(data, function(key, val){
		var tr = document.createElement('tr');
		$.each(val, function(k, v){
			var td = document.createElement('td');
			td.appendChild(document.createTextNode(v));
			tr.appendChild(td);
		});
		tbody.appendChild(tr);
	});
	theTable.appendChild(thead);
	theTable.appendChild(tbody);
	$('#_pwfloatblock .content').empty();
	$('#_pwfloatblock .content').append(theTable);
	$('#_pwfloatblock .content table').tablesorter({
		theme: 'blue',
		widgets: ["zebra", "filter", "editable"],
		widgetOptions: {
			editable_columns: [2],
			editable_enterToAccept: true,
			editable_autoAccept: false,
			editable_autoResort: false,
			editable_validate: null,
			editable_editComplete: 'editComplete',
		}
	});
	$('#_pwfloatblock').show();
	resetEvents();
}


function resetEvents()
{
	$('#_pwfloatblock tbody').on('editComplete', 'td', function(event, config){
		var $this = $(this),
		newContent = $this.text(),
		cellIndex = this.cellIndex,
		identifier = $($this.parents('table').find('th')[0]).text(),
		id = $($($this.parents('table').find('tbody tr')[$this.parent('tr').index()]).find('td')[0]).text(),
		val = $($this.parents('table').find('th')[cellIndex]).text();
		$.ajax({
			url: pw_dev_url,
			data: {
				ajax: true,
				action: 'SetField',
				identifier: identifier,
				id: id,
				value: val,
				content: newContent,
			},
			success: function(output){
			}
		});
	});
}
