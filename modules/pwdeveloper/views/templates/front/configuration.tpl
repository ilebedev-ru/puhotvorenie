<h1>Просмотр значений Configuration</h1>
<table id="_pw_configuration">
<thead>
	<tr><td>Name</td><td>Value</td></tr>
</thead>
<tbody>
{foreach from=$configurations item=configure}
<tr><td>{$configure.name}</td><td>{$configure.value}</td><tr>
{/foreach}
</tbody>
</table>
<script>
$('#_pw_configuration').tablesorter({
	theme: 'blue',
	widgets: ["zebra", "filter", "editable"],
	widgetOptions: {
		editable_columns: [1],
		editable_enterToAccept: true,
		editable_autoAccept: false,
		editable_autoResort: false,
		editable_validate: null,
		editable_editComplete: 'editComplete',
	}
});
$('#_pwfloatblock tbody').on('editComplete', 'td', function(event, config){
	var $this = $(this),
	newContent = $this.text(),
	cellIndex = this.cellIndex,
	identifier = $($this.parents('table').find('th')[0]).text(),
	id = $($($this.parents('table').find('tbody tr')[$this.parent('tr').index()]).find('td')[0]).text(),
	val = $($this.parents('table').find('th')[cellIndex]).text();
	$.ajax({
		url: '/module/pwdeveloper/ajax',
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
</script>