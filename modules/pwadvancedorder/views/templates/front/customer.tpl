{*<button class="btn button button-default button-medium" data-toggle="collapse" data-target="#customeradv"><span>{l s="Личные данные" mod="pwadvancedorder"}</span></button>*}

<div id="customeradv" class="{*collapse*}">
    <div {*class="panel-body"*}>

        <form action="{$link->getPageLink('authentication', null, null, ['back' => 'order'])}" method="POST" class="advancedForm">
            <input type="hidden" name="advStep" value="customer"/>
            {if isset($fields.firstname)}
                <label class="{if !empty($fields.firstname.is_required)}required{/if}">{l s="Фамилия, имя, отчество" mod="pwadvancedorder"}</label><div class="row">
            <div class="col-md-7 col-sm-6"><div class="form-group">
                    <input class="form-control" name="firstname" type="text" value="{$customer->firstname}" {if !empty($fields.firstname.is_required)}required{/if}>
                </div></div>
                <div class="col-md-5 col-sm-6 hidden-xs">
                    <div class="note">{l s="Введите ваше имя, чтобы мы знали как к Вам обращаться" mod="pwadvancedorder"}</div>
                </div></div>
            {$fields.firstname = null}
            {/if}
            {if isset($fields.lastname)}
                <label class="{if !empty($fields.lastname.is_required)}required{/if}">{l s="Фамилия" mod="pwadvancedorder"}</label>
                <div class="row">
            <div class="col-md-7 col-sm-6"><div class="form-group">
                    <input class="form-control" name="lastname" type="text" value="{$customer->lastname}" {if !empty($fields.lastname.is_required)}required{/if}>
                </div></div></div>
            {$fields.lastname = null}
            {/if}
            <button class="hidden btn button btn-default btn-medium button-medium submitAdvanceForm" type="submit"><span>{l s="Изменить" mod="pwadvancedorder"}</span></button>
        </form>
    </div>
</div>
<div class="adressAdvenc">

    <div class="col-md-12 col-xs-12 hidden">
        <form action="{$link->getPageLink('address', null, null, ['back' => 'order'])}" method="POST" class="advancedForm">
            <input type="hidden" name="advStep" value="selectAddress"/>
            <ul class="list-group">
            {foreach from=$addresses item=address}
                <li class="list-group-item {if $address.id_address == $cart->id_address_delivery}active{/if}">
                    <div class="radio">
                        <label>
                            <input type="radio" name="id_address_delivery" value="{$address.id_address}" {if $address.id_address == $cart->id_address_delivery}checked{/if}>
                            {$address.address1}
                        </label>
                    </div>
                </li>
            {/foreach}
                {*<li class="list-group-item list-group-item-warning">
                    <button onclick="return false;" class="btn button button-default button-medium" data-toggle="collapse" data-target="#newAddress"><span>{l s="Создать новый адрес"}</span></button>
                </li>*}
            </ul>
            <button class="btn button btn-default btn-medium button-medium submitAdvanceForm" type="submit"><span>{l s="Перейти к выбору способа доставки" mod="pwadvancedorder"}</span></button>
        </form>
    </div>
    <div class="col-md-12 col-xs-12 hidden">
        {foreach from=$addresses item=address}
        <div id="address_{$address.id_address}" class="well well-lg" {if $address.id_address != $cart->id_address_delivery}style="display:none;"{/if}>
            {assign var=frmAddress value=$formatedAddressFieldsValuesList[$address.id_address]['formated_fields_values']}
            {foreach from=$formatedAddressFieldsValuesList[$address.id_address]['ordered_fields'] item=field}
                {if !empty($frmAddress[$field])}
                    {$frmAddress[$field]}<br/>
                {/if}
            {/foreach}
        </div>
        {/foreach}
        <div id="newAddress" class="collapse">
            <form method="POST" class="advancedForm">
                <input type="hidden" name="advStep" value="newAddress"/>
                {if isset($fields.id_state)}
                    <label class="{if !empty($fields.id_state.is_required)}required{/if}">{l s="Регион" mod="pwadvancedorder"}</label>
                    <div class="row">
                        <div class="col-md-7 col-sm-6"><div class="form-group">
                        <select name="id_state" data-jcf={literal}'{"wrapNative": false, "wrapNativeOnMobile": false}'{/literal}>
                            {foreach from=$states item=state}
                            <option value="{$state.id_state}">{$state.name}</option>
                            {/foreach}
                        </select>
                            </div></div>
                    </div>
                {$fields.id_state = null}
                {/if}
                {if isset($fields.city)}
                    <label class="{if !empty($fields.city.is_required)}required{/if}">{l s="Город" mod="pwadvancedorder"}</label>
                    <div class="row">
                        <div class="col-md-7 col-sm-6">
                            <div class="form-group">
                              <input class="form-control" name="city" type="text" value="" {if !empty($fields.city.is_required)}required{/if}>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-6 hidden-xs">
                            <div class="note">Укажите город для расчёта доставки</div>
                        </div>
                    </div>
                {$fields.city = null}
                {/if}
                {if isset($fields.address1)}
                    <label class="{if !empty($fields.address1.is_required)}required{/if}">{l s="Адрес" mod="pwadvancedorder"}</label>
                    <div class="row">
                        <div class="col-md-7 col-sm-6"><div class="form-group">
                        <input class="form-control" name="address1" type="text" value="" {if !empty($fields.address1.is_required)}required{/if}>
                    </div>
                        </div>
                        <div class="col-md-5 col-sm-6 hidden-xs">
                            <div class="note">Укажите, улицу, дом</div>
                        </div></div>
                {$fields.address1 = null}
                {/if}
                {if isset($fields.phone)}
                    <label class="{if !empty($fields.phone.is_required)}required{/if}">{l s="Контактный телефон" mod="pwadvancedorder"}</label>
                    <div class="row">
                        <div class="col-md-7 col-sm-6"><div class="form-group">
                            <input class="form-control" name="phone" type="text" value="" {if !empty($fields.phone.is_required)}required{/if}>
                            </div></div>
                        <div class="col-md-5 col-sm-6 hidden-xs">
                            <div class="note">{l s="Введите ваш телефон, чтобы мы знали как с Вами связаться" mod="pwadvancedorder"}</div>
                        </div>
                    </div>
                {$fields.phone = null}
                {/if}
                {if isset($fields.other)}
                    <label class="{if !empty($fields.other.is_required)}required{/if}">{l s="Дополнительно" mod="pwadvancedorder"}</label>
                    <div class="row">
                        <div class="col-md-7 col-sm-6"><div class="form-group">
                            <textarea class="form-control" name="other"></textarea>
                            </div></div>
                        <div class="col-md-5 col-sm-6 hidden-xs">
                            <div class="note">{l s="Пожелания к заказу" mod="pwadvancedorder"}</div>
                        </div>
                    </div>
                {$fields.other = null}
                {/if}
                <button class="btn submitAdvanceForm" type="submit"><span>{l s="Сохранить адрес" mod="pwadvancedorder"}</span></button>
            </form>
        </div>
    </div>
    <div class="changeaddress">
        <form action="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" method="post" class="updateAddressForm std" id="add_address">

            <label class="{if !empty($fields.id_state.is_required)}required{/if}">{l s="Регион" mod="pwadvancedorder"}</label>
            <div class="row">
                <div class="col-md-7 col-sm-6">
                    <div class="form-group">
                        <select name="id_state" data-jcf={literal}'{"wrapNative": false, "wrapNativeOnMobile": false}'{/literal}>
                            {foreach from=$states item=state}
                                <option value="{if $addresses[0]['id_state']}{$addresses[0]['id_state']}{/if}" {if $state.id_state == $addresses[0]['id_state']}selected{/if}>{$state.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>

                <label class="required">{l s="Город" mod="pwadvancedorder"}</label>
                <div class="row">
                    <div class="col-md-7 col-sm-6"><div class="form-group ui-widget">
                    <input class="form-control city-ac" required name="city_1" type="text" value="{if $addresses[0]['city']}{$addresses[0]['city']}{/if}">
                        </div></div>
                    <div class="col-md-5 col-sm-6 hidden-xs">
                        <div class="note">{l s="Укажите город для расчёта доставки" mod="pwadvancedorder"}</div>
                    </div>
                </div>

                <label class="required">{l s="Адрес" mod="pwadvancedorder"}</label>
                <div class="row">
                    <div class="col-md-7 col-sm-6"><div class="form-group">
                    <input class="form-control" required name="address1_1" type="text" value="{if $addresses[0]['address1']}{$addresses[0]['address1']}{/if}">
                        </div></div>
                    <div class="col-md-5 col-sm-6 hidden-xs">
                        <div class="note">{l s="Укажите, улицу, дом" mod="pwadvancedorder"}</div>
                    </div>
                </div>
                <label class="{if !empty($fields.email.is_required)}required{/if}">{l s="E-mail" mod="pwadvancedorder"}</label>
                <div class="row">
                    <div class="col-md-7 col-sm-6"><div class="form-group">
                            <input class="form-control" name="email" type="text" value="{$customer->email}" {if !empty($fields.email.is_required)}required{/if}>
                        </div></div>
                    <div class="col-md-5 col-sm-6 hidden-xs">
                        <div class="note">{l s="Укажите e-mail, на который будет выслан пароль" mod="pwadvancedorder"}</div>
                    </div>
                </div>
                <div class="hidden">
                <label class="required">{l s="Почтовый индекс" mod="pwadvancedorder"}</label>
                <div class="row">
                    <div class="col-md-7 col-sm-6"><div class="form-group">
                    <input class="form-control city-id" name="postcode_1" required type="number" value="{if $addresses[0]['postcode']}{$addresses[0]['postcode']}{/if}">
                        </div></div>
                </div>
                </div>

                <label class="required">{l s="Телефон" mod="pwadvancedorder"}</label>
                <div class="row">
                    <div class="col-md-7 col-sm-6"><div class="form-group">
                    <input class="form-control" name="phone_1" type="tel" required data-validate="isPhoneNumber" value="{if $addresses[0]['phone']}{$addresses[0]['phone']}{/if}">
                        </div></div>
                    <div class="col-md-5 col-sm-6 hidden-xs">
                        <div class="note">{l s="Укажите телефон для уточнения заказа" mod="pwadvancedorder"}</div>
                    </div>
                </div>
            <div class="submit2">
                <input type="hidden" class="form-control" name="firstname" type="text" value="{if $customer->firstname}{$customer->firstname}{/if}">
                <input type="hidden" class="form-control" name="lastname" type="text" value="{if $customer->lastname}{$customer->lastname}{/if}">
                <input type="hidden" class="form-control" name="id_state_1" type="text" value="{if $addresses[0]['id_state'] > 0}{$addresses[0]['id_state']}{else}242{/if}">
                <input type="hidden" class="form-control" name="id_country_1" type="text" value="{if $addresses[0]['id_country']}{$addresses[0]['id_country']}{/if}">
                <input type="hidden" id="id_address" name="id_address_1" value="{if $addresses[0]['id_address']}{$addresses[0]['id_address']}{/if}" />
                <input type="hidden" name="alias" value="{if $addresses[0]['alias']}{$addresses[0]['alias']}{/if}" />
                <input type="hidden" name="token" value="{Tools::getToken(false)}" />

                    <div class="agree">
                        <input type="checkbox" name="cgv" id="cgb" checked value="1">
                        <label for="cgb">

                            {l s="Я даю согласие на обработку своих персональных данных в соответствии с законом №152-ФЗ от 27.07.2006 и принимаю условия " mod="pwadvancedorder"}<a class="fancybox cgvlink" href="{$link_conditions}">{l s="Пользовательского соглашения" mod="pwadvancedorder"}</a>
                        </label>
                    </div>
                <button type="submit" name="submitAddress" id="submitAddress" class="btn">
				<span>
					{l s='Перейти к выбору способа доставки'}
                    <i class="fa fa-chevron-right right"></i>
				</span>
                </button>
            </div>
        </form>
    </div>
    <div class="clearfix"></div>
</div>