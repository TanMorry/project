<?php
/**
 * @copyright   2008-2015 简好网络 <http://www.phpshe.com>
 * @creatdate   2012-0501 koyshe <koyshe@gmail.com>
 */
include('common.php');
$cache_category = cache::get('category');
$cache_category_arr = cache::get('category_arr');

$cache_brand = cache::get('brand');
//var_dump($cache_brand);
pe_lead('hook/product.hook.php');
pe_lead('hook/category.hook.php');

//echo $mod;
//echo $module;
//echo $pe['path_root']."module/".$module."/".$mod.".php";
//var_dump($pe);
if (in_array("{$mod}.php", pe_dirlist("{$pe['path_root']}module/{$module}/*.php"))) {
	include("{$pe['path_root']}module/{$module}/{$mod}.php");
}
pe_result();
?>