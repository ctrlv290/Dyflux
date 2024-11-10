<?php
/**
 * 메뉴 관련 Class (관리자 페이지 메뉴 관리)
 * User: woox
 * Date: 2018-11-10
 */
class Menu extends DBConn
{
	/*
	 * 하위 메뉴 목록
	 * $idx : 상위 메뉴 IDX
	 * out : Array
	 */
	public function getMenuList($idx)
	{
		$qry = "Select * From DY_MENU Where is_del = 'N'";
		$qry .= " And parent_idx = '" . $idx . "'";
		$qry .= " order by sort ASC ";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 메뉴 정보
	 * $idx : 메뉴 IDX
	 * out : Array (ONE ROW)
	 */
	public function getMenuInfo($idx)
	{
		$qry = "Select * From DY_MENU Where is_del = 'N'";
		$qry .= " And idx = '" . $idx . "'";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 메뉴 Insert
	 * $args
	 * out : Array
	 */
	public function insertMenu($args)
	{
		extract($args);
		$last_sort = $this->getLastSortNum($parent_idx) + 1;
		$qry = "Insert Into DY_MENU 
				(parent_idx, name, name_short, sort, url, target, popup_size, css_class, is_hidden, is_use) values
				(
					'" . $parent_idx . "',
					N'" . $name . "',
					N'" . $name_short . "',
					'" . $last_sort . "',
					N'" . $url . "',
					N'" . $target . "',
					N'" . $popup_size . "',
					N'" . $css_class . "',
					'" . $is_hidden . "',
					'" . $is_use . "'
				)  
		";
		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 메뉴 Update
	 * $args
	 * out : Array
	 */
	public function updateMenu($args)
	{
		extract($args);
		$qry = "
			Update DY_MENU Set
				name = N'" . $name . "',
				name_short = N'" . $name_short . "',
				url = N'" . $url . "',
				target = N'" . $target . "',
				popup_size = N'" . $popup_size . "',
				css_class = N'" . $css_class . "',
				is_hidden = N'" . $is_hidden . "',
				is_use = N'" . $is_use . "'
			Where idx = '".$idx."'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 메뉴 삭제
	 * $idx : 삭제 메뉴 IDX
	 * out : Array
	 */
	public function deleteMenu($idx)
	{
		$qry = "Update DY_MENU Set is_del = 'Y' Where idx = '". $idx ."' ";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 해당 메뉴에 속한 하위 메뉴 개수를 리턴한다
	 * $idx : 메뉴 IDX
	 * out : int
	 */
	public function getSubMenuCount($idx)
	{
		$qry = "Select count(*) as cnt From DY_MENU Where is_del = 'N' And parent_idx = '".$idx."'";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 같은 부모 메뉴들 중에 가장 높은 정렬값을 반환
	 * $parent_idx: 메뉴 IDX
	 * out : int
	 */
	public function getLastSortNum($parent_idx)
	{
		$qry = "Select Max(sort) as max_sort From DY_MENU Where is_del = 'N' And parent_idx = '".$parent_idx."'";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 메뉴 순서 변경 가능 여부 반환
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

		$menuInfo = $this->getMenuInfo($idx);

		$parent_idx = $menuInfo["parent_idx"];
		$sort = $menuInfo["sort"];

		if($menuInfo) {

			if ($dir == "up") {
				$qry = "
				Select sort From DY_MENU Where is_del = 'N' And idx = '" . $idx . "'
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
					$msg = "존재하지 않는 메뉴입니다.";
				}

			} elseif ($dir == "dn") {
				$qry = "
				  Select count(*) From DY_MENU 
					Where is_del = 'N' 
							And parent_idx = '" . $parent_idx . "'
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
			$msg = "존재하지 않는 메뉴입니다.";
		}

		$rst = array("result"=>$result, "msg"=>$msg);
		return $rst;
	}

	/*
	 * 메뉴 순서 변경
	 * $args
	 * out : boolean
	 */
	public function moveMenuSort($args)
	{
		$idx = "";
		$dir = "";
		extract($args);

		$menuInfo = $this->getMenuInfo($idx);

		$parent_idx = $menuInfo["parent_idx"];
		$sort = $menuInfo["sort"];

		if($dir == "up")
		{

			$qry = "
				Update DY_MENU
					Set sort = sort + 1 
					Where parent_idx = '".$parent_idx."' And sort = '".($sort-1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_MENU
				Set sort = sort - 1
				Where idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}elseif($dir == "dn"){

			$qry = "
				Update DY_MENU
					Set sort = sort - 1 
					Where parent_idx = '".$parent_idx."' And sort = '".($sort+1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_MENU
				Set sort = sort + 1
				Where idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}
	}
}
?>