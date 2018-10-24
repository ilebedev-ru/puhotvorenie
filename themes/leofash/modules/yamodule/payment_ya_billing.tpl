{**
* Module is prohibited to sales! Violation of this condition leads to the deprivation of the license!
*
* @category  Front Office Features
* @package   Yandex Payment Solution
* @author    Yandex.Money <cms@yamoney.ru>
* @copyright © 2015 NBCO Yandex.Money LLC
* @license   https://money.yandex.ru/doc.xml?id=527052
*}

{if $DATA_BILLING && $DATA_BILLING['YA_BILLING_ACTIVE']}
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <p class="payment_module">
                <a href="{$link->getModuleLink('yamodule', 'redirect', ['type' => 'yabilling'])|escape:'quotes':'UTF-8'}" title="{l s='Payment with Yandex.Billing' mod='yamodule'}" class="yandex_money_wallet">
                    {l s='Payment with Yandex.Billing' mod='yamodule'}
                </a>
            </p>
        </div>
    </div>
{/if}