<?php
/**
 * 게시판 관리 관련 Class .....
 * User: woox
 * Date: 2018-11-10
 */
class BBS extends Dbconn
{
	/**
	 * 게시글 입력
	 * @param $args
	 * @return bool
	 */
	public function insertBBS($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$bbs_id              = "";
		$bbs_target          = "";
		$bbs_target_vendor_A = "";
		$bbs_target_vendor_B = "";
		$bbs_target_vendor_C = "";
		$bbs_target_vendor_D = "";
		$bbs_target_vendor_E = "";
		$bbs_category        = "";
		$bbs_is_main         = "";
		$bbs_is_notice       = "";
		$bbs_title           = "";
		$bbs_contents        = "";
		$bbs_file_idx_1      = 0;
		$bbs_file_idx_2      = 0;
		$bbs_file_idx_3      = 0;
		$bbs_file_idx_4      = 0;
		$bbs_file_idx_5      = 0;
		$bbs_ref             = 0;
		$ref_idx             = 0;
		$bbs_level           = 1;
		$bbs_step            = 1;
		extract($args);

		if(!empty($ref_idx)) {
			$_ref_view = $this->getBBSView($bbs_id, $ref_idx);
		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작
		if(!empty($ref_idx)){
			if(!$_ref_view) return false;

			$bbs_category = $_ref_view["bbs_category"];
			$bbs_level = $_ref_view["bbs_level"] + 1;
			$bbs_ref = $_ref_view["bbs_ref"];
			//$ref_step = $_ref_view["bbs_step"] + 1;

			$qry = "Select top 1 bbs_step From DY_BBS Where bbs_ref = N'$bbs_ref' And bbs_level = N'".$_ref_view["bbs_level"]."' And bbs_step > N'".$_ref_view["bbs_step"]."' And bbs_is_del = 'N' Order by bbs_step ASC";
			$bbs_step = parent::execSqlOneCol($qry);

			if($bbs_step == 0){
				$qry = "Select Max(bbs_step) From DY_BBS Where bbs_ref = N'$bbs_ref' And bbs_is_del = 'N' ";
				$ref_step = parent::execSqlOneCol($qry);
				$bbs_step = $ref_step + 1;
			}

			$qry = "Update DY_BBS Set bbs_step = bbs_step + 1 Where bbs_ref = N'$bbs_ref' And bbs_step >= N'$bbs_step' And bbs_is_del = 'N' ";
			$tmp = parent::execSqlUpdate($qry);
		}

		//입력
		$qry = "
			Insert Into DY_BBS
			(
			  bbs_id, bbs_group, bbs_target, bbs_target_vendor_A, bbs_target_vendor_B, bbs_target_vendor_C, bbs_target_vendor_D, bbs_target_vendor_E
			  , bbs_category, bbs_is_main, bbs_is_notice, bbs_title, bbs_contents, bbs_ref, bbs_level, bbs_step
			  , member_idx, bbs_file_idx_1, bbs_file_idx_2, bbs_file_idx_3, bbs_file_idx_4, bbs_file_idx_5
			  , bbs_regip, last_member_idx
		    ) 
		    VALUES
			(
			 N'$bbs_id'
			 , N''
			 , N'$bbs_target'
			 , N'$bbs_target_vendor_A'
			 , N'$bbs_target_vendor_B'
			 , N'$bbs_target_vendor_C'
			 , N'$bbs_target_vendor_D'
			 , N'$bbs_target_vendor_E'
			 , N'$bbs_category'
			 , N'$bbs_is_main'
			 , N'$bbs_is_notice'
			 , N'$bbs_title'
			 , N'$bbs_contents'
			 , N'$bbs_ref'
			 , N'$bbs_level'
			 , N'$bbs_step'
			 , N'$last_member_idx'
			 , N'$bbs_file_idx_1'
			 , N'$bbs_file_idx_2'
			 , N'$bbs_file_idx_3'
			 , N'$bbs_file_idx_4'
			 , N'$bbs_file_idx_5'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";

		$inserted_idx = parent::execSqlInsert($qry);

		if(empty($ref_idx)) {
			//ref Update
			$qry = "Update DY_BBS Set bbs_ref = N'$inserted_idx' Where bbs_idx = N'$inserted_idx'";
			$tmp = parent::execSqlUpdate($qry);
		}

		//첨부파일 Update
		if(!empty($bbs_file_idx_1)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$inserted_idx' Where file_idx = N'$bbs_file_idx_1'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_2)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$inserted_idx' Where file_idx = N'$bbs_file_idx_2'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_3)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$inserted_idx' Where file_idx = N'$bbs_file_idx_3'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_4)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$inserted_idx' Where file_idx = N'$bbs_file_idx_4'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_5)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$inserted_idx' Where file_idx = N'$bbs_file_idx_5'";
			$tmp = parent::execSqlUpdate($qry);
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 게시글 내용 반환 -
	 * @param $bbs_id
	 * @param $bbs_idx
	 * @return array|false|null
	 */
	public function getBBSView($bbs_id, $bbs_idx)
	{
		$bbs_category_code = "";
		if($bbs_id == "notice"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}elseif($bbs_id == "biz"){
			$bbs_category_code = "BBS_BIZ_CATEGORY";
		}elseif($bbs_id == "design"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}elseif($bbs_id == "work"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}elseif($bbs_id == "faq"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}

		$qry = "
			Select 
		       B.*
				, C.code_name as target_name
				, CC.code_name as category_name
				, F1.save_filename as save_filename_1, F1.user_filename as user_filename_1 
				, F2.save_filename as save_filename_2, F2.user_filename as user_filename_2 
				, F3.save_filename as save_filename_3, F3.user_filename as user_filename_3 
				, F4.save_filename as save_filename_4, F4.user_filename as user_filename_4 
				, F5.save_filename as save_filename_5, F5.user_filename as user_filename_5
				, Case When name is not null Then name
						When S.supplier_name is not null Then supplier_name
						When V.vendor_name is not null Then vendor_name
					End as name
			From  DY_BBS B
			Left Outer Join DY_CODE C On C.parent_code = N'BBS_TARGET' And C.code = B.bbs_target
			Left Outer Join DY_CODE CC On CC.parent_code = N'$bbs_category_code' And CC.code = B.bbs_category
			Left Outer Join DY_FILES F1 On F1.file_idx = B.bbs_file_idx_1 And F1.is_use = N'Y' And F1.is_del = N'N'
			Left Outer Join DY_FILES F2 On F2.file_idx = B.bbs_file_idx_2 And F2.is_use = N'Y' And F2.is_del = N'N'
			Left Outer Join DY_FILES F3 On F3.file_idx = B.bbs_file_idx_3 And F3.is_use = N'Y' And F3.is_del = N'N'
			Left Outer Join DY_FILES F4 On F4.file_idx = B.bbs_file_idx_4 And F4.is_use = N'Y' And F4.is_del = N'N'
			Left Outer Join DY_FILES F5 On F5.file_idx = B.bbs_file_idx_5 And F5.is_use = N'Y' And F5.is_del = N'N'
			Left Outer Join DY_MEMBER_USER U On U.member_idx = B.member_idx
			Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = B.member_idx 
			Left Outer Join DY_MEMBER_VENDOR V On V.member_idx = B.member_idx
			Where B.bbs_is_del = N'N' And B.bbs_id = N'$bbs_id' And B.bbs_idx = N'$bbs_idx'
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 게시글 수정
	 * @param $args
	 * @return bool
	 */
	public function updateBBS($args){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;
		$bbs_idx             = "";
		$bbs_id              = "";
		$bbs_target          = "";
		$bbs_target_vendor_A = "";
		$bbs_target_vendor_B = "";
		$bbs_target_vendor_C = "";
		$bbs_target_vendor_D = "";
		$bbs_target_vendor_E = "";
		$bbs_category        = "";
		$bbs_is_main         = "";
		$bbs_is_notice         = "";
		$bbs_title           = "";
		$bbs_contents        = "";
		$bbs_file_idx_1      = 0;
		$bbs_file_idx_2      = 0;
		$bbs_file_idx_3      = 0;
		$bbs_file_idx_4      = 0;
		$bbs_file_idx_5      = 0;
		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//입력
		$qry = "
			Update DY_BBS
			Set
			    bbs_target = N'$bbs_target', 
			    bbs_target_vendor_A = N'$bbs_target_vendor_A',
			    bbs_target_vendor_B = N'$bbs_target_vendor_B',
			    bbs_target_vendor_C = N'$bbs_target_vendor_C',
			    bbs_target_vendor_D = N'$bbs_target_vendor_D',
			    bbs_target_vendor_E = N'$bbs_target_vendor_E',
			    bbs_category = N'$bbs_category',
			    bbs_is_main = N'$bbs_is_main', 
			    bbs_is_notice = N'$bbs_is_notice', 
			    bbs_title = N'$bbs_title',
			    bbs_contents = N'$bbs_contents',
			    bbs_file_idx_1 = N'$bbs_file_idx_1',
			    bbs_file_idx_2 = N'$bbs_file_idx_2',
			    bbs_file_idx_3 = N'$bbs_file_idx_3',
			    bbs_file_idx_4 = N'$bbs_file_idx_4',
			    bbs_file_idx_5 = N'$bbs_file_idx_5',
			    bbs_moddate = getdate(), 
			    bbs_modip = N'$modip', 
			    last_member_idx = N'$last_member_idx'
		    Where bbs_idx = N'$bbs_idx'
		";

		$tmp = parent::execSqlUpdate($qry);

		//첨부파일 Update
		if(!empty($bbs_file_idx_1)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$bbs_idx' Where file_idx = N'$bbs_file_idx_1'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_2)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$bbs_idx' Where file_idx = N'$bbs_file_idx_2'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_3)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$bbs_idx' Where file_idx = N'$bbs_file_idx_3'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_4)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$bbs_idx' Where file_idx = N'$bbs_file_idx_4'";
			$tmp = parent::execSqlUpdate($qry);
		}
		if(!empty($bbs_file_idx_5)) {
			$qry = "Update DY_FILES Set is_use = 'Y', ref_table_idx = N'$bbs_idx' Where file_idx = N'$bbs_file_idx_5'";
			$tmp = parent::execSqlUpdate($qry);
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 게시글 조회수 +1
	 * @param $bbs_idx
	 * @return bool|resource
	 */
	public function updateRead($bbs_idx){
		$qry = "Update DY_BBS Set bbs_read = bbs_read + 1 Where bbs_idx = N'$bbs_idx'";
		parent::db_connect();
		$tmp = parent::execSqlUpdate($qry);
		parent::db_close();

		return $tmp;
	}

	/**
	 * 게시글 삭제
	 * @param $bbs_idx
	 * @return bool|resource
	 */
	public function deleteBBS($bbs_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
				Update DY_BBS 
				Set bbs_is_del = N'Y'
					, bbs_moddate = getdate()
					, bbs_modip = N'$modip'
					, last_member_idx = N'$last_member_idx'
				Where bbs_idx = N'$bbs_idx'
		";
		parent::db_connect();
		$tmp = parent::execSqlUpdate($qry);
		parent::db_close();

		return $tmp;
	}

	public function getTopNoticeBBS($bbs_id){

		$bbs_category_code = "";
		if($bbs_id == "notice"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}elseif($bbs_id == "biz"){
			$bbs_category_code = "BBS_BIZ_CATEGORY";
		}elseif($bbs_id == "design"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}elseif($bbs_id == "work"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}elseif($bbs_id == "faq"){
			$bbs_category_code = "BBS_NOTICE_CATEGORY";
		}


		$qry = "
			Select 
			       A.* 
					, C.code_name as target_name
					, CC.code_name as category_name
					, Case When name is not null Then name
							When S.supplier_name is not null Then supplier_name
							When V.vendor_name is not null Then vendor_name
						End as bbs_name
			From DY_BBS A 
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx 
			Left Outer Join DY_CODE C On C.parent_code = N'BBS_TARGET' And C.code = A.bbs_target
			Left Outer Join DY_CODE CC On CC.parent_code = N'$bbs_category_code' And CC.code = A.bbs_category
			Left Outer Join DY_MEMBER_USER U On U.member_idx = A.member_idx
			Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = A.member_idx 
			Left Outer Join DY_MEMBER_VENDOR V On V.member_idx = A.member_idx
			Where bbs_is_del = 'N'
				  And bbs_id = N'$bbs_id'
				  And bbs_is_notice = N'Y'
			Order by bbs_idx DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 댓글 리스트
	 * @param $bbs_idx
	 * @return array
	 */
	public function getCommentList($bbs_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Select 
				CM.comment_idx, CM.comment 
				, Case When name is not null Then name
						When S.supplier_name is not null Then supplier_name
						When V.vendor_name is not null Then vendor_name
					End as name
				, convert(varchar(20), CM.comment_regdate, 120) as regdate
				, Case When convert(varchar(20), CM.member_idx) = N'$last_member_idx' Then 1 Else 0 End as is_mine
			From DY_BBS_COMMENT CM 
				Left Outer Join DY_MEMBER M On CM.member_idx = M.idx
				Left Outer Join DY_MEMBER_USER U On U.member_idx = CM.member_idx
				Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = CM.member_idx 
				Left Outer Join DY_MEMBER_VENDOR V On V.member_idx = CM.member_idx
			Where bbs_idx = N'$bbs_idx' And comment_is_del = N'N'
			Order by comment_idx ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 댓글 입력
	 * @param $bbs_idx
	 * @param $comment
	 * @return int
	 */
	public function insertComment($bbs_idx, $comment)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_BBS_COMMENT
			(bbs_idx, member_idx, comment, comment_regip, last_member_idx)
			VALUES
			(
			 N'$bbs_idx'
			 , N'$last_member_idx'
			 , N'$comment'
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
	 * 댓글 삭제
	 * @param $comment_idx
	 * @return bool|resource
	 */
	public function deleteComment($comment_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_BBS_COMMENT
			Set comment_is_del = N'Y', comment_moddate = getdate(), comment_modip = N'$modip', last_member_idx = N'$last_member_idx'
			Where comment_idx = N'$comment_idx'
		";

		parent::db_connect();
		$tmp = parent::execSqlUpdate($qry);
		parent::db_close();

		return $tmp;
	}

	public function getMainNoticeList(){
		$qry = "
			Select top 5 bbs_idx, bbs_title, bbs_regdate
			From DY_BBS
			Where bbs_id = N'notice' and bbs_target = N'ALL' And bbs_is_del = N'N' And bbs_is_main = 'Y'
			Order by bbs_regdate DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	public function getMainNoticeViewPrevNext($bbs_idx)
	{
		$returnValue = array();
		$returnValue["prev"] = "";
		$returnValue["next"] = "";

		//Prev
		$qry = "
			Select top 1 bbs_idx, bbs_title, bbs_regdate
			From DY_BBS
			Where bbs_id = N'notice' And bbs_is_del = N'N' And bbs_is_main = 'Y'
			And bbs_idx < N'$bbs_idx'
			Order by bbs_regdate DESC
		";

		parent::db_connect();
		$returnValue["prev"] = parent::execSqlOneRow($qry);
		parent::db_close();

		//Next
		$qry = "
			Select top 1 bbs_idx, bbs_title, bbs_regdate
			From DY_BBS
			Where bbs_id = N'notice' And bbs_is_del = N'N' And bbs_is_main = 'Y'
			And bbs_idx > N'$bbs_idx'
			Order by bbs_regdate DESC
		";

		parent::db_connect();
		$returnValue["next"] = parent::execSqlOneRow($qry);
		parent::db_close();

		return $returnValue;
	}
}
?>