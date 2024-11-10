<?php
/**
 * SMS 매크로 관리
 * User: hyoung
 * Date:
 */
class SmsMecro extends Dbconn
{

    /*
     *  알림톡 템플릿 삭제
     */
    public function setSmsTemplateDelete($args = array())
    {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

//        $qry = "
//            delete from dbo.DY_SMS_AL_SETUP where idx='$idx'
//        ";

        $qry = "
            Update dbo.DY_SMS_AL_SETUP
            Set tp_is_del = N'Y'
			where idx='$idx'
        ";

        parent::db_connect();
        $rst = parent::execSqlUpdate($qry);
        parent::db_close();
        return $rst;

    }

    /*
     *  알림톡 템플릿 수정
     */
    public function setSmsTemplateModify($args = array()) {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $qry = "
			  update
			    dbo.DY_SMS_AL_SETUP
			  set
			    tp_code=N'$tp_code',
			    tp_name=N'$tp_name',
			    tp_con=N'$tp_con',
			    tp_replace_code=N'$tp_replace_code',
			    tp_use=N'$tp_use'
			  where   
			    idx='$idx'
            ";
        parent::db_connect();
        $rst = parent::execSqlUpdate($qry);
        parent::db_close();
        return $rst;
    }
    
    /*
     *  알림톡 템플릿 등록
     */
    public function setSmsTemplateInsert($args = array()) {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $qry = "
			  Insert Into dbo.DY_SMS_AL_SETUP
			    (tp_code, tp_name, tp_con, tp_replace_code, tp_use, tp_regdate)
			  VALUES 
                (                    
                    N'$tp_code',
                    N'$tp_name',
                    N'$tp_con',
                    N'$tp_replace_code',
                    N'$tp_use',
                    N'$tp_regdate'
                );
            ";
        parent::db_connect();
        $rst = parent::execSqlInsert($qry);
        parent::db_close();
        return $rst;
    }

    /*
     *  알림톡 템플릿 정보
     */
    public function getSmsTemplateInfo($idx) {
        $qry = "select * from DY_SMS_AL_SETUP where idx='$idx'";
        parent::db_connect();
        $rst = parent::execSqlOneRow($qry);
        parent::db_close();
        return $rst;
    }

    /*
     *  알림톡 템플릿 리스트
     */
    public function getSmsTemplateList() {
        $qry = "
			Select * From DY_SMS_AL_SETUP where tp_use='Y' And tp_is_del = N'N'
		";
        parent::db_connect();
        $rst = parent::execSqlList($qry);
        parent::db_close();
        return $rst;
    }

    /*
     *  SMS 매크로 사용 업데이트
     */
    public function setSmsMecroUseUpdate($args = array()) {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $qry = "
            update
                DY_SMS_MECRO
            set
              mecro_usedate='$mecro_usedate'
            where   
              idx ='$mecro_idx'
        ";
        parent::db_connect();
        $rst = parent::execSqlUpdate($qry);
        parent::db_close();
        return $rst;
    }

    /*
     *
     */
    public function getSmsMecroList($member_idx)
    {
        $qry = "
			Select * From DY_SMS_MECRO
			Where mecro_is_del='N' AND member_idx='$member_idx'
		";
        parent::db_connect();
        $rst = parent::execSqlList($qry);
        parent::db_close();
        return $rst;
    }

    /*
     * 최근 사용 10개
     */
    public function getSmsMecroTop10List($member_idx)
    {
        $qry = "
			Select top 10 * From DY_SMS_MECRO
			Where 
			  mecro_is_del='N'
			  AND member_idx='$member_idx'
			order by mecro_usedate desc
		";
        parent::db_connect();
        $rst = parent::execSqlList($qry);
        parent::db_close();
        return $rst;
    }

    /*
     *  SMS 매크로 삭제
     */
    public function setSmsMecroDel($args = array()) {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $qry = "
			Update
			    DY_SMS_MECRO
			set
			  mecro_is_del ='Y'
            Where 
              idx='$idx'
		";
        parent::db_connect();
        $rst = parent::execSqlUpdate($qry);
        parent::db_close();
        return $rst;
    }

    /*
     *  SMS 매크로 등록
     */
    public function setSmsMecroInsert($args = array()) {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $qry = "
			  Insert Into dbo.DY_SMS_MECRO
			    (mecro_msg, mecro_regdate, mecro_is_del, member_idx)
			  VALUES 
                (                    
                    N'$mecro_msg',
                    N'$mecro_regdate',
                    N'$mecro_is_del',
                    N'$member_idx'
                );
            ";
        parent::db_connect();
        $rst = parent::execSqlInsert($qry);
        parent::db_close();
        return $rst;
    }

    /*
     *  SMS 개인발송 기록
     */
    public function setSmsPersonalInsert($args = array()) {

	    $cmid           = "";
	    $order_idx      = "";
	    $order_pack_idx = "";

        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $qry = "
			  Insert Into dbo.DY_SMS_PERSONAL
			    (cmid, sms_send_date, sms_send_time, sms_receive_num, sms_send_num, sms_msg, member_idx, sms_regdate)
			  VALUES 
                (                    
                    N'$cmid',
                    N'$sms_send_date',
                    N'$sms_send_time',
                    N'$sms_receive_num',
                    N'$sms_send_num',
                    N'$sms_msg',
                    N'$member_idx',
                    N'$sms_regdate'
                );
            ";
        parent::db_connect();
        $rst = parent::execSqlInsert($qry);
        parent::db_close();

        if($rst){
        	if($order_idx && $order_pack_idx){

        		$C_CS = new CS();

		        //CS 입력
		        $cs_task = "SMS_SEND";    //상품교환
		        $cs_msg = "<보내는 사람 : " . $sms_send_num . ">" . PHP_EOL;
		        $cs_msg .= "<받는 사람 : " . $sms_receive_num . ">" . PHP_EOL;
		        $cs_msg .= $sms_msg;
        		$inserted_cs_idx = $C_CS->insertCS($order_idx, $order_pack_idx, 0, 0, 0, 'Y', '', $cs_task, $cs_msg, '', '', null, true, "", "");

	        }
        }


	    return $rst;
    }
}
