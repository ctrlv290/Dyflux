<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 로그인 페이지
 */

//Init
include_once "./_init_.php";

$C_ListTable = new ListTable();

$bbs_id = "notice";

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.bbs_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$search_column = $_GET["search_column"];
$search_keyword = $_GET["search_keyword"];

//검색 가능한 컬럼 지정
$available_col = array(
	"bbs_title",
	"bbs_contents",
);
$qryWhereAry = array();
if($search_column && search_keyword)
{
	if(in_array($search_column, $available_col)){
		$qryWhereAry[] = " $search_column like N'%".$search_keyword."%'";
	}
}

$date_start = $_GET["date_start"];
$date_end = $_GET["date_end"];

if($date_start) $qryWhereAry[] = " bbs_regdate >= '$date_start'";
if($date_end) $qryWhereAry[] = " bbs_regdate <= '$date_end'";

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;


// make select query
$args['qry_table_idx'] 	= "A.bbs_idx";
$args['qry_get_colum'] 	= " A.*
							, C.code_name as target_name
							, CC.code_name as category_name
                            ";

$args['qry_table_name'] 	= " DY_BBS A 
								Left Outer Join DY_MEMBER M On A.member_idx = M.idx 
								Left Outer Join DY_CODE C On C.parent_code = N'BBS_TARGET' And C.code = A.bbs_target
								Left Outer Join DY_CODE CC On CC.parent_code = N'BBS_NOTICE_CATEGORY' And CC.code = A.bbs_category
";
$args['qry_where']			= " A.bbs_is_del = N'N' And A.bbs_id = 'notice' And A.bbs_is_main = N'Y' And bbs_target = N'ALL'";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_groupby']		= "";
$args['qry_orderby']		= $order_by;

// image set
$args['search_img'] 		= "";
$args['search_img_tag']		= "";
$args['front_img'] 			= "";
$args['next_img'] 			= "";

$args['add_element']		= "";
$args['seeQry'] 			= "0";

$args['addFormStr'] 		= '';

$WholeGetListResult = $C_ListTable -> WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $WholeGetListResult['listRst'];
$listRst_cnt 		= count($listRst);

$startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;


$article_number = $WholeGetListResult['pageInfo']['total'];
$article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page-1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>dyflux</title>
	<meta property="og:title" content=""/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content=""/>
	<meta property="og:image" content=""/>
	<meta property="og:description" content=""/>
	<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

	<link rel="stylesheet" type="text/css" href="css/main_reset.css"/>
	<link rel="stylesheet" type="text/css" href="css/main_fonts.css"/>
	<link rel="stylesheet" type="text/css" href="/css/slick.css"/>
	<link rel="stylesheet" type="text/css" href="/css/main.css"/>

	<script type="text/javascript" src="/js/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="/js/slick.min.js"></script>
</head>
<body>

<div class="notice_list_set">
	<div class="inner_set">
		<table class="notice_table">
			<thead>
			<tr>
				<th class="w01">NO</th>
				<th class="w02">제목</th>
				<th class="w03">등록일</th>
				<th class="w04">조회수</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($listRst as $row) {
				$is_new = '';
				$dt = new DateTime();
				$dt->setTimestamp(strtotime($row["bbs_regdate"]));
				$now = new DateTime();

				if($now->diff($dt)->days < 8){
					$is_new = '<span class="new"></span>';
				}
				?>
				<tr>
					<td><?=$article_number?></td>
					<td class="bold">
						<a href="notice_view.php?bbs_idx=<?=$row["bbs_idx"]?>&page=<?=$page?>" class="link"><?=$row["bbs_title"]?></a>
					</td>
					<td><?=date('Y.m.d', strtotime($row["bbs_regdate"]))?></td>
					<td><?=number_format($row["bbs_read"])?></td>
				</tr>
				<?php
				$article_number--;
			}
			?>
			</tbody>
		</table>
	</div>

	<div class="paging_set">
		<?=$WholeGetListResult["listPageLinkMain"]?>
	</div>
</div>

</body>
</html>