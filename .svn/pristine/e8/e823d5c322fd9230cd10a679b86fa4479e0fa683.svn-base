<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처관리 관련 Process
 */
//Page Info
$pageMenuIdx = 99;
//Init
include "../_init_.php";


    $response = array();

    switch($mode) {

        //알림톡 템플릿 삭제
        case "SMS_TEMPLATE_DEL":
            $C_SmsMecro = new SmsMecro();

            $args = array();
            $args['idx'] = $_POST['idx'];
            $rst = $C_SmsMecro -> setSmsTemplateDelete($args);

            $response["result"] = true;
        break;

        //알림톡 템플릿 수정
        case "SMS_TEMPLATE_MOD" :
            $C_SmsMecro = new SmsMecro();

            $args = array();
            $args['idx'] = $_POST['idx'];
            $args['tp_code'] = $_POST['tp_code'];
            $args['tp_name'] = $_POST['tp_name'];
            $args['tp_con'] = $_POST['tp_con'];
            $args['tp_replace_code'] = $_POST['tp_replace_code'];
            $args['tp_use'] = $_POST['tp_use'];

            $rst = $C_SmsMecro -> setSmsTemplateModify($args);

            $exec_script = "
                opener.Template_Setup.TemplateListReload();
            ";

            exec_script_and_close($exec_script);
        break;

        //알림톡 템플릿 등록
        case "SMS_TEMPLATE_ADD" :
            $C_SmsMecro = new SmsMecro();

            $args = array();
            $args['tp_code'] = $_POST['tp_code'];
            $args['tp_name'] = $_POST['tp_name'];
            $args['tp_con'] = $_POST['tp_con'];
            $args['tp_replace_code'] = $_POST['tp_replace_code'];
            $args['tp_use'] = $_POST['tp_use'];
            $args['tp_regdate'] = date('Y-m-d H:i:s');

            $rst = $C_SmsMecro -> setSmsTemplateInsert($args);

            $exec_script = "
                opener.Template_Setup.TemplateListReload();
            ";

            exec_script_and_close($exec_script);
        break;

        //알림톡 전송
        case "SMS_AL_SEND" :
            $C_Sms = new Sms();

            $sms_tit = $_POST['sms_tit'];
            $sms_sender = $_POST['sms_sender'];
            $tp_rp_code = $_POST['tp_rp_code'];
            $rp_ex_code = $_POST['rp_ex_code'];
            $tp_code = $_POST['tp_code'];
	        $attached_file = $_POST['attached_file'];

	        //
	        //$attached_file = "btn.json";

            $tp_rp_code_arr = explode(",", $tp_rp_code);
            $rp_ex_code_arr = explode(",", $rp_ex_code);
            $check_date = date('Y-m-d H:i:s');

            $send_data = json_decode($_POST['chk_data'],true);

            $send_cnt = 0;
            foreach($send_data as $k => $v) {
                if($v['receive_hp_num'] != '') {
                    $sms_msg = $_POST['sms_msg'];

                    for($i=0; $i<count($rp_ex_code_arr); $i++) {
                        $ch_val = $v[$rp_ex_code_arr[$i]];
                        $ch_key = $tp_rp_code_arr[$i];
                        $sms_msg = str_replace($ch_key, $ch_val, $sms_msg);
                    }

                    $args = array();
                    $args['receiver_phone'] = str_replace("-", "", $v['receive_hp_num']);
                    //$args['receiver_phone'] = "강제테스트시 주석 제거후 전화번호 입력";
                    $args['send_msg'] = $sms_msg;
                    $args['sender_phone'] = $sms_sender;
                    $args['tp_code'] = $tp_code;
                    $args['req_date'] = $check_date;
                    $args['attached_file'] = $attached_file;
                    //print_r($args);
                    $rst = $C_Sms->setSmsALSendInsert($args);

                    $send_cnt++;
                }
            }

            $args = array();
            $args['al_date'] = $check_date;
            $args['al_title'] = $sms_tit;
            $args['tp_code'] = $tp_code;
            $args['receive_cnt'] = $send_cnt;
            $args['send_phone'] = $sms_sender;
            $args['al_regdate'] = date('Y-m-d H:i:s');
            $C_Sms->setSmsSendResultInsert($args);

            $response["result"] = true;
        break;

        //알림톡 템플릿 테스트
        case "SMS_AL_SEND_TEST" :

            $C_Sms = new Sms();

            //$send_data = json_decode($_POST['chk_data'],true);
            $sms_msg = $_POST['sms_msg'];
            $sms_tit = $_POST['sms_tit'];
            $sms_sender = $_POST['sms_sender'];
            $tp_code = 'bizp_2019030610201504807544118';

            $args['receiver_phone'] = "010"; //woox
            $args['send_msg'] = $sms_msg;
            $args['sender_phone'] = $sms_sender;
            $args['tp_code'] = $tp_code;
            $rst = $C_Sms->setSmsALSendInsert($args);

            $response["result"] = true;
        break;


        //매크로 저장
        case "MECRO_INSERT" :
            $C_SmsMecro = new SmsMecro();

            $args = array();
            $args['mecro_msg'] = $_POST['sms_msg'];
            $args['mecro_regdate'] = date('Y-m-d H:i:s');
            $args['mecro_is_del'] = 'N';
            $args['member_idx'] = $_SESSION['dy_member']['member_idx'];
            $rst = $C_SmsMecro -> setSmsMecroInsert($args);

            if($rst) {
                $response["result"] = true;
            }else {
                $response["result"] = false;
            }
        break;

        //매크로 삭제
        case "MECRO_DEL" :
            $C_SmsMecro = new SmsMecro();

            $args = array();
            $args['idx'] = $idx;
            $rst = $C_SmsMecro -> setSmsMecroDel($args);

            $response["result"] = true;
        break;

        //개별문자 전송
        case "PERSONAL" :
            //매크로 사용 업데이트
            if(isset($_POST['mecro_idx']) && !empty($_POST['mecro_idx'])) {
                $C_SmsMecro = new SmsMecro();

                $args = array();
                $args['mecro_usedate'] = date('Y-m-d H:i:s');
                $args['mecro_idx'] = $_POST['mecro_idx'];
                $rst = $C_SmsMecro -> setSmsMecroUseUpdate($args);
            }

            $C_Sms = new Sms();

            $sms_msg = $_POST['sms_msg'];
            $sms_tit = $_POST['sms_tit'];
            $rphone = $_POST['rphone'];
            $sms_sender = $_POST['sms_sender'];

            $order_idx = $_POST["order_idx"];
            $order_pack_idx = $_POST["order_pack_idx"];

	        $sms_msg = str_replace("\r\n", "\n", $sms_msg);
	        $chk_len = mb_strwidth($sms_msg, "UTF-8");

            if($rphone != '') {
                $args = array();
                $args['receiver_phone'] = str_replace("-", "", $rphone);
                $args['send_msg'] = $sms_msg;
                $args['sender_phone'] = $sms_sender;
                if($chk_len > 90) {

                	if(!$sms_tit) $sms_tit = "덕윤";

                    $args['lms_title'] = $sms_tit;
                }
	            $inserted_cmid = $C_Sms->setSmsSendInsert($args);

                //전송내용 기록
                $C_SmsMecro = new SmsMecro();

                $args = array();
                $args['cmid'] = $inserted_cmid;
                $args['sms_send_date'] = date('Y-m-d');
                $args['sms_send_time'] = date('H:i:s');
                $args['sms_regdate'] = date('Y-m-d H:i:s');
                $args['sms_receive_num'] = str_replace("-", "", $rphone);
                $args['sms_send_num'] = $sms_sender;
                $args['sms_msg'] = $sms_msg;
                $args['order_idx'] = $order_idx;
                $args['order_pack_idx'] = $order_pack_idx;
                $args['member_idx'] = $_SESSION['dy_member']['member_idx'];
                $C_SmsMecro -> setSmsPersonalInsert($args);
            }

            $response["result"] = true;
        break;

        //다중 문자 보내기
        case "PUBLIC" :
            //매크로 사용 업데이트
            if(isset($_POST['mecro_idx']) && !empty($_POST['mecro_idx'])) {
                $C_SmsMecro = new SmsMecro();

                $args = array();
                $args['mecro_usedate'] = date('Y-m-d H:i:s');
                $args['mecro_idx'] = $_POST['mecro_idx'];
                $rst = $C_SmsMecro -> setSmsMecroUseUpdate($args);
            }

            $C_Sms = new Sms();

            $send_data = json_decode($_POST['chk_data'],true);
            $sms_msg = $_POST['sms_msg'];
            $sms_tit = $_POST['sms_tit'];
            $sms_sender = $_POST['sms_sender'];

            $chk_len = mb_strlen($sms_msg, 'UTF-8');

            foreach($send_data as $k => $v) {
                if($v['hp'] != '') {
                    $args = array();
                    $args['receiver_phone'] = str_replace("-", "", $v['hp']);
	                //$args['receiver_phone'] = "강제테스트시 주석 제거후 전화번호 입력";
                    $args['send_msg'] = $sms_msg;
                    $args['sender_phone'] = $sms_sender;
                    if($chk_len > 90) {
	                    if(!$sms_tit) $sms_tit = "덕윤";
                        $args['lms_title'] = $sms_tit;
                    }
                    $rst = $C_Sms->setSmsSendInsert($args);
                }
            }

            $response["result"] = true;
        break;
    }

echo json_encode($response);
exit;
?>