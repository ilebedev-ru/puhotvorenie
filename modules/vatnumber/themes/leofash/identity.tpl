{*
* 2007-2013 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Your personal information'}{/capture}

<h1>{l s='Your personal information'}</h1>

{include file="$tpl_dir./errors.tpl"}

{if isset($confirmation) && $confirmation}
	<p class="success">
		{l s='Your personal information has been successfully updated.'}
		{if isset($pwd_changed)}<br />{l s='Your password has been sent to your email:'} {$email}{/if}
	</p>
{else}
	<h3>{l s='Please be sure to update your personal information if it has changed.'}</h3>
	
	<form action="{$link->getPageLink('identity', true)|escape:'html'}" method="post" class="std">
		<div class="panel panel-default"><div class="panel-body">
			<div class="form-horizontal form_content">

					<p class="required"><sup>*</sup>{l s='Required field'}</p>
					<div class="form-group">
						<label class="col-md-4 control-label">{l s='Title'}</label>
						<div class="col-md-6">
							{foreach from=$genders key=k item=gender}
								<input type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id|intval}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
								<label for="id_gender{$gender->id}" class="top">{$gender->name}</label>
							{/foreach}
						</div>
					</div>

					<div class="required text form-group">
						<label for="firstname" class="col-md-4 control-label">{l s='First name'} <sup>*</sup></label>
						<div class="col-md-6">
							<input class="form-control" type="text" id="firstname" name="firstname" value="{$smarty.post.firstname}" />
						</div>
					</div>
					<div class="required text form-group">
						<label for="lastname" class="col-md-4 control-label">{l s='Last name'} <sup>*</sup></label>
						<div class="col-md-6">
							<input class="form-control" type="text" name="lastname" id="lastname" value="{$smarty.post.lastname}" />
						</div>	
					</div>
					<div class="required text form-group">
						<label for="email" class="col-md-4 control-label">{l s='Email'} <sup>*</sup></label>
						<div class="col-md-6">
							<input class="form-control" type="text" name="email" id="email" value="{$smarty.post.email}" />
						</div>
					</div>
					<div class="required text form-group">
						<label for="old_passwd" class="col-md-4 control-label">{l s='Current Password'} <sup>*</sup></label>
						<div class="col-md-6">
							<input class="form-control" type="password" name="old_passwd" id="old_passwd" />
						</div>
					</div>
					<div class="password form-group">
						<label for="passwd" class="col-md-4 control-label">{l s='New Password'}</label>
						<div class="col-md-6">
							<input  class="form-control"type="password" name="passwd" id="passwd" />
						</div>
					</div>
					<div class="password form-group">
						<label for="confirmation" class="col-md-4 control-label">{l s='Confirmation'}</label>
						<div class="col-md-6">
							<input class="form-control" type="password" name="confirmation" id="confirmation" />
						</div>
					</div>
					<div class="select form-group">
						<label class="col-md-4 control-label">{l s='Date of Birth'}</label>
						<div class="col-md-6">
							<div class="row">
								<div class="col-sm-3 col-xs-12">
									<select class="form-control" name="days" id="days">
										<option value="">-</option>
										{foreach from=$days item=v}
											<option value="{$v}" {if ($sl_day == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
										{/foreach}
									</select>
								</div>
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
								<div class="col-sm-6 col-xs-12">
									<select class="form-control" id="months" name="months">
										<option value="">-</option>
										{foreach from=$months key=k item=v}
											<option value="{$k}" {if ($sl_month == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
										{/foreach}
									</select>
								</div>
								<div class="col-sm-3 col-xs-12">
									<select class="form-control" id="years" name="years">
										<option value="">-</option>
										{foreach from=$years item=v}
											<option value="{$v}" {if ($sl_year == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
										{/foreach}
									</select>
								</div>
							</div>
						</div>	
					</div>
					{if $newsletter}
					<div class="form-group">
						<div class="col-sm-8 col-md-offset-4">
							<div class="checkbox">
								<label for="newsletter">
									<input type="checkbox" id="newsletter" name="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if} autocomplete="off"/>						
								{l s='Sign up for our newsletter!'}</label>
							</div>
						</div>
					</div>
					<div class=" form-group">
						<div class="col-sm-8 col-sm-offset-4"> 
							<div class="checkbox">
								<label for="optin"><input type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if} autocomplete="off"/>
								{l s='Receive special offers from our partners!'}</label>
							</div>
						</div>
					</div>
					{/if}
					<div class="submit form-group">
						<div class="col-sm-8 col-sm-offset-4">
							<input type="submit" class="button" name="submitIdentity" value="{l s='Save'}" />
						</div>	
					</div>
			</div>		
		</div></div>
	</form>
{/if}

<ul class="footer_links clearfix">
	<li class="pull-left"><a href="{$link->getPageLink('my-account', true)|escape:'html'}"><span class="fa fa-user"></span>{l s='Back to your account'}</a></li>
	<li class="pull-right"><a href="{$base_dir}"><span class="fa fa-home"></span> {l s='Home'}</a></li>
</ul>
