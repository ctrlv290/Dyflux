<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 디자인게시판 리스트 페이지
 */
//Page Info
$pageMenuIdx = 149;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();

$bbs_id = "design";

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
if($search_column && $search_keyword)
{
	if(in_array($search_column, $available_col)){
		$qryWhereAry[] = " $search_column like N'%".$search_keyword."%'";
	}
}

global $GL_Member;
$addQry = "";
if(!isDYLogin()) {
	if ($GL_Member["member_type"] == "VENDOR") {
		$qryWhereAry[] =  "
		                bbs_target_vendor_" .$GL_Member["vendor_grade"]. " = N'Y'
					";
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
$args['qry_where']			= " A.bbs_is_del = N'N' And A.bbs_id = '$bbs_id'";
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

//등록 가능 여부
$canWrite = false;
if($GL_Member["member_type"] == "USER"){
	$canWrite = true;
}

//페이지 파라미터
$page_param_column_ary = array("page", "category", "date_start", "date_end", "search_column", "search_keyword");
$page_param_ary = array();
foreach($page_param_column_ary as $col) $page_param_ary[] = $col . "=" . $_GET[$col];
$page_parameters = implode("&", $page_param_ary);
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="w900px">
			<form name="searchForm" id="searchForm" method="get">
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">
								<span class="text">기간</span>
								<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
								~
								<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							</div>
							<div class="finder_col">
								<select name="search_column">
									<option value="bbs_title" <?=($search_column == "bbs_title") ? "selected" : ""?>>제목</option>
									<option value="bbs_contents" <?=($search_column == "bbs_contents") ? "selected" : ""?>>내용</option>
								</select>
								<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
							</div>
						</div>
					</div>
					<div class="find_btn">
						<div class="table">
							<div class="table_cell">
								<a href="javascript:;" id="btn_searchBar" class="wide_btn  btn_default">검색</a>
							</div>
						</div>
					</div>
					<a href="javascript:;" class="find_hide_btn">
						<i class="fas fa-angle-up up_btn"></i>
						<i class="fas fa-angle-down dw_btn"></i>
					</a>
				</div>
			</form>
			<!--
			<p class="sub_tit">신규가입회원 <span class="red_strong">5</span>건 목록</p>
			<p class="sub_desc">총회원수 <span class="red_strong">1,255</span>명 중 차단 <span class="strong">0</span>명, 탈퇴 : <span class="strong">18</span>명</p>
			-->
			<div>
				<?php if($canWrite){?>
					<a href="design_write.php" class="btn btn-write">신규등록</a>
				<?php }?>
			</div>
			<div class="tb_wrap">
				<table>
					<colgroup>
						<col width="80" />
						<col width="120" />
						<col width="*" />
						<col width="120" />
						<col width="80" />
					</colgroup>
					<thead>
					<tr>
						<th>No</th>
						<th>카테고리</th>
						<th>제목</th>
						<th>등록일</th>
						<th>조회수</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($listRst as $row) {
						?>
						<tr>
							<td><?=$article_number?></td>
							<td><?=$row["category_name"]?></td>
							<td class="text_left ellipsis">
								<a href="design_view.php?bbs_id=<?=$bbs_id?>&bbs_idx=<?=$row["bbs_idx"]?>&<?=$page_parameters?>" class="link"><?=htmlentities_utf8($row["bbs_title"])?></a>
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
			<div class="pg_wrap">
				<?=$WholeGetListResult["listPageLink"]?>
				<!--				<ul>-->
				<!--					<li><a href="javascript:;" class="pg_ll"><i class="fas fa-angle-double-left"></i></a></li>-->
				<!--					<li><a href="javascript:;" class="pg_l"><i class="fas fa-angle-left"></i></a></li>-->
				<!--					<li><a href="javascript:;" class="active">1</a></li>-->
				<!--					<li><a href="javascript:;" class="">2</a></li>-->
				<!--					<li><a href="javascript:;" class="">3</a></li>-->
				<!--					<li><a href="javascript:;" class="">4</a></li>-->
				<!--					<li><a href="javascript:;" class="">5</a></li>-->
				<!--					<li><a href="javascript:;" class="">6</a></li>-->
				<!--					<li><a href="javascript:;" class="">7</a></li>-->
				<!--					<li><a href="javascript:;" class="">8</a></li>-->
				<!--					<li><a href="javascript:;" class="">9</a></li>-->
				<!--					<li><a href="javascript:;" class="">10</a></li>-->
				<!--					<li><a href="javascript:;" class="pg_r"><i class="fas fa-angle-right"></i></a></li>-->
				<!--					<li><a href="javascript:;" class="pg_rr"><i class="fas fa-angle-double-right"></i></a></li>-->
				<!--				</ul>-->
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/help.js"></script>
<script>
	Help.DesignListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

