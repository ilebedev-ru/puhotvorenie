<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
// if (!defined('_PS_ADMIN_DIR_')) {
    // define('_PS_ADMIN_DIR_', getcwd());
// }
// include(_PS_ADMIN_DIR_.'/../config/config.inc.php');
// /* Getting cookie or logout */
// require_once(_PS_ADMIN_DIR_.'/init.php');

include('../../config/config.inc.php');

$query = Tools::getValue('q', false);
if (!$query or $query == '' or strlen($query) < 1) {
    return false;
}

/*
 * In the SQL request the "q" param is used entirely to match result in database.
 * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
 * they are no return values just because string:"(ref : #ref_pattern#)"
 * is not write in the name field of the product.
 * So the ref pattern will be cut for the search request.
 */

// Excluding downloadable products from packs because download from pack is not supported
$items = Category::searchByName(Context::getContext()->language->id, $query, false, false);
$results = array();
if (is_array($items)) {
    foreach ($items as $item) {
        $category = array(
            'id' => (int)($item['id_category']),
            'name' => $item['name'],
            'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
            'image' => '',
        );
        array_push($results, $category);
    }
    $results = array_values($results);
    echo Tools::jsonEncode($results);
}else{
    return false;
}

