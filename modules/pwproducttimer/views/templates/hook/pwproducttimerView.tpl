{if $product}
    <!-- PwProductTimer MODULE -->
    <section class="specialsalesection">
        <div class="container puhotvorcont">
            <div class="special_sale clearfix">
                <div class="mobile_block">
                    <div class="main_sale">
                        <div class="main_sale_title">
                            <img src="https://puhotvorenie.ru/img/Object.png" alt="">
                            <div class="sale_title"><h4>Ограниченное предложение</h4></div>
                        </div>
                        <h3>{$product->name.6}</h3>
                        <div class="prices">
                            <div class="priceblock">
										<span class="new_price">
											 {$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)} ₽
										</span>
                                <span class="old_price">
											{displayPrice price=$product->price|number_format:0} ₽
										</span>
                            </div>
                            <div class="pricetitle"><span>Размер<br>скидки</span></div>
                            <div class="pricename">
                                {assign var="specificPrice" value=$product->specificPrice.0}
                                {if $specificPrice.reduction_type == 'percentage'}
                                    <span>-{$specificPrice.reduction*100} %</span>
                                {elseif $specificPrice.reduction_type == 'amount'}
                                    <span>{$specificPrice.reduction} ₽</span>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="left_block col-md-6 col-sm-12">
                    <div class="boxi">
                        <a href="{$link->getProductLink($product->id)}">
                            <img src="{$product->image}" alt="">
                            <div>
                                <div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="right_block col-md-6 col-sm-12">
                    <div class="main_sale">
                        <div class="sale_first_block">
                            <div class="main_sale_title">
                                <img src="https://puhotvorenie.ru/img/Object.png" alt="" style="display: inline-block!important;">
                                <div class="sale_title"><h4>Ограниченное предложение</h4></div>
                            </div>
                            <h3>{$product->name.6}</h3>
                            <div class="prices">
                                <div class="priceblock">
										<span class="new_price">
                                            {$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)} ₽
										</span>
                                    <span class="old_price">
											{displayPrice price=$product->price|number_format:0} ₽
										</span>
                                </div>
                                <div class="pricetitle"><span>Размер<br>скидки</span></div>
                                <div class="pricename">
                                    {assign var="specificPrice" value=$product->specificPrice.0}
                                    {if $specificPrice.reduction_type == 'percentage'}
                                    <span>-{$specificPrice.reduction*100} %</span>
                                    {elseif $specificPrice.reduction_type == 'amount'}
                                    <span>{$specificPrice.reduction} ₽</span>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="sale_second_block">
                            <div class="time_remaining_title"><a>До конца акции осталось</a></div>
                            <div class="time_remaining">
                                <div class="block_1">
                                    <span><span>{$time.days}</span></span>
                                    <a>Дней</a>
                                </div>
                                <span>:</span>
                                <div class="block_2">
                                    <span><span>{$time.hours}</span></span>
                                    <a>Часов</a>
                                </div>
                                <span>:</span>
                                <div class="block_3">
                                    <span><span>{$time.mins}</span></span>
                                    <a>Минут</a>
                                </div>
                            </div>
                            <div class="buyit">
                                <form id="buy_block"  action="{$link->getPageLink('cart', true)|escape:'html'}" method="post">
                                    <button>Добавить в корзину</button>
                                    <p class="hidden">

                                        <input type="hidden" name="token" value="{$static_token}"/>

                                        <input type="hidden" name="id_product" value="{$product->id|intval}"

                                               id="product_page_product_id"/>

                                        <input type="hidden" name="add" value="1"/>

                                        <input type="hidden" name="id_product_attribute" id="idCombination" value=""/>

                                    </p>
                                </form>
                            </div>
                            <div class="items_left">Осталась: 1 шт.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{else}
    <div style="background:blueviolet; width: 100%;">Акционных товаров пока что нет</div>
{/if}
