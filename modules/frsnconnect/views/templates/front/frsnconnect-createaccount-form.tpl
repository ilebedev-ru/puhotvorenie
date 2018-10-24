{*
* Social Network connect modules
* frsnconnect 0.16 by froZZen
*}
<div class="account_creation">
    <h3 class="page-subheading">{l s='Your personal information' mod='frsnconnect'}</h3>
    <div id="frsn_createaccount_errors" class="error" style="display:none;"></div>
    <div class="clearfix">
        <label>{l s='Title' mod='frsnconnect'}</label>
	<br />
	{foreach from=$genders key=k item=gender}
            <div class="radio-inline">
                <label for="id_gender{$gender->id}" class="top">
                    <input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
                    {$gender->name}
		</label>
            </div>
	{/foreach}
    </div>
    <div class="required form-group">
        <label for="customer_firstname">{l s='First name' mod='frsnconnect'} <sup>*</sup></label>
	<input type="text" class="is_required form-control" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
    </div>
    <div class="required form-group">
        <label for="customer_lastname">{l s='Last name' mod='frsnconnect'} <sup>*</sup></label>
	<input type="text" class="is_required form-control" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
    </div>
    <div class="required form-group">
        <label for="email">{l s='Email' mod='frsnconnect'}</label>
	<input type="text" class="is_required form-control" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
    </div>

    {if $fr_email_warning}
    <p class="inline-infos"> 
        {l s='We encourage you to register your real e-mail' mod='frsnconnect'} 
    </p>
    {/if}  
    
    <div class="form-group">
        <label>{l s='Date of Birth' mod='frsnconnect'}</label>
	<div class="row">
            <div class="col-xs-4">
                <select id="days" name="days" class="form-control">
                    <option value="">-</option>
                    {foreach from=$days item=day}
                        <option value="{$day}" {if ($sl_day == $day)} selected="selected"{/if}>{$day}&nbsp;&nbsp;</option>
                    {/foreach}
		</select>
						{*
							{l s='January'}
							{l s='February'}
							{l s='March'}
							{l s='April'}
							{l s='May'}
							{l s='June'}
							{l s='July'}
							{l s='August'}
							{l s='September'}
							{l s='October'}
							{l s='November'}
							{l s='December'}
						*}
            </div>
            <div class="col-xs-4">
                <select id="months" name="months" class="form-control">
                    <option value="">-</option>
                    {foreach from=$months key=k item=month}
                        <option value="{$k}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
                    {/foreach}
		</select>
            </div>
            <div class="col-xs-4">
                <select id="years" name="years" class="form-control">
                    <option value="">-</option>
                    {foreach from=$years item=year}
                        <option value="{$year}" {if ($sl_year == $year)} selected="selected"{/if}>{$year}&nbsp;&nbsp;</option>
                    {/foreach}
                </select>
            </div>
	</div>
    </div>
</div>
{if isset($PS_REGISTRATION_PROCESS_TYPE) && $PS_REGISTRATION_PROCESS_TYPE}
<div class="account_creation">
    <h3 class="page-subheading">{l s='Your address' mod='frsnconnect'}</h3>
    {if $inOrderOpc}
	<p class="required form-group">
            <label for="address1">{l s='Address' mod='frsnconnect'} <sup>*</sup></label>
            <input type="text" class="form-control" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
            <span class="inline-infos">{l s='Street address, P.O. Box, Company name, etc.'}</span>
	</p>
	<p class="required postcode form-group">
            <label for="postcode">{l s='Zip / Postal code' mod='frsnconnect'} <sup>*</sup></label>
            <input type="text" class="form-control" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
	</p>
    {/if}
    
    <p class="required form-group">
	<label for="city">{l s='City' mod='frsnconnect'} {if $inOrderOpc}<sup>*</sup>{/if}</label>
        <input type="text" class="form-control" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
    </p>
    <p class="required select form-group">
        <label for="id_country">{l s='Country' mod='frsnconnect'} {if $inOrderOpc}<sup>*</sup>{/if}</label>
        <select name="id_country" id="id_country" class="form-control">
            <option value="">-</option>
            {foreach from=$countries item=v}
		<option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
            {/foreach}
	</select>
    </p>
    
    {if $onr_phone_at_least}
        <p class="inline-infos">{l s='You must register at least one phone number' mod='frsnconnect'}</p>
        <p class="form-group">
            <label for="phone">{l s='Home phone' mod='frsnconnect'}</label>
            <input type="text" class="form-control" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
        </p>
        <p class="required form-group">
            <label for="phone_mobile">{l s='Mobile phone' mod='frsnconnect'} {if $onr_phone_at_least}<sup>*</sup>{/if}</label>
            <input type="text" class="form-control" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
        </p>
    {/if}
</div>
{/if}
<div class="submit clearfix">
    <input type="hidden" name="passwd" id="passwd" value="{if isset($smarty.post.passwd)}{$smarty.post.passwd}{/if}" />
    <input type="hidden" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
    {if isset($PS_REGISTRATION_PROCESS_TYPE) && !$PS_REGISTRATION_PROCESS_TYPE}
    <input type="hidden" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
    <input type="hidden" name="id_country" id="city" value="{if isset($smarty.post.id_country)}{$smarty.post.id_country}{/if}" />
    <input type="hidden" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
    <input type="hidden" name="phone_mobile" id="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
    {/if}
    {if !$inOrderOpc}
    <input type="hidden" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
    <input type="hidden" name="postcode" id="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}" />
    {/if}

    <input type="hidden" name="email_create" value="1" />
    <input type="hidden" name="is_new_customer" value="1" />
    <input type="hidden" name="sn_serv" value="{if isset($smarty.post.sn_serv)}{$smarty.post.sn_serv}{/if}" />
    <input type="hidden" name="sn_serv_uid" value="{if isset($smarty.post.sn_serv_uid)}{$smarty.post.sn_serv_uid}{/if}" />

    {if isset($back)}
        <input type="hidden" class="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />
    {/if}
    <button type="submit" name="submitAccount" id="submitAccount{if $inOrderOpc}_SN{/if}" class="btn btn-default button button-medium">
	<span>{if $inOrderOpc}{l s='Save' mod='frsnconnect'}{else}{l s='Register' mod='frsnconnect'}{/if}<i class="icon-chevron-right right"></i></span>
    </button>
    <p class="pull-right required"><span><sup>*</sup>{l s='Required field' mod='frsnconnect'}</span></p>
</div>
