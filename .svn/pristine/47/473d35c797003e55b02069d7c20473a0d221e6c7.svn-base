<?php
/**
 * 사이트 메뉴 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class SiteMenu extends DBConn
{
	/*
	 * 하위 메뉴 목록 반환
	 * $parent_idx : 상위 메뉴 IDX
	 * out : Array
	 */
	public function getMenuList($parent_idx)
	{
		$qry = "
			Select * From DY_MENU 
			Where 
			is_del = 'N' 
			And is_dev = 'N' 
			And is_hidden = 'N'
			And parent_idx = '".$parent_idx."'
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 전체 메뉴 목록 반환
	 * 재귀 쿼리를 이용한 목록
	 * is_del = 'N', is_hidden = 'N', is_use = 'Y'
	 * out : Array
	 */
	public function getAllMenuList()
	{
		$qry = "
			WITH tree_query
			AS (
				SELECT idx
					,parent_idx
					,name
					,name_short
					,sort
					,url
					,target
					,popup_size
					,css_class
					,is_hidden
					,is_dev
					,is_use
					,1 as depth
					,convert(VARCHAR(255), right('0' + CAST(sort as varchar), 2)) sort_idx
					,convert(VARCHAR(255), name) depth_fullname
				FROM DY_MENU
				WHERE parent_idx = 0 And is_del = 'N' And is_hidden = 'N' And is_dev = 'N' And is_use = 'Y'
				
				UNION ALL
				
				SELECT B.idx
					,B.parent_idx
					,B.name
					,B.name_short
					,B.sort
					,B.url
					,B.target
					,B.popup_size
					,B.css_class
					,B.is_hidden
					,B.is_dev
					,B.is_use
					,convert(int, convert(int, C.depth) + 1) as depth
					,convert(VARCHAR(255), convert(NVARCHAR, C.sort_idx) + ' > ' + convert(VARCHAR(255), right('0' + CAST(B.sort as varchar), 2))) sort_idx
					,convert(VARCHAR(255), convert(NVARCHAR, C.depth_fullname) + ' > ' + convert(VARCHAR(255), B.name)) depth_fullname
				FROM DY_MENU B
					,tree_query C
				WHERE B.parent_idx = C.idx And B.is_del = N'N' And B.is_hidden = 'N' And B.is_dev = N'N' And B.is_use = N'Y'
				)
			SELECT idx
				,parent_idx
				,name
				,name_short
				,sort
				,url
				,target
				,popup_size
				,css_class
				,is_hidden
				,is_dev
				,is_use
				,depth
				,depth_fullname
				,sort_idx
			FROM tree_query

			ORDER BY sort_idx

		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 전체 메뉴 목록 반환
	 * 재귀 쿼리를 이용한 목록
	 * 사용자 IDX 를 기반으로 권한을 체크
	 * global $GL_Member 로그인 정보를 이용하여 체크
	 *      사용자 일 경우 사용자 IDX
	 *      벤더사 일 경우 권한그룹 IDX : 120000 (고정)
     *      공급처 일 경우 권한그룹 IDX : 140000 (고정)
	 * is_del = 'N', is_hidden = 'N', is_use = 'Y'
	 * out : Array
	 */
	public function getAllMenuListByPermission()
	{
		global $GL_Member;

		$permission_member_idx = 0;
		if($GL_Member["member_type"] == "VENDOR"){
			$permission_member_idx = 120000;
		}elseif($GL_Member["member_type"] == "SUPPLIER") {
			$permission_member_idx = 140000;
		}else{
			$permission_member_idx = $GL_Member["member_idx"];
		}

		$qry = "
			WITH recursive tree_query AS
            (
                SELECT
                    idx
                    , parent_idx
                    , name
                    , name_short
                    , sort
                    , url
                    , target
                    , popup_size
                    , css_class
                    , is_hidden
                    , is_dev
                    , is_use
                    , 1 as depth
                    , concat('0', CAST(sort as char)) as sort_idx
                    , name as depth_fullname
                FROM DY_MENU
                WHERE
                    parent_idx = 0
                    And is_del = 'N'
                    And is_hidden = 'N'
                    And is_dev = 'N'
                    And is_use = 'Y'
                UNION ALL
                SELECT
                    B.idx
                    , B.parent_idx
                    , B.name
                    , B.name_short
                    , B.sort
                    , B.url
                    , B.target
                    , B.popup_size
                    , B.css_class
                    , B.is_hidden
                    , B.is_dev
                    , B.is_use
                    , C.depth + 1 as depth
                    , concat(C.sort_idx, ' > ', right(concat('0', CAST(B.sort as char)), 2)) as sort_idx
                    , concat(C.depth_fullname, ' > ', B.name) as depth_fullname
                FROM DY_MENU B
                    JOIN tree_query AS C ON B.parent_idx = C.idx
                WHERE
                    B.is_del = N'N'
                    And B.is_hidden = 'N'
                    And B.is_dev = N'N'
                    And B.is_use = N'Y'
            )
            
            SELECT
                idx
                , parent_idx
                , name
                , name_short
                , sort
                , url
                , target
                , popup_size
                , css_class
                , is_hidden
                , is_dev
                , is_use
                , depth
                , depth_fullname
                , sort_idx
            FROM tree_query
            Where idx in (Select menu_idx From DY_PERMISSION Where member_idx = N'$permission_member_idx')
            ORDER BY sort_idx;
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 메뉴 정보 반환
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
	 * 전체 메뉴 목록 수 반환 - Depth 3 까지만 - 권한목록을 위한 함수
	 * 재귀 쿼리를 이용한 목록
	 * is_del = 'N', is_hidden = 'N', is_use = 'Y'
	 * out : Array
	 */
	public function getAllMenuListCount()
	{
		$qry = "
			WITH tree_query
			AS (
				SELECT idx
					,parent_idx
					,name
					,name_short
					,sort
					,url
					,target
					,popup_size
					,css_class
					,is_hidden
					,is_dev
					,is_use
					,1 as depth
					,convert(VARCHAR(255), right('0' + CAST(sort as varchar), 2)) sort_idx
					,convert(VARCHAR(255), name) depth_fullname
				FROM DY_MENU
				WHERE parent_idx = 0 And is_del = 'N' And is_hidden = 'N' And is_dev = 'N' And is_use = 'Y'
				
				UNION ALL
				
				SELECT B.idx
					,B.parent_idx
					,B.name
					,B.name_short
					,B.sort
					,B.url
					,B.target
					,B.popup_size
					,B.css_class
					,B.is_hidden
					,B.is_dev
					,B.is_use
					,convert(int, convert(int, C.depth) + 1) as depth
					,convert(VARCHAR(255), convert(NVARCHAR, C.sort_idx) + ' > ' + convert(VARCHAR(255), right('0' + CAST(B.sort as varchar), 2))) sort_idx
					,convert(VARCHAR(255), convert(NVARCHAR, C.depth_fullname) + ' > ' + convert(VARCHAR(255), B.name)) depth_fullname
				FROM DY_MENU B
					,tree_query C
				WHERE B.parent_idx = C.idx And B.is_del = N'N' And B.is_hidden = 'N' And B.is_dev = N'N' And B.is_use = N'Y'
				)
			SELECT count(*)
			FROM tree_query
			Where depth < 4

		";

		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 하위 메뉴 목록 (권한 목록 생성 용)
	 * $idx : 상위 메뉴 IDX
	 * out : Array
	 */
	public function getMenuListForPermission($idx, $member_idx)
	{
		$qry = "
				Select M.*
				, (Select count(*) From DY_MENU MM Where MM.parent_idx = M.idx And is_del = 'N' And is_use = N'Y' And is_dev = N'N' And is_hidden = N'N') as children_count 
				From DY_MENU M
					Left Outer Join DY_PERMISSION P On M.idx = P.menu_idx And P.member_idx = '".$member_idx."' 
				Where is_del = 'N' And is_use = N'Y' And is_dev = N'N' And is_hidden = N'N'
				And M.parent_idx = '" . $idx . "'
				order by M.sort ASC
				
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}

	/*
	 * 모든 메뉴 리스트 반환 (모든 Depth 를 포함. / 메뉴 순서대로 출력)
	 * 사용자의 권한 여부도 함께 반환
	 * $member_idx : 권한 여부를 위한 DY_MEMBER 테이블 IDX
	 * out : Array
	 */
	public function getPermissionMenu($member_idx)
	{
		$qry = "
		
			Select 
			M.idx as idx_L, M.name as name_L, P.permission_idx as permission_idx_L
			, MM.idx as idx_M, MM.name as name_M, PP.permission_idx as permission_idx_M
			, MMM.idx as idx_S, MMM.name as name_S, PPP.permission_idx as permission_idx_S
			From DY_MENU M 
				Left Outer Join DY_PERMISSION P On M.idx = P.menu_idx And P.member_idx = '".$member_idx."' 
				Left Outer Join DY_MENU MM On M.idx = MM.parent_idx
				Left Outer Join DY_PERMISSION PP On MM.idx = PP.menu_idx And PP.member_idx = '".$member_idx."'
				Left Outer Join DY_MENU MMM On MM.idx = MMM.parent_idx
				Left Outer Join DY_PERMISSION PPP On MMM.idx = PPP.menu_idx And PPP.member_idx = '".$member_idx."'
			Where M.parent_idx = 0 
			And M.is_del = N'N' And M.is_use = N'Y' And M.is_hidden = N'N' And M.is_dev = N'N'
			And MM.is_del = N'N' And MM.is_use = N'Y' And MM.is_hidden = N'N' And MM.is_dev = N'N'
			And 
			(
				(MMM.is_del = N'N' And MMM.is_use = N'Y' And MMM.is_hidden = N'N' And MMM.is_dev = N'N')
				Or
				(IFNULL(MMM.idx, 0) = 0)
			)
			Order by M.sort, MM.sort, MMM.sort
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}




	/*
	 * 모든 메뉴 리스트 반환 (Depth 미포함)
	 * 사용자의 권한 여부도 함께 반환
	 * $member_idx : 권한 여부를 위한 DY_MEMBER 테이블 IDX
	 * out : Array
	 */
	public function getAllMenuListWithPermission($member_idx)
	{
		$qry = "
			Select M.*, IFNULL(P.permission_idx, 0) as is_permission From DY_MENU M
				Left Outer Join DY_PERMISSION P On M.idx = P.menu_idx And P.member_idx = N'".$member_idx."'
				Where M.is_del = N'N' And M.is_use = N'Y' And M.is_hidden = N'N' And M.is_dev = N'N'
				Order by M.idx ASC
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();
		return $rst;
	}


	public function saveFav($member_idx, $menu_idx)
	{
		$returnValue = false;

		$qry = "
			Select count(*) From DY_MEMBER_FAV
			Where member_idx = N'$member_idx'
		";

		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		if($cnt > 4){
			$returnValue = false;
			return $returnValue;
		}else{

			$qry = "
				Select count(*) From DY_MEMBER_FAV
				Where member_idx = N'$member_idx' And menu_idx = N'$menu_idx'
			";

			parent::db_connect();
			$dup = parent::execSqlOneCol($qry);
			parent::db_close();

			if($dup) {
				$returnValue = false;
				return $returnValue;
			}else{
				$qry = "
					Insert Into DY_MEMBER_FAV
					(member_idx, menu_idx) VALUES (N'$member_idx', N'$menu_idx')
				";
				parent::db_connect();
				$inserted_idx = parent::execSqlInsert($qry);
				parent::db_close();

				if ($inserted_idx) {
					$returnValue = true;
				} else {
					$returnValue = false;
				}
			}

			return $returnValue;
		}
	}

	public function removeFav($member_idx, $menu_idx){

		$qry = "
			Delete From DY_MEMBER_FAV Where member_idx = N'$member_idx' And menu_idx = N'$menu_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;

	}

	public function removeFavByFavIdx($fav_idx){

		$qry = "
			Delete From DY_MEMBER_FAV Where fav_idx = N'$fav_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;

	}

	public function isFavMenu($member_idx, $menu_idx)
	{
		$qry = "
			Select count(*) From DY_MEMBER_FAV
			Where member_idx = N'$member_idx' And menu_idx = N'$menu_idx'
		";
		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		return $cnt;
	}

	/**
	 * 해당 메뉴의 권한이 있는지 여부 반환
	 * true : 권한 있음
	 * false : 권한 없음
	 * @param $menu_idx
	 * @param $member_idx
	 * @return bool
	 */
	public function checkPermission($menu_idx, $member_idx)
	{
		global $GL_Member;

		$returnValue = false;

		if(isDYLogin()) {
			$qry = "
				Select count(*) From DY_PERMISSION 
				Where menu_idx = N'$menu_idx' 
				  And (
				      member_idx = N'$member_idx'
					  OR 
				      member_idx in (
						Select G.member_group_idx From DY_MEMBER_GROUP G
						Left Outer Join DY_MEMBER_GROUP_USER U On G.member_group_idx = U.member_group_idx
						Where member_idx = N'$member_idx'
								And G.member_group_is_del = N'N' 
								And G.member_group_is_use = N'Y'
								And U.member_group_user_is_del = N'N' 
								And U.member_group_user_is_use = N'Y'
					  ) 
				  )
			";
		}else{
			if ($GL_Member["member_type"] == "VENDOR") {
				$qry = "
					Select count(*) From DY_PERMISSION 
					Where menu_idx = N'$menu_idx' 
					  And member_idx = 120000
				";
			}elseif ($GL_Member["member_type"] == "SUPPLIER") {
				$qry = "
					Select count(*) From DY_PERMISSION 
					Where menu_idx = N'$menu_idx' 
					  And member_idx = 140000
				";
			}
		}

		parent::db_connect();
		$_cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		if($_cnt > 0){
			$returnValue = true;
		}else{
			$returnValue = false;
		}

		return $returnValue;
	}
}
?>