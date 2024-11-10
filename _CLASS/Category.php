<?php
/**
 * 카테고리 관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Category extends Dbconn
{
	/*
	 * 하위 카테고리 목록
	 * $idx : 상위 카테고리 IDX
	 * out : Array
	 */
	public function getCategoryList($idx)
	{
		$qry = "Select * From DY_CATEGORY Where is_del = 'N'";
		$qry .= " And parent_category_idx = '" . $idx . "'";
		$qry .= " order by sort ASC ";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 카테고리 정보
	 * $idx : 카테고리 IDX
	 * out : Array (ONE ROW)
	 */
	public function getCategoryInfo($idx)
	{
		$qry = "Select * From DY_CATEGORY Where is_del = 'N'";
		$qry .= " And category_idx = '" . $idx . "'";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 카테고리 Insert
	 * $args
	 * out : Array
	 */
	public function insertCategory($args)
	{
		extract($args);
		$last_sort = $this->getLastSortNum($parent_category_idx) + 1;
		$qry = "Insert Into DY_CATEGORY 
				(parent_category_idx, name, sort, is_hidden, is_use, regip) values
				(
					'" . $parent_category_idx . "',
					N'" . $name . "',
					'" . $last_sort . "',
					'N',
					'" . $is_use . "',
					'" . $_SERVER["REMOTE_ADDR"] . "'
				)  
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 카테고리 Update
	 * $args
	 * out : Array
	 */
	public function updateCategory($args)
	{
		extract($args);
		$qry = "
			Update DY_CATEGORY Set
				name = N'" . $name . "',
				is_hidden = N'N',
				is_use = N'" . $is_use . "',
				moddate = getdate(),
				modip = N'".$_SERVER["REMOTE_ADDR"]."'
			Where category_idx = '".$idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 카테고리 삭제
	 * $idx : 삭제 카테고리 IDX
	 * out : Array
	 */
	public function deleteCategory($idx)
	{
		$qry = "Update DY_CATEGORY Set is_del = 'Y' Where category_idx = '". $idx ."' ";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 해당 카테고리에 속한 하위 카테고리 개수를 리턴한다
	 * $idx : 카테고리 IDX
	 * out : int
	 */
	public function getSubCategoryCount($idx)
	{
		$qry = "Select count(*) as cnt From DY_CATEGORY Where is_del = 'N' And parent_category_idx = '".$idx."'";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 같은 부모 카테고리들 중에 가장 높은 정렬값을 반환
	 * $parent_category_idx: 카테고리 IDX
	 * out : int
	 */
	public function getLastSortNum($parent_category_idx)
	{
		$qry = "Select Max(sort) as max_sort From DY_CATEGORY Where is_del = 'N' And parent_category_idx = '".$parent_category_idx."'";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 카테고리 순서 변경 가능 여부 반환
	 * $args
	 * out : Array (result[boolean] : 가능 여부, msg[string] : 불가능 시 메시지)
	 */
	public function checkCanSortChange($args)
	{
		$idx = "";
		$dir = "";
		extract($args);

		$result = false;
		$msg = "";

		$categoryInfo = $this->getCategoryInfo($idx);

		$parent_category_idx = $categoryInfo["parent_category_idx"];
		$sort = $categoryInfo["sort"];

		if($categoryInfo) {

			if ($dir == "up") {
				$qry = "
				Select sort From DY_CATEGORY Where is_del = 'N' And category_idx = '" . $idx . "'
			";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if ($rst) {
					if ($rst > 1) {
						$result = true;
					} else {
						$result = false;
						$msg = "이미 최상위입니다.";
					}
				} else {
					$result = false;
					$msg = "존재하지 않는 카테고리입니다.";
				}

			} elseif ($dir == "dn") {
				$qry = "
				  Select count(*) From DY_CATEGORY 
					Where is_del = 'N' 
							And parent_category_idx = '" . $parent_category_idx . "'
							And sort > '" . $sort . "'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if ($rst == 0) {
					$result = false;
					$msg = "이미 최하위입니다.";
				} else {
					$result = true;
				}
			}
		}else{
			$result = false;
			$msg = "존재하지 않는 카테고리입니다.";
		}

		$rst = array("result"=>$result, "msg"=>$msg);
		return $rst;
	}

	/*
	 * 카테고리 순서 변경
	 * $args
	 * out : boolean
	 */
	public function moveCategorySort($args)
	{
		$idx = "";
		$dir = "";
		extract($args);

		$categoryInfo = $this->getCategoryInfo($idx);

		$parent_category_idx = $categoryInfo["parent_category_idx"];
		$sort = $categoryInfo["sort"];

		if($dir == "up")
		{

			$qry = "
				Update DY_CATEGORY
					Set sort = sort + 1 
					Where parent_category_idx = '".$parent_category_idx."' And sort = '".($sort-1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_CATEGORY
				Set sort = sort - 1
				Where category_idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}elseif($dir == "dn"){

			$qry = "
				Update DY_CATEGORY
					Set sort = sort - 1 
					Where parent_category_idx = '".$parent_category_idx."' And sort = '".($sort+1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_CATEGORY
				Set sort = sort + 1
				Where category_idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}
	}

	/**
	 * 카테고리명으로 대분류 카테고리 찾기
	 * @param $category_name : 검색할 카테고리 명 텍스트
	 * @return array|false|null
	 */
	public function getCategoryLByName($category_name)
	{
		$qry = "
			Select top 1 * 
			From DY_CATEGORY
			Where parent_category_idx = 0
				And name like '%$category_name%'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}


	/**
	 * 카테고리명으로 중분류 카테고리 찾기
	 * @param $parent_category_idx : 부모 카테고리 IDX
	 * @param $category_name : 검색할 카테고리 명 텍스트
	 * @return array|false|null
	 */
	public function getCategoryMByName($parent_category_idx, $category_name)
	{
		$qry = "
			Select top 1 * 
			From DY_CATEGORY
			Where parent_category_idx = N'$parent_category_idx'
				And name like '%$category_name%'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}
}
?>