<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 업무게시판 리스트 페이지
 */
//Page Info
$pageMenuIdx = 210;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();

$bbs_id = "work";

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.bbs_ref DESC, A.bbs_step ASC, A.bbs_level ASC";
//if($_GET["sidx"] && $_GET["sord"])
//{
//	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
//}

$bbs_category = $_GET["category"];

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


if(!empty($bbs_category)){
	$qryWhereAry[] = " bbs_category = N'$bbs_category'";
}

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
							, Case When name is not null Then name
									When S.supplier_name is not null Then supplier_name
									When V.vendor_name is not null Then vendor_name
								End as bbs_name
                            ";

$args['qry_table_name'] 	= " DY_BBS A 
								Left Outer Join DY_MEMBER M On A.member_idx = M.idx 
								Left Outer Join DY_CODE C On C.parent_code = N'BBS_TARGET' And C.code = A.bbs_target
								Left Outer Join DY_CODE CC On CC.parent_code = N'BBS_NOTICE_CATEGORY' And CC.code = A.bbs_category
								Left Outer Join DY_MEMBER_USER U On U.member_idx = A.member_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = A.member_idx 
								Left Outer Join DY_MEMBER_VENDOR V On V.member_idx = A.member_idx
";
$args['qry_where']			= " A.bbs_is_del = N'N' And A.bbs_id = '$bbs_id'";

if($GL_Member["member_type"] != "USER" && $GL_Member["member_type"] != "ADMIN"){
	$view_member_idx = $GL_Member["member_idx"];
	$args['qry_where']		.= " And A.bbs_ref in (Select bbs_idx From DY_BBS Where member_idx = N'$view_member_idx') ";
}

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

$args['add_element']		= "category=$bbs_category";
$args['seeQry'] 			= "0";

$args['addFormStr'] 		= '';

$WholeGetListResult = $C_ListTable -> WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $WholeGetListResult['listRst'];
$listRst_cnt 		= count($listRst);

$startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;


$article_number = $WholeGetListResult['pageInfo']['total'];
$article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page-1));

//공지글 불러오기
$C_BBS = new BBS();
$_top_list = $C_BBS->getTopNoticeBBS($bbs_id);

//등록 가능 여부
$canWrite = true;

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
					<a href="work_write.php" class="btn btn-write">신규등록</a>
				<?php }?>
			</div>
			<div class="tb_wrap">
				<table>
					<colgroup>
						<col width="80" />
						<col width="120" />
						<col width="*" />
						<col width="120" />
						<col width="120" />
						<col width="80" />
					</colgroup>
					<thead>
					<tr>
						<th>No</th>
						<th>카테고리</th>
						<th>제목</th>
						<th>작성자</th>
						<th>등록일</th>
						<th>조회수</th>
					</tr>
					</thead>
					<tbody>
					<?php
					if($_top_list){
						foreach($_top_list as $_top){
					?>
							<tr>
								<td><span class="lb_red">공지</span></td>
								<td><?=$_top["category_name"]?></td>
								<td class="text_left ellipsis">
									<a href="work_view.php?bbs_id=<?=$bbs_id?>&bbs_idx=<?=$_top["bbs_idx"]?>&<?=$page_parameters?>" class="link"><?=htmlentities_utf8($_top["bbs_title"])?></a>
								</td>
								<td><?=$_top["bbs_name"]?></td>
								<td><?=date('Y.m.d', strtotime($_top["bbs_regdate"]))?></td>
								<td><?=number_format($_top["bbs_read"])?></td>
							</tr>
					<?php
						}
					}
					?>
					<?php
					foreach($listRst as $row) {

						$level_icon = "";

						for($i=1;$i<$row["bbs_level"];$i++){
							$level_icon .= '<img src="/images/transparent.gif" width="10" height="1" />';
						}

						if($row["bbs_level"] > 1){
							$level_icon .= "┗ 답변 : ";
						}

						?>
						<tr>
							<td><?=$article_number?></td>
							<td><?=$row["category_name"]?></td>
							<td class="text_left ellipsis">
								<?=$level_icon?>
								<a href="work_view.php?bbs_id=<?=$bbs_id?>&bbs_idx=<?=$row["bbs_idx"]?>&<?=$page_parameters?>" class="link"><?=htmlentities_utf8($row["bbs_title"])?></a>
							</td>
							<td><?=$row["bbs_name"]?></td>
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
	Help.WorkListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

