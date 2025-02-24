<?php
/**
 * 로그인 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Sms extends DBConn
{

    /*
	 *  알림톡 발송 이력
	 */
    public function setSmsSendResultInsert($args = array()) {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $qry = "
			  Insert Into DY_SMS_AL_SEND_RESULT
			    (al_title, al_date, tp_code, receive_cnt, send_phone, send_fail_cnt, send_succ_cnt, al_regdate)
			  VALUES 
                (               
                    N'$al_title',     
                    N'$al_date',
                    N'$tp_code',
                    N'$receive_cnt',
                    N'" . $send_phone. "',
                    N'0',
                    N'0',
                    N'" . $al_regdate. "'
                    
                );
            ";
        parent::db_connect();
        $rst = parent::execSqlInsert($qry);
        parent::db_close();
        return $rst;

    }
    /*
	 *  알림톡 발송
	 */
    public function setSmsALSendInsert($args = array())
    {
    	$attached_file = "";

        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $uniq_key = uniqid();
        $now_date = date('Y-m-d H:i:s');

        $qry = "
			  Insert Into DYFLUX_SMS.BIZ_MSG
			    (MSG_TYPE, CMID, REQUEST_TIME, SEND_TIME, DEST_PHONE, SEND_PHONE, MSG_BODY, TEMPLATE_CODE, SENDER_KEY, NATION_CODE, ATTACHED_FILE)
			  VALUES 
                (
                    N'6',
                    N'$uniq_key',
                    N'$req_date',
                    N'$now_date',
                    N'" . $receiver_phone . "',
                    N'" . $sender_phone . "',
                    N'" . $send_msg . "',
                    N'" . $tp_code . "',
                    N'" . DY_PPURIO_AL_KEY . "',
                    N'82', 
                    N'$attached_file'
                    
                );
            ";

        parent::db_connect();
        $rst = parent::execSqlInsert($qry);
        parent::db_close();
        return $rst;
    }

    /*
	 *
	 *
	 *  SMS 발송 90바이트 이상일시 LMS 발송
	 */
    public function setSmsSendInsert($args = array()) {
        if (is_array($args)) foreach ($args as $k => $v) ${$k} = $v;

        $uniq_key = uniqid();
        $now_date = date('Y-m-d H:i:s');
        $req_date = date('Y-m-d H:i') . ":00";

        if(isset($lms_title)) { //LMS
            $qry = "
			  Insert Into DYFLUX_SMS.BIZ_MSG
			    (MSG_TYPE, CMID, REQUEST_TIME, SEND_TIME, DEST_PHONE, SEND_PHONE, SUBJECT, MSG_BODY)
			  VALUES 
                (
                    N'5',
                    N'$uniq_key',
                    N'$req_date',
                    N'$now_date',
                    N'" . $receiver_phone . "',
                    N'" . $sender_phone . "',
                    N'" . $lms_title . "',
                    N'" . $send_msg . "'
                );
            ";

        }else { //SMS
            $qry = "
			  Insert Into DYFLUX_SMS.BIZ_MSG
			    (MSG_TYPE, CMID, REQUEST_TIME, SEND_TIME, DEST_PHONE, SEND_PHONE, MSG_BODY)
			  VALUES 
                (
                    N'0',
                    N'$uniq_key',
                    N'$req_date',
                    N'$now_date',
                    N'" . $receiver_phone . "',
                    N'" . $sender_phone . "',
                    N'" . $send_msg . "'
                );
            ";
        }
	    parent::db_connect();
        $rst = parent::execSqlInsert($qry);
	    parent::db_close();

	    $qry = "
	        Select CMID From DYFLUX_SMS.BIZ_MSG
			Order by REQUEST_TIME DESC
			LIMIT 1
	    ";
	    parent::db_connect();
	    $inserted_cmid = parent::execSqlOneCol($qry);
	    parent::db_close();

        return $inserted_cmid;
    }
}

?>