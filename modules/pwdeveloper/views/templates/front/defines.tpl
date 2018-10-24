<table id="_pw_define">
<thead>
	<tr><td>Name</td><td>Value</td></tr>
</thead>
<tbody>
{foreach from=$defines key=key item=define}
<tr><td>{$key}</td><td>{$define}</td><tr>
{/foreach}
</tbody>
</table>
<script>
$('#_pw_define').tablesorter({
	theme: 'blue',
	widgets: ["zebra", "filter"],
});
</script>