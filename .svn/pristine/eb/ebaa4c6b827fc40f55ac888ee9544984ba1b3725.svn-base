<?php
/**
 *  List class
 *
 * author      woox
 * version     1.0
 */
/*
select
	top(@rowSize) resultTable.*
from
	(
		select
			top (@rowSize*@currentPage) ROW_NUMBER() over (order by mykey desc) as rownumber
			, field1, field2, field3 from tMyTable
	) as resultTable
*/

// GROUP BY 시 인자가 2개 이상이면 COUNT 에러
class ListTable extends DBConn
{
	function WholeGetListResult ($args)
	{
		extract($args);

		$sortCnt = count($sortArr);
		if($sortBy && $sortType)
		{
			$orderby_list 	= $sortBy;
			$orderby_sort 	= $sortType;

			for($i = 0; $i < $sortCnt ; $i++)
			{
				if($sortBy != $sortArr[$i]  || ($sortBy == $sortArr[$i] && $sortType == "desc"))
					${"sort_".$sortArr[$i]} = "asc";
				else
					${"sort_".$sortArr[$i]} = "desc";
			}
		}
		else
		{
			$orderby_list	 	= $qry_table_idx;
			$orderby_sort 	= "desc";

			for($i = 0; $i < $sortCnt ; $i++)
			{
				${"sort_".$sortArr[$i]} = "asc";
			}
		}

		if($sortBy && $sortType)
		{
			$orderby = $orderby_list." ".$orderby_sort;
		}
		else
		{
			if($qry_orderby)
			{
				$orderby = $qry_orderby;
				$default_orderby = "";
			}
			else
			{
				$orderby = $orderby_list." ".$orderby_sort;
			}
		}
		//******************************* 쿼리 설정 >******************************

		//******************************* 기타 설정 <******************************
		if($searchVar && $searchWord)
		{
			$searchParam = "";
			$searchParam = "searchVar=$searchVar&searchWord=$searchWord";
		}
		if($sortBy && $sortType)
		{
			$sortParam = "";
			$sortParam = "sortBy=$sortBy&sortType=$sortType";
		}
		if($add_element) $add_element .= "&";
		if($searchParam) $searchParam .= "&";
		$addElement 		= $add_element.$searchParam.$sortParam;

		//******************************* 기타 설정 >******************************

		//******************************* 함수 호출 <******************************

		$arr_list				    = $this -> SetListPage($qry_table_name, $qry_table_idx, $qry_get_colum,$qry_groupby, $qry_where, $orderby, $searchVar, $searchArr, $searchWord, $page, $show_page, $show_row, $seeQry, $excelDown, $default_orderby);	//리스트 함수 [0] : 리스트  [1] : 페이지정보
		if($excelDown)
		{
			$this -> GetExcelResult($excelHanArr, $arr_list[0]);
			exit;
		}
		$returnArr['listRst'] 		= $arr_list[0];
		$returnArr['pageInfo'] 		= $arr_list[1];
		$returnArr['listPageLink'] 	= $this -> GetListPageLink($pagename, $page, $returnArr['pageInfo'],$addElement, $front_img, $next_img, $show_page,$list_class,$list_page_script);						//페이지 링크 함수
		$returnArr['listPageLinkMain'] 	= $this -> GetListPageLinkMain($pagename, $page, $returnArr['pageInfo'],$addElement, $front_img, $next_img, $show_page,$list_class,$list_page_script);						//페이지 링크 함수
		$returnArr['searchForm'] 	= $this -> SetSearchForm($pagename,$searchArr,$search_img,$search_img_tag,$addFormStr,$searchWord,$searchVar,$sortBy,$sortType, $add_element);						//검색 폼 함수

		if($page)
			$addPageVal = "page=".$page."&";
		for($i = 0; $i < $sortCnt; $i++)
			$returnArr['sortLink'][$i] 	= $pagename."?".$add_element.$addPageVal.$searchParam."sortBy=".$sortArr[$i]."&sortType=".${"sort_".$sortArr[$i]};


		return $returnArr;
	}

	function SetListPage()
	{
		parent::db_connect();
		$args = func_get_args ();
		$paraArr = array("table_name","table_idx","get_colum","groupby","where","orderby","searchVar","searchPart","searchWord","page","show_page","show_row","seeQry","excelDown","default_orderby");		// 변수명 지정
		$argsCnt = count($args);
		for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

		$values = array_values($searchPart);

		$searchVar = trim($searchVar);
		$searchVar = str_replace(" ","",$searchVar);

		//if($searchVar)
		if(preg_match("/^[1-9][0-9]*$/",$values[$searchWord-1]))
			$replace_type = 0;
		else
			$replace_type = 1;

		$is_column_name = $values[$searchWord-1];

		if (preg_match('/&/',$is_column_name)) {
			$concat_arr = explode('&',$is_column_name);

			$add_where .= "CONCAT(";
			foreach ($concat_arr as $k => $v) {
				$add_bar = ($k)? ',' : '';
				//$add_where .= $add_bar . "REPLACE(".$v.", \' \',\'\')";
				$add_where .= $add_bar . $v;
			}
			$add_where .= ")";

			if($where == "")
			{
				$where = ($searchVar && $replace_type)? "where " . $add_where . " like '%$searchVar%'" : "";
				//echo 'in';
			}
			else
			{
				$where = ($searchVar && $replace_type)? "where ".$where." and " . $add_where . " like '%$searchVar%' " : "where ".$where;
				//echo 'out';
			}
		} else {
			if($where == "")
			{
				$where = ($searchVar && $replace_type)? "where REPLACE(".$is_column_name.", ' ','') like '%$searchVar%'" : "";
				//echo 'in';
			}
			else
			{
				$where = ($searchVar && $replace_type)? "where ".$where." and REPLACE(".$is_column_name.", ' ','') like '%$searchVar%' " : "where ".$where;
				//echo 'out';
			}
		}

		$page	  	= (!$page) ? 1 : $page;
		$startnum 	= ($page - 1) * $show_row;

		//$qry		= ($groupby)? "SELECT COUNT(distinct($groupby)) FROM $table_name $where" : "SELECT COUNT($table_idx) FROM $table_name $where $groupby" ;

		$groupby = ($groupby)? "group by ".$groupby : "";

		$qry		= ($groupby)? "SELECT $table_idx FROM $table_name $where $groupby" : "SELECT COUNT($table_idx) FROM $table_name $where " ;

		//echo $qry;
		if($table_idx && $table_name)
		{
			if($groupby)
				$total		= parent::execSqlNumRow($qry);
			else
				$total		= parent::execSqlOneCol($qry);
		}

		$totalpages	= ceil($total / $show_row);
		$articlecount = $total - (($page - 1) * $show_row) + 1;

		$startpage 	= ((ceil(($page / $show_page) - 0.01) - 1) * $show_page) + 1;
		$endpage   	= $startpage + ($show_page - 1);
		$endpage   	= ($totalpages < $endpage) ? $totalpages : $endpage;
		$prevpage  	= ($startpage != 1) ? $startpage - $show_page : 1;
		$nextpage  	= (($endpage + 1) > $totalpages) ? $totalpages : $endpage + 1;

		if(!$excelDown) $addLimit = "LIMIT $startnum, $show_row";

		// 추가 기본정렬
		if($default_orderby) $default_orderby = " , ".$default_orderby;

		//$qry = "SELECT $get_colum FROM $table_name $where $groupby ORDER BY $orderby $default_orderby $addLimit";

		$qry = "
			Select * From
			(
				Select 
					$get_colum
					,ROW_NUMBER() OVER (
						ORDER BY $orderby
			        ) AS rowNum
		        From $table_name
		        $where $groupby 
			 ) TBL
			 WHERE rowNum BETWEEN ($page - 1) * $show_page + 1
		            AND $page * $show_page
		";


		if($seeQry)
		{
			echo $qry; exit;
		}
		$page_arr = array("startpage"=>$startpage,"endpage"=>$endpage,"prevpage"=>$prevpage,"nextpage"=>$nextpage,"total"=>$total,"searchVar"=>$searchVar,"totalpages"=>$totalpages);

		if($table_idx && $table_name)
			$return_arr_list[0] =  parent::execSqlList($qry);
		$return_arr_list[1] = $page_arr;
		parent::db_close();

		return $return_arr_list;
	}

	// list_page_script
	function GetListPageLink()
	{

		/*
		<a href="#" class="ui basic button"><i class="fast backward icon"></i></a>
		<a href="#" class="ui active basic button">1</a>
		<a href="#" class="ui basic button">2</a>
		<a href="#" class="ui basic button">3</a>
		<a href="#" class="ui basic button">4</a>
		<a href="#" class="ui basic button">5</a>
		<a href="#" class="ui basic button">6</a>
		<a href="#" class="ui basic button">7</a>
		<a href="#" class="ui basic button">8</a>
		<a href="#" class="ui basic button">9</a>
		<a href="#" class="ui basic button">10</a>
		<a href="#" class="ui basic button"><i class="fast forward icon"></i></a>
		*/

		$args = func_get_args ();
		$paraArr = array("pagename","page","page_info_arr","addElement","front_img","next_img","show_page", "list_class", "list_page_script");	// 변수명 지정
		$argsCnt = count($args);
		for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

		foreach($page_info_arr as $key => $value) {											//page 설정 배열을 다시 풀어서 변수로 생성
			${$key} = $value;
		}

		if($total > 0)
		{
			if($show_page < $totalpages)		// 이전 이후 보여주기
			{
				$front = '<i class="fast backward icon"></i>';
				$next = '<i class="fast forward icon"></i>';
			}
			if($addElement) $addElement = "&".$addElement;

			if($front)
			{
				$return_String = "<li><a href='$pagename?page=$prevpage"."$addElement' class='ui basic button'> ";
				$return_String .= $front;
				$return_String .= "</a></li>";
			}

			for($i = $startpage ; $i <= $endpage ; $i++){
				$return_String .= "<li><a href='$pagename?page=$i"."$addElement' class='ui basic button ";
				$return_String .= ($i == $page)? "active":"";
				$return_String .= "'>";
				$return_String .= $i;
				$return_String .= "</a></li>";
			}

			if($next)
			{
				$return_String .= "<li><a href='$pagename?page=$nextpage"."$addElement' class='ui basic button'> ";
				$return_String .= $next;
				$return_String .= "</a></li>";
			}

		}else {
			//$return_String = ($searchVar)?  "<font color='red'><b>$searchVar</b></font> 에 대한 검색 결과가 없습니다.":"등록된 데이터가 없습니다.";
		}

		if ($list_class) {
			$return_String = "<table width='380'><tr><td class='" . $list_class . "' align='center'>" . $return_String . "</td></tr></table>";
		}
		return "<ul>".$return_String."</ul>";
	}

	function GetListPageLinkMain()
	{

		/*
		<button><img src="images/arrow_left2.png" alt="" /></button>
		<button><img src="images/arrow_left.png" alt="" /></button>
		<a href="javascript:;" class="on">1</a>
		<a href="javascript:;">2</a>
		<a href="javascript:;">3</a>
		<a href="javascript:;">4</a>
		<a href="javascript:;">5</a>
		<a href="javascript:;">6</a>
		<a href="javascript:;">7</a>
		<a href="javascript:;">8</a>
		<a href="javascript:;">9</a>
		<a href="javascript:;">10</a>
		<button><img src="images/arrow_right.png" alt="" /></button>
		<button><img src="images/arrow_right2.png" alt="" /></button>
		*/

		$args = func_get_args ();
		$paraArr = array("pagename","page","page_info_arr","addElement","front_img","next_img","show_page", "list_class", "list_page_script");	// 변수명 지정
		$argsCnt = count($args);
		for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

		foreach($page_info_arr as $key => $value) {											//page 설정 배열을 다시 풀어서 변수로 생성
			${$key} = $value;
		}

		if($total > 0)
		{
			if($show_page < $totalpages)		// 이전 이후 보여주기
			{
				$front = '<img src="images/arrow_left.png" alt="" />';
				$next = '<img src="images/arrow_right.png" alt="" />';
			}
			if($addElement) $addElement = "&".$addElement;

			if($front)
			{
				$return_String = "<a href='$pagename?page=$prevpage"."$addElement' class='arrow '> ";
				$return_String .= $front;
				$return_String .= "</a>";
			}

			for($i = $startpage ; $i <= $endpage ; $i++){
				$return_String .= "<a href='$pagename?page=$i"."$addElement' class=' ";
				$return_String .= ($i == $page)? "active":"";
				$return_String .= "'>";
				$return_String .= $i;
				$return_String .= "</a>";
			}

			if($next)
			{
				$return_String .= "<a href='$pagename?page=$nextpage"."$addElement' class='arrow'> ";
				$return_String .= $next;
				$return_String .= "</a>";
			}

		}else {
			//$return_String = ($searchVar)?  "<font color='red'><b>$searchVar</b></font> 에 대한 검색 결과가 없습니다.":"등록된 데이터가 없습니다.";
		}

		if ($list_class) {
			$return_String = "<table width='380'><tr><td class='" . $list_class . "' align='center'>" . $return_String . "</td></tr></table>";
		}
		return "<ul>".$return_String."</ul>";
	}

	function GetListPageLinkOLD()
	{
		$args = func_get_args ();
		$paraArr = array("pagename","page","page_info_arr","addElement","front_img","next_img","show_page", "list_class", "list_page_script");	// 변수명 지정
		$argsCnt = count($args);
		for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

		foreach($page_info_arr as $key => $value) {											//page 설정 배열을 다시 풀어서 변수로 생성
			${$key} = $value;
		}

		if($total > 0)
		{
			if($show_page < $totalpages)		// 이전 이후 보여주기
			{
				$front = ($front_img)? "<img src='$front_img' border='0'>":"◀";
				$next = ($next_img)? "<img src='$next_img' border='0'>":"▶";
			}
			if($addElement) $addElement = "&".$addElement;

			$return_String = "<a href='$pagename?page=$prevpage"."$addElement'> ";
			$return_String .= $front;
			$return_String .= "</a>&nbsp;";
			for($i = $startpage ; $i <= $endpage ; $i++){
				$return_String .= ($i != $page)? "<a href='$pagename?page=$i"."$addElement'>":"";
				$return_String .= ($i == $page)? " &nbsp;&nbsp;<b>$i</b>&nbsp;&nbsp;" : " &nbsp;&nbsp;" . $i . "&nbsp;&nbsp; ";
				$return_String .= ($i != $page)? "</a>":"";
				$return_String .= ($i != $endpage) ? "  " : "";
			}

			if ($list_page_script) {
				$return_String .= "&nbsp;<a href=\"javascript:$list_page_script('page=$nextpage.$addElement')\"> ";
			} else {
				$return_String .= "&nbsp;<a href='$pagename?page=$nextpage"."$addElement'> ";
			}

			$return_String .= $next;
			$return_String .= "</a>";

		}else {
			$return_String = ($searchVar)?  "<font color='red'><b>$searchVar</b></font> 에 대한 검색 결과가 없습니다.":"등록된 데이터가 없습니다.";
		}

		if ($list_class) {
			$return_String = "<table width='380'><tr><td class='" . $list_class . "' align='center'>" . $return_String . "</td></tr></table>";
		}
		return $return_String;
	}

	function SetSearchForm()
	{
		$args = func_get_args ();
		$paraArr = array("pagename","searchPart","search_img","search_img_tag","addFormStr","searchWord","searchVar","sortBy","sortType","add_element");		// 변수명 지정
		$argsCnt = count($args);
		for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

		$searchWord = $searchWord++;
		$return_String = "<script>
					function reLocPage()
					{
						if(searchListForm.searchWord.value=='0')
						{
							location.href='$pagename?$add_element';
						}
							
					}
					function eoCheckSearchForm()
					{
						if(searchListForm.searchWord.value=='0')
						{
							alert('검색형태를 선택해주세요');
							return false;
						}
						
						searchListForm.action = '$pagename';
					}
					</script>";

		$return_String .= "<table border='0' cellpadding='0' height='0' cellspacing='0' class='border_clear' " . $list_class . ">";
		$return_String .= "<form name='searchListForm' method='get' onSubmit='return eoCheckSearchForm()'>";
		$return_String .= $addFormStr;
		$return_String .= "<tr><td width='*'>";
		$return_String .= "<select name='searchWord' class='select1' onchange='reLocPage()'>";
		$return_String .=  "<option value='0'>:: 전체 ::</option>";
		$keys = array_keys($searchPart);
		for($i = 1 ; $i < count($keys)+1 ; $i++){
			$selected = "";
			if($searchWord==$i) $selected = "selected";
			$return_String .=  "<option value='$i' $selected>".$keys[$i-1]."</option>";
		}
		if($searchVar) $addValue = "value='".$searchVar."'";
		$return_String .= "</select>";
		$return_String .=  "</td><td width='*'>";
		$return_String .= "&nbsp;<input type='text' name='searchVar' class='box_l' style='width:200px' $addValue>&nbsp;";
		$return_String .= "</td><td width='*'>";

		if ($search_img || $search_img_tag) {
			$return_String .= ($search_img)? "<input type='image' src='$search_img'>" : $search_img_tag;
		} else {
			$return_String .= "<input type='submit' value='검색'>";
		}

		if($sortBy && $sortType)
		{
			$return_String .= "<input type='hidden' name='sortBy' value='$sortBy'>";
			$return_String .= "<input type='hidden' name='sortType' value='$sortType'>";
		}

		// 추가 전송 정보가 있을시 Hidden 값으로 같이 넘겨줌
		$element_piece = explode("&",$add_element);
		$element_piece_cnt = count($element_piece);
		for($i = 0; $i < $element_piece_cnt; $i++)
		{
			$element_piece2 = "";
			$element_piece2 = explode("=",$element_piece[$i]);

			if($element_piece2[0])
				$return_String .= "<input type='hidden' name='".$element_piece2[0]."' value='".$element_piece2[1]."'>";
		}

		$return_String .=  "</td></tr></form></table>";


		return $return_String;
	}

	function GetExcelResult()
	{
		global $GL_dec_pw;

		$args = func_get_args ();
		$paraArr = array("excelHanArr","rstReturn");		// 변수명 지정
		$argsCnt = count($args);
		for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

		//echo $this -> qryStr;

		$str = "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=euc-kr\" />";
		$str .= "<table border='1' width='100%'>";

		if(is_array($excelHanArr))
		{
			$str .= "<tr>";
			$rstCnt = "";

			$keyArr = array_keys($excelHanArr);
			$rstCnt = count($keyArr);
			for($i = 0; $i < $rstCnt; $i++)
			{
				$str .= "<td bgcolor='CCCCCC'>".$keyArr[$i]."</td>";
			}

			$str .= "</tr>";
		}


		$rstCnt2 = "";
		$rstCnt2 = count($rstReturn);

		$valueArr = array_values($excelHanArr);

		// 연결연산자가 있는지 확인한다.
		foreach ($valueArr as $k => $v) {
			if (eregi('&', $v)) {
				$get_and_arr = explode('&', $v);
				$store_and_position[] = $k;
			}
		}

		for($i = 0; $i < $rstCnt2; $i++)
		{
			$str .= "<tr>";

			for($j =0 ;$j < $rstCnt ; $j++)
			{
				$dataDsp = "";
				$excelHan_val = $valueArr[$j];

				$get_column_data = $rstReturn[$i][$excelHan_val];

				if ($get_column_data) {
					$dataDsp = $get_column_data;
				} else {
					if (eregi('&', $excelHan_val)) {
						$get_and_arr = explode('&', $excelHan_val);
						foreach ($get_and_arr as $k2 => $v2) {
							$add_sep = (eregi("'",$v2)) ? str_replace("'","",$v2) : '';
							$dataDsp .= $add_sep . $rstReturn[$i][$v2];
						}
					} else {
						$dataDsp = "&nbsp;";
					}
				}

				if($keyArr[$j] == '주민번호' && strlen($dataDsp) > 15){
					$dataDsp = decrypt_md5($dataDsp,$GL_dec_pw);
				}

				$str .= "<td>".$dataDsp."</td>";
			}

			$str .= "</tr>";
		}
		$str .= "</table>";

		echo $str;
	}

	public function getAnalyze () {
		return parent::$this -> query_analyze;
	}

}
?>