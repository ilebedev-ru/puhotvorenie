{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $errors}
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		{foreach from=$errors item=error name=errors}
			<span>{$error}</span>
		{/foreach}
	</div>
{/if}

{if $success}
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<span>{l s='Сохранено' mod='pwblockfavpage'}</span>
	</div>
{/if}

<div class="panel">
	<form class="form-horizontal" method="post">

		<h3><i class="icon icon-credit-card"></i> {l s='Простое меню' mod='pwblockfavpage'}</h3>
		<div class="panel-body">

			<div class="form-group">
				<label for="block-name" class="col-sm-2 control-label">{l s='Название блока' mod='pwblockfavpage'}</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="block-name" placeholder="{l s='Название блока' mod='pwblockfavpage'}"
						   {if isset($data->block_name) AND $data->block_name}value="{$data->block_name}"{/if}>
				</div>
			</div>

			<hr>

			<table class="table pwblockfavpage-table">
				<caption><h1>{l s='Пункты меню' mod='pwblockfavpage'}</h1></caption>
				<thead>
					<tr>
						<th>{l s='#' mod='pwblockfavpage'}</th>
						<th>{l s='Название' mod='pwblockfavpage'}</th>
						<th>{l s='Ссылка' mod='pwblockfavpage'}</th>
						<th>{l s='Удалить' mod='pwblockfavpage'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$data->links item=link name=links}
						<tr>
							<td>{$smarty.foreach.links.iteration}</td>
							<td><input type="text" class="form-control" name="link-name[]" placeholder="{l s='Название ссылки' mod='pwblockfavpage'}" value="{$link->name}"></td>
							<td><input type="text" class="form-control" name="link-url[]" placeholder="{l s='URL ссылки' mod='pwblockfavpage'}" value="{$link->url}"></td>
							<td><img class="link-del" src="../img/admin/disabled.gif" /></td>
						</tr>
					{foreachelse}
						<tr>
							<td>1</td>
							<td><input type="text" class="form-control" name="link-name[]" placeholder="{l s='Название ссылки' mod='pwblockfavpage'}"></td>
							<td><input type="text" class="form-control" name="link-url[]" placeholder="{l s='URL ссылки' mod='pwblockfavpage'}"></td>
							<td><img class="link-del" src="../img/admin/disabled.gif" /></td>
						</tr>
					{/foreach}
				</tbody>
			</table>

			<button type="submit" value="1" id="linkAdd" class="btn btn-default pull-right">{l s='Добавить еще' mod='pwblockfavpage'}</button>

			<div class="hidden" id="row-template">
				<table>
					<tbody>
						<tr>
							<td></td>
							<td><input type="text" class="form-control" name="link-name[]" placeholder="{l s='Название ссылки' mod='pwblockfavpage'}"></td>
							<td><input type="text" class="form-control" name="link-url[]" placeholder="{l s='URL ссылки' mod='pwblockfavpage'}"></td>
							<td><img class="link-del" src="../img/admin/disabled.gif" /></td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>

		<div class="panel-footer">
			<button type="submit" value="1" id="submitPWBlockFavPageModule" name="submitPWBlockFavPageModule" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Сохранить' mod='pwblockfavpage'}
			</button>
			<a href="index.php?controller=AdminModules" class="btn btn-default pull-right">
				<i class="process-icon-cancel"></i> {l s='Отмена' mod='pwblockfavpage'}
			</a>
		</div>

	</form>
</div>
