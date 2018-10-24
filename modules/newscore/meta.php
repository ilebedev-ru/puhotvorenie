<?php
if ($id_entry = Tools::getValue('id_entry')) {
    $row = Db::getInstance()->getRow('
    SELECT nwl.`meta_title`, nwl.`meta_description`, nwl.`meta_keywords`
    FROM `' . _DB_PREFIX_ . 'news_lang` nwl
	LEFT JOIN `' . _DB_PREFIX_ . 'news` nw
	ON nwl.`id_entry` = nw.`id_entry`
    WHERE nw.`status` = 1 AND nwl.`id_lang` = ' . intval($cookie->id_lang) . ' AND nwl.`id_entry` = ' . intval($id_entry));
    if ($row) {
        $row['meta_title'] = $row['meta_title'] . ' - ' . Configuration::get('PS_SHOP_NAME');
        $meta = Tools::completeMetaTags($row, $row['meta_title']);
    }
} elseif ($category_id = Tools::getValue('category_id')) {
    $row = Db::getInstance()->getRow('
    SELECT ncl.`meta_title`, ncl.`meta_description`, ncl.`meta_keywords`
    FROM `' . _DB_PREFIX_ . 'newscategories_lang` ncl
	LEFT JOIN `' . _DB_PREFIX_ . 'newscategories` nc
	ON ncl.`id_category` = nc.`id_category`
    WHERE nc.`status` = 1 AND ncl.`id_lang` = ' . intval($cookie->id_lang) . ' AND ncl.`id_category` = ' . intval($category_id));
    if ($row) {
        $row['meta_title'] = $row['meta_title'] . ' - ' . Configuration::get('PS_SHOP_NAME');
        $meta = Tools::completeMetaTags($row, $row['meta_title']);
    }
}

// Override default PrestaShop Meta Information
// (eliminating the need to make changes to classes/Tools.php file)
if(isset($meta) && is_array($meta)) {
	foreach($meta as $key => $value) {
		if($value != '') {
			$smarty->assign($key, $value);
		}
	}
}
?>
