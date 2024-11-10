<?php
/**
 * 배너관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Banner extends DBConn
{
	/**
	 * 배너 전체 목록 반환
	 * @return array
	 */
	public function getBannerList($banner_type)
	{
		$qry = "
			Select B.*, M.member_id
			From 
			     DY_BANNER B
				 Left Outer Join DY_MEMBER M On B.banner_regidx = M.idx
			Where banner_is_del = N'N' And B.banner_type = N'$banner_type'
			Order by banner_sort ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 서비스 중인 배너 목록 반환
	 * @param $banner_type
	 * @return array
	 */
	public function getUseBanner($banner_type)
	{
		$nowDT = date('Y-m-d');
		$qry = "
			Select * 
			From DY_BANNER
			Where banner_is_del = N'N' And banner_is_use = N'Y' And banner_type = N'$banner_type'
			And (
			  banner_use_period = N'N' OR 
			  (banner_use_period = N'Y' And banner_period_start <= N'$nowDT' And banner_period_end >= N'$nowDT' )
			)
			Order by banner_sort ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 최대 Sort 번호 가져오기
	 * @return int|mixed
	 */
	public function getBannerMaxSort($banner_type)
	{
		$qry = "
			Select MAX(banner_sort) From DY_BANNER 
			Where banner_is_del = N'N' And banner_type = N'$banner_type'
		";
		parent::db_connect();
		$_view = parent::execSqlOneCol($qry);
		parent::db_close();

		if(!$_view) {
			$_view = 1;
		}else{
			$_view = $_view + 1;
		}

		return $_view;
	}

	/**
	 * 배너 상세 정보 가져오기
	 * @param $banner_idx
	 * @return array|false|null
	 */
	public function getBannerDetail($banner_idx)
	{
		$qry = "
			Select * From DY_BANNER Where banner_idx = N'$banner_idx'
		";
		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 배너 입력
	 * @param $args
	 * @return int
	 */
	public function insertBanner($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$banner_type         = "MAIN";
		$banner_image        = "";
		$banner_click_url    = "";
		$banner_click_target = "";
		$banner_use_period   = "";
		$banner_period_start = "";
		$banner_period_end   = "";
		$banner_sort         = "";
		$banner_is_use         = "";

		extract($args);

		$qry = "Update DY_BANNER Set banner_sort = banner_sort + 1 Where banner_sort >= N'$banner_sort'";
		parent::db_connect();
		$tmp = parent::execSqlUpdate($qry);
		parent::db_close();

		$qry = "
			Insert Into DY_BANNER
			(
			 banner_type, banner_image, 
			 banner_use_period, banner_period_start, banner_period_end, 
			 banner_click_url, banner_click_target, 
			 banner_sort, banner_is_use, 
			 banner_regip, banner_regidx
		    ) 
			VALUES
			(
			 N'$banner_type'
			 , N'$banner_image'
			 , N'$banner_use_period'
			 , N'$banner_period_start'
			 , N'$banner_period_end'
			 , N'$banner_click_url'
			 , N'$banner_click_target'
			 , N'$banner_sort'
			 , N'$banner_is_use'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";

		parent::db_connect();
		$inserted_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $inserted_idx;
	}

	/**
	 * 배너 수정
	 * @param $args
	 * @return bool|resource
	 */
	public function updateBanner($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$banner_type         = "MAIN";
		$banner_image        = "";
		$banner_click_url    = "";
		$banner_click_target = "";
		$banner_use_period   = "";
		$banner_period_start = "";
		$banner_period_end   = "";
		$banner_is_use       = "";
		$banner_idx          = "";

		extract($args);

		if($banner_image)
		{
			$addQry = ", banner_image = N'$banner_image'";
		}

		$qry = "
			Update DY_BANNER
			Set
			    banner_click_url = N'$banner_click_url'
				, banner_click_target = N'$banner_click_target'
				, banner_use_period = N'$banner_use_period'
				, banner_period_start = N'$banner_period_start'
				, banner_period_end = N'$banner_period_end'
				, banner_is_use = N'$banner_is_use'
				, banner_moddate = NOW()
				, banner_modip = N'$modip'
				, banner_modidx = N'$last_member_idx'
				$addQry
			Where banner_idx = N'$banner_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 배너 삭제
	 * @param $banner_idx
	 * @return bool|resource
	 */
	public function deleteBanner($banner_idx){

		$tmp = false;

		//현재 sort 구하기
		$qry = "Select banner_sort From DY_BANNER Where banner_idx = N'$banner_idx' And banner_is_del = N'N'";
		parent::db_connect();
		$banner_sort = parent::execSqlOneCol($qry);
		parent::db_close();

		if($banner_sort) {
			$qry = "
			Update DY_BANNER
			Set banner_sort = banner_sort - 1
			Where banner_sort > N'$banner_sort'
		";
			parent::db_connect();
			$tmp = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
			Update DY_BANNER
			Set banner_is_del = N'Y'
			Where banner_idx = N'$banner_idx'
		";
			parent::db_connect();
			$tmp = parent::execSqlUpdate($qry);
			parent::db_close();

		}
		return $tmp;
	}

	/*
	 * 배너 순서 변경
	 * $args
	 * out : Array (result[boolean] : 가능 여부, msg[string] : 불가능 시 메시지)
	 */
	public function moveBanner($args)
	{
		$banner_idx = "";
		$dir = "";
		$banner_sort = "";
		$banner_type = "";
		extract($args);

		$result = false;
		$msg = "";

		$bannerInfo = $this->getBannerDetail($banner_idx);

		$sort = $bannerInfo["banner_sort"];

		if($bannerInfo) {

			if ($dir == "up") {
				$qry = "
					Select banner_sort, banner_type From DY_BANNER Where banner_is_del = 'N' And banner_idx = '" . $banner_idx . "'
				";
				parent::db_connect();
				$row = parent::execSqlOneRow($qry);
				parent::db_close();

				$banner_sort = $row["banner_sort"];
				$banner_type = $row["banner_type"];

				if ($row) {
					if ($banner_sort > 1) {

						$qry = "
							Update DY_BANNER
								Set banner_sort = banner_sort + 1 
								Where banner_sort = '".($sort-1)."' And banner_is_del = 'N' And banner_type = N'$banner_type'
						";
						parent::db_connect();
						$rst = parent::execSqlUpdate($qry);
						parent::db_close();

						$qry = "
							Update DY_BANNER
							Set banner_sort = banner_sort - 1
							Where banner_idx = '".$banner_idx."'
						";
						parent::db_connect();
						$rst = parent::execSqlUpdate($qry);
						parent::db_close();

						$result = true;
					} else {
						$result = false;
						$msg = "이미 최상위입니다.";
					}
				} else {
					$result = false;
					$msg = "존재하지 않는 배너입니다.";
				}

			} elseif ($dir == "dn") {
				$qry = "
				  Select count(*) From DY_BANNER 
					Where banner_is_del = 'N' 
							And banner_sort > '" . $sort . "'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				$qry = "
					Select banner_sort, banner_type From DY_BANNER Where banner_is_del = 'N' And banner_idx = '" . $banner_idx . "'
				";
				parent::db_connect();
				$row = parent::execSqlOneRow($qry);
				parent::db_close();

				$banner_sort = $row["banner_sort"];
				$banner_type = $row["banner_type"];

				if ($rst == 0) {
					$result = false;
					$msg = "이미 최하위입니다.";
				} else {

					$qry = "
						Update DY_BANNER
							Set banner_sort = banner_sort - 1 
							Where banner_sort = '".($sort+1)."' And banner_is_del = 'N' And banner_type = N'$banner_type'
					";
					parent::db_connect();
					$rst = parent::execSqlUpdate($qry);
					parent::db_close();

					$qry = "
						Update DY_BANNER
						Set banner_sort = banner_sort + 1
						Where banner_idx = '".$banner_idx."'
					";
					parent::db_connect();
					$rst = parent::execSqlUpdate($qry);
					parent::db_close();

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
}
?>

