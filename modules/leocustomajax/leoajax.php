<?php
/*
* 2011-2013 LeoTheme
*
*/
// get number of category
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/leocustomajax.php');

//process category
$listCat = Tools::getValue("cat_list");
$listPro = Tools::getValue("pro_list");
$leoProInfo = Tools::getValue("pro_info");
$leoProAdd = Tools::getValue("pro_add");

$result = array();
$leoProductInfo = new Leocustomajax();

if($listCat){
    $listCat = explode(",", $listCat);
    $listCat = array_unique($listCat);
    $listCat = implode(",", $listCat);
    
    $sql = 'SELECT COUNT(cp.`id_product`) AS total, cp.`id_category`
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` IN ('.$listCat.')'.
					' AND product_shop.`visibility` IN ("both", "catalog")'.
					' AND product_shop.`active` = 1'.
                                        ' GROUP BY cp.`id_category`';
    $cat = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    if($cat) $result["cat"] = $cat;
}
if($listPro){
    $listPro = explode(",", $listPro);
    $listPro = array_unique($listPro);
    $listPro = implode(",", $listPro);
    
    //get rating time detail
    $listGrades = Leocustomajax::getGradeByProducts($listPro);
    //get total rating time
    $listTotal  = Leocustomajax::getGradedCommentNumber($listPro);
    
    //conver to product id
    $productGrades = array ();
    foreach ($listGrades as $listGrade){
        $productGrades[$listGrade["id_product"]][] = $listGrade;
    }
    
    //convert to product id
    $productTotal = array ();
    foreach ($listTotal as $listTol){
        $productTotal[$listTol["id_product"]] = $listTol["nbr"];
    }
    
    //rating buy criterion
    $listAverages = array();
    foreach ($productGrades as $key=>$listGrade){
        $criterionsGradeTotal = array();
        $count_grades = count($listGrade);
       
        for ($i = 0; $i < $count_grades; ++$i)
            if (array_key_exists($listGrade[$i]['id_product_comment_criterion'], $criterionsGradeTotal) === false)
                    $criterionsGradeTotal[$listGrade[$i]['id_product_comment_criterion']] = (int)($listGrade[$i]['grade']);
            else
                    $criterionsGradeTotal[$listGrade[$i]['id_product_comment_criterion']] += (int)($listGrade[$i]['grade']);
           
        /* Finally compute the averages */
        $averages = array();
        
        $total = $productTotal[$key];
        
        foreach ($criterionsGradeTotal as $key1 => $criterionGradeTotal)
			$averages[(int)($key1)] = (int)($total) ? ((int)($criterionGradeTotal) / (int)($total)) : 0;
        $listAverages[$key] = $averages;
    }
    
    //criterions
    $list_product_average = array();
    foreach($listAverages as $key=>$averages){
        $criterions = Leocustomajax::getByProduct($key);
        $grade_total = 0;

        if (count($averages) > 0)
        {
            foreach ($criterions as $criterion)
            {
                if (isset($averages[$criterion['id_product_comment_criterion']]))
                {
                    $grade_total += (float)($averages[$criterion['id_product_comment_criterion']]);
                }
            }
            
            $product_average = $grade_total / count($criterions);
            $list_product_average[$key] = round($product_average);
        }
    }
    $result["pro"] = array();
    if($list_product_average)
        foreach ($list_product_average as $key=>$val){
            $result["pro"][] = array("id"=>$key,"rate"=>$val);
        }
}

if($leoProInfo){
    $leoProInfo = explode(",", $leoProInfo);
    $leoProInfo = array_unique($leoProInfo);
    $leoProInfo = implode(",", $leoProInfo);
    
    //$leocustomajax = new Leocustomajax();
    $result["pro_info"] = $leoProductInfo->hookProductMoreImg($leoProInfo);
}
if($leoProAdd){
    $leoProAdd = explode(",", $leoProAdd);
    $leoProAdd = array_unique($leoProAdd);
    $leoProAdd = implode(",", $leoProAdd);
    
    $result["pro_add"] = $leoProductInfo->hookProductOneImg($leoProAdd);
}

if($result && ($listCat||$listPro||$leoProInfo ||$leoProAdd))
    die(Tools::jsonEncode($result));