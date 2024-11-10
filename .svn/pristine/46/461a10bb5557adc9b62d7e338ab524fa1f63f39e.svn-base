<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 단축URL 실행 페이지
 */

//Init
include_once "../_init_.php";

$url = isset($_GET['url']) ? urldecode(trim($_GET['url'])) : '';

$url = "/product/product_sasdasdfasfafdsfasdfasasadfas12312list.php?a=3";

echo $url;

$C_UrlShortener = new UrlShortener();

$slug = $C_UrlShortener->makeSlug($url);

echo $slug;

?>