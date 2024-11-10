<?php

$pageMenuIdx = 303;
$pagePermissionIdx = 303;

include_once "../_init_.php";

$listTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$searchParam = $_GET["param"];
if($searchParam)
{
    $searchParam = urldecode($searchParam);
    $_search_paramAry = explode("&", $searchParam);

    foreach($_search_paramAry as $sitem) {
        list($col, $val) = explode("=", $sitem);

        if(trim($val) && $col == "kind_name") {
            $qryWhereAry[] = $col . " like N'%" . $val . "%'";
        }
    }
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] = "A.seller_idx";
$args['qry_get_colum'] = " A.*, S.seller_name";
$args['qry_table_name'] = " 
		(
			SELECT 
				CASE
					WHEN CTE_S.seller_idx IS NOT NULL 
					THEN CTE_S.seller_idx 
					ELSE CTE_A.seller_idx 
				END AS seller_idx, 
				CTE_S.ad_amount, CTE_A.cost, IFNULL(CTE_S.ad_amount, 0) - IFNULL(CTE_A.cost, 0) AS total
			FROM
			(
				SELECT ad_date, seller_idx, SUM(ad_amount) as ad_amount
				FROM DY_SETTLE_AD_COST 
				WHERE
					ad_inout = 1
					AND ad_is_del = N'N'
				GROUP BY seller_idx
			)AS CTE_S
				LEFT JOIN 
				(
					SELECT seller_idx, SUM(cost) AS cost
					FROM DY_AD_DATA AS AD
						JOIN DY_AD_KINDS AS K ON AD.kind_idx = K.idx
					WHERE
						AD.is_del = N'N'
					GROUP BY seller_idx
				) AS CTE_A ON CTE_S.seller_idx = CTE_A.seller_idx
			UNION
			SELECT 
				CASE
					WHEN CTE_S.seller_idx IS NOT NULL 
					THEN CTE_S.seller_idx 
					ELSE CTE_A.seller_idx 
				END AS seller_idx, 
				CTE_S.ad_amount, CTE_A.cost, IFNULL(CTE_S.ad_amount, 0) - IFNULL(CTE_A.cost, 0) AS total
			FROM 
			(
				SELECT seller_idx, SUM(cost) AS cost
				FROM DY_AD_DATA AS AD
					JOIN DY_AD_KINDS AS K ON AD.kind_idx = K.idx
				WHERE
					AD.is_del = N'N'
				GROUP BY seller_idx
			) AS CTE_A
				LEFT JOIN 
				(
					SELECT ad_date, seller_idx, SUM(ad_amount) as ad_amount
					FROM DY_SETTLE_AD_COST 
					WHERE
						ad_inout = 1
						AND ad_is_del = N'N'
					GROUP BY seller_idx
				) AS CTE_S ON CTE_A.seller_idx = CTE_S.seller_idx
		) AS A
			JOIN DY_SELLER AS S ON A.seller_idx = S.seller_idx
";

$args['qry_groupby'] = "";
$args['qry_orderby'] = "";

// image set
$args['search_img'] = "";
$args['search_img_tag'] = "";
$args['front_img'] = "";
$args['next_img'] = "";

$args['add_element'] = "";
$args['seeQry'] = "0";

$args['addFormStr'] = '';

$WholeGetListResult = $listTable -> WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $WholeGetListResult['listRst'];
$listRst_cnt 		= count($listRst);

$startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;

$article_number = $WholeGetListResult['pageInfo']['total'];
$article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page-1));

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];

echo json_encode($grid_response, true);
