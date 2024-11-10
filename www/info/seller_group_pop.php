<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 그룹 팝업 [manage_group_pop.php 를 Include]
 */

//Page Info
$pageMenuIdx = 159;
//Permission IDX
$pagePermissionIdx = 43;
//Init
include_once "../_init_.php";

//공통 그룹 관리 페이지 Include
$manage_group_type = "SELLER_GROUP";
include_once "./manage_group_pop.php";
?>