{**
* Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
*
* @category  Front Office Features
* @package   Yandex Payment Solution
* @author    Yandex.Money <cms@yamoney.ru>
* @copyright © 2015 NBCO Yandex.Money LLC
* @license   https://money.yandex.ru/doc.xml?id=527052
*}

	<div class="tab-content panel">
		<div class="tab-pane active" id="kassa_return">
			{if isset($return_success) && $return_success}<p class='alert alert-success'>{$text_success|escape:'htmlall':'UTF-8'}</p>{/if}
			{if isset($return_errors) && $return_errors|count > 0}
				{foreach $return_errors as $ke}
					<p class='alert alert-danger'>{$ke|escape:'htmlall':'UTF-8'}</p>
				{/foreach}
			{/if}

			<form class="form-horizontal" method='post' action="">
			<table class="table table-bordered">
			{if $invoiceId}
			<tr>
				<td>{l s='Номер транзакции Яндекс.Касса' mod='yamodule'}</td>
				<td>{$invoiceId|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td>{l s='Номер заказа' mod='yamodule'}</td>
				<td>{$id_order|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td>{l s='Способ оплаты' mod='yamodule'}</td>
				<td>{$payment_method|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td>{l s='Сумма платежа' mod='yamodule'}</td>
				<td>
					{displayPrice price=$doc->total_paid_tax_incl}&nbsp;
				</td>
			</tr>
			<tr>
				<td>{l s='Возвращено' mod='yamodule'}</td>
				<td>{$return_total|escape:'htmlall':'UTF-8'}</td>
			</tr>
                {if $products|count < 1 && $YA_SEND_CHECK}

                {else}
			<tr>
				<td>{l s='Сумма возврата' mod='yamodule'}</td>
				<td style="width: 350px;">
					<div class="input-group">
						<span class="input-group-addon"> руб</span>
                        {if $YA_SEND_CHECK}
							<input type="text" disabled name="return_sum_front" class='control-form return_sum return_disabled' value="{$doc->total_paid_tax_incl|replace:',':'.'|escape:'htmlall':'UTF-8' - $return_total|replace:',':'.'|escape:'htmlall':'UTF-8'}" id="return_sum" />
							<input type="hidden" name="return_sum" class='control-form return_sum return_hidden' value="{$doc->total_paid_tax_incl|replace:',':'.'|escape:'htmlall':'UTF-8' - $return_total|replace:',':'.'|escape:'htmlall':'UTF-8'}" id="return_sum" />
						{else}
							<input type="text" name="return_sum" class='control-form return_sum' value="{$doc->total_paid_tax_incl|replace:',':'.'|escape:'htmlall':'UTF-8' - $return_total|replace:',':'.'|escape:'htmlall':'UTF-8'}" id="return_sum" />
						{/if}
					</div>
				</td>
			</tr>
			  <tr>
				 <td>{l s='Причина возврата' mod='yamodule'}</td>
				 <td><textarea class='control-form' name='return_cause'></textarea></td>
			  </tr>
                    {if $YA_SEND_CHECK}
				<tr>
					<td></td>
					<td>
						<label><input checked type="radio" name="fullreturn" value="1" style="margin-left: 10px;"/> Полный возврат</label>
                        <label><input {if $products|count < 1 || !$YA_SEND_CHECK} disabled {/if} type="radio" name="fullreturn" value="0" style="margin-left: 10px;"/> Частичный возврат</label>
					</td>
				</tr>
					<tr class="product-list" style="display: none;">
					  <td colspan="2">
						  <label>Товары, которые будут удалены из чека</label>
						  <script>
							  $(document).ready(function(){
								  updPrice();

                                  $('input[name="fullreturn"]').on('change', function () {
                                      var value = $('input[name="fullreturn"]:checked').val();
                                      if (value == 1) {
                                          $('.product-list').hide();
                                      } else {
                                          $('.product-list').show();
                                      }

                                      updPrice();
                                  });
                                  $('input[name="fullreturn"]').trigger('change');

								  $('.removeProduct').click(function(e) {
									  $(this).parent().parent().remove();
									  updPrice();
								  });

								  $('.qty_change').click(function(e) {
										e.preventDefault();
										var value = parseInt($(this).parent().find('input.nshow').first().val());
										var min = parseInt($(this).attr('min'));
										var max = parseInt($(this).attr('max'));

										if ($(this).hasClass('up')) {
											value++;
										}

										if ($(this).hasClass('down')) {
											value--;
										}

										if (value < min) {
											return false;
										}
										if (value > max) {
											return false;
										}

										$(this).parent().find('input').val(value);
										updPrice();
								  });

								  function updPrice() {
										var sum = 0;
										$('input.summa').each(function() {
											var qty = parseFloat($(this).parent().parent().find('input.nshow').val());
											sum += parseFloat($(this).val()) * qty;
										});

										$('.return_sum').val(sum.toFixed(2));
								  }
							  });
						  </script>
						  <input type="hidden" class="email" value="{$id_order}" name="id_order"/>
						  <input type="hidden" class="email" value="{$email}" name="email"/>
						  <table style="width:100%;">
							  <tbody>
								  {foreach $products as $product}
								  <tr>
									  <td>{$product['product_name']}</td>
									  <td>
										  <div style="display: flex;">
											  <button class="qty_change down" max="{$product['product_quantity']}" min="0"> - </button>
											  <input class="show" disabled style="text-align: center;width:60px;" type="text" value="{$product['product_quantity']}" />
											  <input class="nshow" type="hidden" value="{$product['product_quantity']}" name="items[{$product['product_id']}_{$product['product_attribute_id']}][quantity]" />
											  <button class="qty_change up" max="{$product['product_quantity']}" min="0"> + </button>
										  </div>
									  </td>
									  <td>
										  <a class="removeProduct" style="text-decoration: underline; cursor: pointer;">Оставить в чеке</a>
										  <input type="hidden" class="summa" value="{$product['unit_price_tax_incl']}" name="items[{$product['product_id']}_{$product['product_attribute_id']}][price][amount]"/>
										  <input type="hidden" value="{$product['product_id']}" name="items[{$product['product_id']}_{$product['product_attribute_id']}][id_product]"/>
										  <input type="hidden" value="{$product['id_order_detail']}" name="items[{$product['product_id']}_{$product['product_attribute_id']}][id_order_detail]"/>
										  <input type="hidden" value="{$product['product_name']|truncate:'128'}" name="items[{$product['product_id']}_{$product['product_attribute_id']}][text]"/>
										  <input type="hidden" value="643" name="items[{$product['product_id']}_{$product['product_attribute_id']}][price][currency]"/>
										  <input type="hidden" value="{if isset($taxesValue["YA_NALOG_STAVKA_{$product['id_tax_rules_group']}"])}{$taxesValue["YA_NALOG_STAVKA_{$product['id_tax_rules_group']}"]}{else}1{/if}" name="items[{$product['product_id']}_{$product['product_attribute_id']}][tax]"/>
									  </td>
								  </tr>
								  {/foreach}
							  		{if $delivery}
										<tr>
											<td>{$dname}</td>
											<td>
												<div style="display: flex;">
													<button class="qty_change down" max="1" min="0"> - </button>
													<input class="show" disabled style="text-align: center;width:60px;" type="text" value="1" />
													<input class="nshow" type="hidden" value="1" name="items[shipping][quantity]" />
													<button class="qty_change up" max="1" min="0"> + </button>
												</div>
											</td>
											<td>
												<a class="removeProduct" style="text-decoration: underline; cursor: pointer;">Оставить в чеке</a>
												<input type="hidden" value="0" name="items[shipping][order_product_id]"/>
												<input type="hidden" class="summa" value="{$delivery}" name="items[shipping][price][amount]"/>
												<input type="hidden" value="643" name="items[shipping][price][currency]"/>
												<input type="hidden" value="0" name="items[shipping][tax]"/>
												<input type="hidden" value="{$dname}" name="items[shipping][text]"/>
											</td>
										</tr>
									{/if}
							  </tbody>
						  </table>
					  </td>
				  </tr>
					{/if}
                <tr>
				 <td colspan='2'><button {if !$invoiceId}disabled{/if} type='submit' class='btn btn-success'>{l s='Сделать возврат' mod='yamodule'}</button></td>
			  </tr>
				{/if}
			{else}
				<tr>
					<td colspan='3'><div class='alert alert-danger'>{l s='Информация по платежу отсутствует. Причиной может быть ошибочный сертификат по работе с MWS или настройки модуля Яндекс.Касса' mod='yamodule'}</div></td>
				</tr>
			{/if}
			</table>
			</form>
		</div>
		<div class="tab-pane" id="kassa_return_table">
			<div id="history"></div>
			<br />
			  <legend>{l s='Список возвратов' mod='yamodule'}</legend>
			  <form class="form-horizontal">
				<div class="form-group">
				  <div class="col-lg-12">
						<table class='table'>
						<tr>
							<td>{l s='Дата возврата' mod='yamodule'}</td>
							<td>{l s='Сумма возврата' mod='yamodule'}</td>
							<td>{l s='Причина возврата' mod='yamodule'}</td>
						</tr>
						{if $return_items}
							{foreach $return_items as $ret}
							 <tr>
								 <td>{$ret['date']|escape:'htmlall':'UTF-8'}</td>
								 <td>{displayPrice price=$ret['amount']}&nbsp;</td>
								 <td>{$ret['cause']|escape:'htmlall':'UTF-8'}</td>
							 </tr>
							 {/foreach}
						{else}
							 <tr>
								 <td colspan='3'><div class='alert alert-danger'>{l s='Успешные возвраты по данному платежу отсутствуют' mod='yamodule'}</div></td>
							 </tr>
						{/if}
						</table>
				  </div>
				</div>
			  </form>
		</div>
	</div>
</div>