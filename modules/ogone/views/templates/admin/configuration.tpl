{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="configuration_wrapper" class="ogone-panel">
    <div class="sticky-help">
      <div class="step-text">
        <strong>{l s='Need assistance?' mod='ogone'}</strong> {l s='Even if you are not an Ingenico customer ' mod='ogone'}<br />
          {l s='you can create ' mod='ogone'}  <a href="{$support_url|escape:'htmlall':'UTF-8'}" target="_blank">{l s='a ticket' mod='ogone'}</a>
          {l s='or contact us' mod='ogone'} <a href="mailto:{$support_email|escape:'htmlall':'UTF-8'}">{l s='by email' mod='ogone'}</a>.
      </div>
    </div>
    <div class="full-block">
        <form action="{$form_action|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data">
            <div class="ogone-config-block">
                <h2>{l s='Basic configuration' mod='ogone'}</h2>
                <div class="row">
                    <div class="quarter-block-3">
                        <p class="ogone-subtitle">{l s='This is the configuration necessary to use Ingenico ePayments' mod='ogone'}</p>
                        <section>
                            <!-- OGONE_PSPID -->
                            <div class="form-group">
                                <label class="control-label">{l s='PSPID' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="text" name="OGONE_PSPID" id="OGONE_PSPID" value="{$OGONE_PSPID|escape:'htmlall':'UTF-8'}" />
                                    <div class="ogone-help">{l s='The PSPID is the Merchant ID chosen by the merchant administrator when opening the account' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_SHA_IN -->
                            <div class="form-group">
                                <label class="control-label">{l s='SHA-IN signature' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="text" name="OGONE_SHA_IN" id="OGONE_SHA_IN" value="{$OGONE_SHA_IN|escape:'htmlall':'UTF-8'}" /><br />
                                    <div class="ogone-help">{l s='SHA-IN signature can be defined in your Ingenico ePayments backoffice in Configuration/Technical information/Data and origin verification/Checks for e-commerce/SHA-IN pass phrase' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_SHA_OUT -->
                            <div class="form-group">
                                <label class="control-label">{l s='SHA-OUT signature' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="text" name="OGONE_SHA_OUT" id="OGONE_SHA_OUT" value="{$OGONE_SHA_OUT|escape:'htmlall':'UTF-8'}" /><br />
                                    <div class="ogone-help">{l s='SHA-OUT signature can be defined in your Ingenico ePayments backoffice' mod='ogone'}<br /> {l s='Look in Configuration/Technical information/Transaction feedback/SHA-OUT pass phrase' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_MODE -->
                            <div class="form-group">
                                <label class="control-label">{l s='Mode' mod='ogone'}</label>
                                <div class="control-field">
                                    <div class="ogone-radio-block">
                                        <input type="radio" id="OGONE_MODE_TEST" name="OGONE_MODE" value="0" {if $OGONE_MODE != 1}checked="checked"{/if} />
                                        <label for="OGONE_MODE_TEST">{l s='Test' mod='ogone'}</label>
                                    </div>
                                    <div class="ogone-radio-block">
                                        <input type="radio" id="OGONE_MODE_PRODUCTION" name="OGONE_MODE" value="1" {if $OGONE_MODE == 1}checked="checked"{/if} />
                                        <label for="OGONE_MODE_PRODUCTION">{l s='Production' mod='ogone'}</label>
                                    </div>
                                    <div class="ogone-help">{l s='You need visit your Ingenico ePayments backoffice to transfer your test account into production' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_OPERATION -->
                            <div class="form-group">
                                <label class="control-label">{l s='Default operation type' mod='ogone'}</label>
                                <div class="control-field">
                                    <div class="ogone-radio-block">
                                        <input type="radio" id="OGONE_OPERATION_SALE" name="OGONE_OPERATION" value="SAL" {if $OGONE_OPERATION == 'SAL'}checked="checked"{/if} />
                                        <label for="OGONE_OPERATION_SALE">{l s='Direct sale' mod='ogone'}</label>
                                    </div>
                                    <div class="ogone-radio-block">
                                        <input type="radio" id="OGONE_OPERATION_AUTH" name="OGONE_OPERATION" value="RES" {if $OGONE_OPERATION == 'RES'}checked="checked"{/if} />
                                        <label for="OGONE_OPERATION_AUTH">{l s='Authorise' mod='ogone'}</label>
                                    </div>
                                    <div class="ogone-help">{l s='If you choose "authorise" option, payment will be finalised only after order capture' mod='ogone'}</div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="quarter-block block-info">
                        <div class="inner">
                            <h4>{l s='In order to use basic payment capabilities, you need to configure properly your Ingenico ePayments backoffice' mod='ogone'}</h4>
                            <ul>
                                <li>{l s='You need to verify that e-commerce option is activated for your contract.' mod='ogone'}</li>
                                <li>{l s='You need to set SHA-IN and SHA-OUT variables properly' mod='ogone'}</li>
                                <li>{l s='You need to declare validation url in Ingenico ePayments backoffice' mod='ogone'}
                                        <br />{l s='Your validation url' mod='ogone'}:
                                        <br /><span class="long-url"><strong>{$validation_url|escape:'htmlall':'UTF-8'}</strong></span>
                                </li>
                                <li>{l s='You need to declare confirmation url in Ingenico ePayments backoffice' mod='ogone'}
                                        <br />{l s='Your confirmation url' mod='ogone'}:
                                        <br /><span class="long-url"><strong>{$confirmation_url|escape:'htmlall':'UTF-8'}</strong></span>
                                </li>
                                <li>{l s='If you have any questions, please contact our support' mod='ogone'}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr/>

            <div class="ogone-config-block">
                <h2>{l s='DirectLink configuration' mod='ogone'}</h2>

                <div class="row">
                    <div class="quarter-block-3">
                        <p class="ogone-subtitle">{l s='This part will allow you to use DirectLink' mod='ogone'}</p>

                        <section>

                            <!-- OGONE_USE_DL -->
                            <div class="form-group">
                                <label class="control-label">{l s='Allow usage of Direct Link' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="checkbox" id="OGONE_USE_DL" name="OGONE_USE_DL" {if $OGONE_USE_DL}checked="checked"{/if} />
                                    <div class="ogone-help">{l s='If you choose this option, operations like capture could be handled directly from your Prestashop backoffice.' mod='ogone'}</div>
                                </div>
                            </div>


                            <!-- OGONE_DL_USER -->
                            <div class="form-group">
                                <label class="control-label">{l s='DirectLink user' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="text" name="OGONE_DL_USER" id="OGONE_DL_USER" value="{$OGONE_DL_USER|escape:'htmlall':'UTF-8'}" />
                                    <div class="ogone-help">{l s='You need to create separate user with special permissions to use DirectLink' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_DL_PASSWORD -->
                            <div class="form-group">
                                <label class="control-label">{l s='DirectLink password' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="text" name="OGONE_DL_PASSWORD" id="OGONE_DL_PASSWORD" value="{$OGONE_DL_PASSWORD|escape:'htmlall':'UTF-8'}" />
                                    <div class="ogone-help">{l s='This is a password associated with DirectLink user' mod='ogone'}</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label">{l s='DirectLink SHA-IN signature' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="text" name="OGONE_DL_SHA_IN" id="OGONE_DL_SHA_IN" value="{$OGONE_DL_SHA_IN|escape:'htmlall':'UTF-8'}" />
                                    <div class="ogone-help">{l s='DirectLink SHA-IN signature can be defined in your Ingenico ePayments backoffice in Configuration/Technical information/Data and origin verification/Checks for DirectLink/SHA-IN pass phrase' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_DL_TIMOEUT -->
                            <div class="form-group">
                                <label class="control-label">{l s='DirectLink request timeout' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="number" min="0" max="300" name="OGONE_DL_TIMOEUT" id="OGONE_DL_TIMOEUT" value="{$OGONE_DL_TIMOEUT|escape:'htmlall':'UTF-8'}" />
                                    <div class="ogone-help">{l s='Maximal number of seconds to wait for DirectLink response' mod='ogone'}</div>
                                </div>
                            </div>

                        </section>
                    </div>

                    <div class="quarter-block block-info">
                        <div class="inner">
                            <h4>{l s='In order to use DirectLink capabilities, you need to configure properly your Ingenico ePayments backoffice' mod='ogone'}</h4>
                            <ul>
                                <li>{l s='You need to verify that DirectLink option is activated for your contract.' mod='ogone'}</li>
                                <li>{l s='You need to create an API user' mod='ogone'} {l s='See' mod='ogone'} <a href="{$direct_link_doc_url|escape:'html':'UTF-8'}" target="_blank">{l s='our documentation' mod='ogone'} </a> {l s='for more details' mod='ogone'}</li>
                                <li>{l s='You need to set API user, API user password, DirectLink SHA_IN' mod='ogone'}</li>
                                <li>{l s='You need to add your server\'s IP to whitelist in Ingenico ePayments backoffice. Contact your hosting provider if you don\'t know IP of your server.' mod='ogone'}
                                {if $server_ip}{l s='It seems that this server\'s IP is' mod='ogone'}  <strong>{$server_ip|escape:'html':'UTF-8'}</strong>{else}{l s='We cannot determine your server IP' mod='ogone'} {/if}</li>
                                <li>{l s='If you have any questions, please contact our support' mod='ogone'}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr/>

            <div class="ogone-config-block">
                <h2>{l s='Alias configuration' mod='ogone'}</h2>

                <div class="row">
                    <div class="quarter-block-3">
                        <p class="ogone-subtitle">{l s='This part will allow your customers to store they card data on Ingenico servers' mod='ogone'}</p>

                        <section>
                            <!-- OGONE_USE_ALIAS -->
                            <div class="form-group">
                                <label class="control-label">{l s='Allow Alias utilisation' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="checkbox" id="OGONE_USE_ALIAS" name="OGONE_USE_ALIAS" {if $OGONE_USE_ALIAS}checked="checked"{/if} />
                                    <div class="ogone-help">{l s='If you choose this option, your customers will have possibility to store their card credentials on Ingenico ePayments servers.' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_ALIAS_SHA_IN -->
                            <div class="form-group">
                                <label class="control-label">{l s='Alias SHA-IN signature' mod='ogone'}</label>
                                <div class="control-field">
                                    <input type="text" name="OGONE_ALIAS_SHA_IN" id="OGONE_ALIAS_SHA_IN" value="{$OGONE_ALIAS_SHA_IN|escape:'htmlall':'UTF-8'}" />
                                    <div class="ogone-help">{l s='Alias Gateway SHA-IN signature can be defined in your Ingenico ePayments backoffice in Configuration/Technical information/Data and origin verification/Checks for Alias Gateway/SHA-IN pass phrase' mod='ogone'}</div>
                                </div>
                            </div>

                            <!-- OGONE_ALIAS_PM -->
                            <div class="form-group">
                                <label class="control-label">{l s='Type of proposed payment method' mod='ogone'}</label>
                                <div class="control-field">
                                    <select name="OGONE_ALIAS_PM" id="OGONE_ALIAS_PM"/>
                                        <option value="CreditCard" {if $OGONE_ALIAS_PM=='CreditCard'}selected="selected"{/if}>{l s='CreditCard' mod='ogone'}</option>
                                        <option value="DirectDebit" {if $OGONE_ALIAS_PM=='DirectDebit'}selected="selected"{/if}>{l s='DirectDebit' mod='ogone'}</option>
                                    </select>
                                    <div class="ogone-help">{l s='Type of payment method proposed' mod='ogone'}</div>
                                </div>
                            </div>


                        </section>
                    </div>

                    <div class="quarter-block block-info">
                        <div class="inner">
                            <h4>{l s='In order to use Alias capabilities, you need to configure properly your Ingenico ePayments backoffice' mod='ogone'}</h4>
                            <ul>
                                <li>{l s='You need to verify that Alias option is activated for your contract.' mod='ogone'}</li>
                                <li>{l s='You need to set Alias dynamic variables properly' mod='ogone'}</li>
                                <li>{l s='You can send us CSS file to style alias creation form' mod='ogone'}</li>
                                <li>{l s='If you have any questions, please contact our support' mod='ogone'}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ogone-config-block">
                <section>
                    <div class="form-group ogone-submit">
                        <input type="submit" name="submitOgone" value="{l s='Update settings' mod='ogone'}" />
                    </div>
                </section>
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>