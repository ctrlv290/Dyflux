<?php
/*로그인 체크*/
function isLogin()
{
	global $GL_Member;
	if(isset($GL_Member))
	{
		$C_Login = new Login();
		if($C_Login -> checkLoginData())
		{
			$C_Login = null;
			return true;
		}else{
			$C_Login = null;
			return false;
		}
	}else{
		return false;
	}
}

/**
 * 간단한 로그인 세션 체크
 */
function isHasLoginSession()
{
	if($_SESSION["dy_member"] && isset($_SESSION["dy_member"])){
		return true;
	}else{
		return false;
	}
}

/*로그인 체크 관리자*/
function isAdminLogin()
{
	global $GL_Member;
	if(isset($GL_Member))
	{
		$C_Login = new Login();
		if($C_Login -> checkLoginData())
		{
			$C_Login = null;
			if($GL_Member["member_type"] == "ADMIN")
			{
				return true;
			}else{
				return false;
			}
		}else{
			$C_Login = null;
			return false;
		}
	}else{
		return false;
	}
}

/*로그인 체크 관리자*/
function isDYLogin()
{
	global $GL_Member;
	if(isset($GL_Member))
	{
		$C_Login = new Login();
		if($C_Login -> checkLoginData())
		{
			$C_Login = null;
			if($GL_Member["member_type"] == "ADMIN" || $GL_Member["member_type"] == "USER")
			{
				return true;
			}else{
				return false;
			}
		}else{
			$C_Login = null;
			return false;
		}
	}else{
		return false;
	}
}

function isNotAdminLoginGoRoot($msg = "", $not_login_url = "", $logged_url = "")
{
	if($msg == "") $msg = "로그인 후 이용해주세요.";

	if($not_login_url == "")
	{
		$not_login_url = DY_URL."/login.php";
	}

	if(!isAdminLogin())
	{
		put_msg_and_go($msg, $not_login_url);
	}elseif($logged_url){
		go_replace($logged_url);
	}
}

function AdminLogOut($msg = "", $logout_url = "")
{
	unset($_SESSION["dy_member"]);
	ss_auth_clean();
	ss_donate_clean();

	if($msg == "") $msg = "로그아웃 되었습니다.";

	if($logout_url == "")
	{
		$logout_url = DY_ADMIN_URL."/login.php";
	}

	put_msg_and_go($msg, $logout_url);
}

function LogOut($msg = "", $logout_url = "")
{
	unset($_SESSION["dy_member"]);
	ss_auth_clean();
	ss_donate_clean();

	if($msg == "") $msg = "로그아웃 되었습니다.";

	if($logout_url == "")
	{
		$logout_url = DY_URL."/main.php";
	}

	put_msg_and_go($msg, $logout_url);
}

function LoginCheck($return_url)
{
	if(!isLogin())
	{
		$goto_url = "";
		if($return_url)
		{
			$goto_url = $return_url;
		}
		put_msg_and_go("로그인이 필요한 서비스입니다.", LoginUrl($goto_url));
	}
}

function LoginUrl($return_url)
{
	$loginUrl = "/login.php";
	if($return_url)
	{
		$loginUrl .= "?return_url=".urlencode($return_url);;
	}
	return DY_URL.$loginUrl;
}

function MLoginUrl($return_url)
{
	$loginUrl = "/m/";
	if($return_url)
	{
		$loginUrl .= "?return_url=".urlencode($return_url);;
	}
	return DY_URL.$loginUrl;
}

/**
 * 모바일 로그인 체크
 * @param $return_url
 */
function MLoginCheck($return_url)
{
	if(!isMLogin())
	{
		$goto_url = "";
		if($return_url)
		{
			$goto_url = $return_url;
		}
		put_msg_and_go("로그인이 필요한 서비스입니다.", MLoginUrl($goto_url));
	}
}

/**
 * 모바일 로그인 확인
 * @return bool
 */
function isMLogin()
{
	global $GL_Member_M;
	if(isset($GL_Member_M))
	{
		$C_Login = new Login();
		if($C_Login -> checkMLoginData())
		{
			$C_Login = null;
			return true;
		}else{
			$C_Login = null;
			return false;
		}
	}else{
		return false;
	}
}

// 셀렉트 박스 만들기
// make_select('test_select',$value_array,$value,$etc, '::선택::')
function make_select()
{
	$args = @func_get_args ();
	$paraArr = array(
		'selectName'
	,'selArr'
	,'vals'
	,'etc'
	,'basic'
	);
	$argsCnt = count($args);
	for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

	$str = "<select name='$selectName' $etc>";
	if($basic)
	{
		$str .= "<option value=''>$basic</option>";
	}

	if(count($selArr) && is_array($selArr))
	{
		foreach ($selArr as $key => $value)
		{
			$selValue = "";
			$selName = "";
			$selValue = $key;
			$selName = $value;

			$selected = "";
			if($vals && $vals == $selValue) $selected = "selected";
			$str .= "<option value='$selValue' $selected>$selName</option>";
		}
	}
	$str .= "</select>";

	return $str;
}


// 셀렉트 박스 만들기 - 가나다순으로 정렬
function make_select_ASC()
{
	$args = @func_get_args ();
	$paraArr = array(
		'selectName'
	,'selArr'
	,'vals'
	,'etc'
	,'basic'
	);
	$argsCnt = count($args);
	for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

	$str = "<select name='$selectName' $etc>";
	if($basic)
	{
		$str .= "<option value=''>$basic</option>";
	}

	if(count($selArr) && is_array($selArr))
	{

		asort($selArr);

		foreach ($selArr as $key => $value)
		{
			$selValue = "";
			$selName = "";
			$selValue = $key;
			$selName = $value;

			$selected = "";
			if($vals && $vals == $selValue) $selected = "selected";
			$str .= "<option value='$selValue' $selected>$selName</option>";
		}
	}
	$str .= "</select>";

	return $str;
}

// 키값이 없는 배열일 경우
function make_select2()
{
	$args = @func_get_args ();
	$paraArr = array(
		'selectName'
	,'selArr'
	,'vals'
	,'etc'
	,'basic'
	);
	$argsCnt = count($args);
	for($i = 0; $i < $argsCnt ; $i++) ${$paraArr[$i]} = $args[$i];

	$str = "<select name='$selectName' $etc>";
	if($basic)
	{
		$str .= "<option value=''>$basic</option>";
	}

	if(count($selArr) && is_array($selArr))
	{
		foreach ($selArr as $value)
		{
			$selValue = "";
			$selName = "";
			$selValue = $value;
			$selName = $value;

			$selected = "";
			if($vals && $vals == $selValue) $selected = "selected";
			$str .= "<option value='$selValue' $selected>$selName</option>";
		}
	}
	$str .= "</select>";

	return $str;
}

// 라디오버튼 생성
//$use_value_arr = array('미사용' => 0, '사용' => 1);
//make_radio ($use_value_arr, 'bl_type', $bl_type);

function make_radio ($value_arr, $name, $value, $etc = '') {
	if (is_array($value_arr)) {
		foreach ($value_arr as $k => $v) {
			if (!$value) $value = 0;
			$checked = ($v == $value)? 'checked' : '';
			$str .= '<input type="radio" name="' . $name . '" value="' . $v . '" ' . $checked . $etc . '>' . $k;
		}
	}

	return $str;
}

// list 에서 make_select를 위한 데이터 형식으로 변환
// $get_arr = array('ca_idx','ca_name');
// reset_for_make_select (&$args, $get_arr);
function reset_for_make_select (&$args, $get_arr) {
	$args_cnt = count($args);
	for ($i = 0; $i < $args_cnt; $i++) {
		$tmp_arr[$args[$i][$get_arr[0]]] = $args[$i][$get_arr[1]];
		echo $args[$i][$get_arr[0]] . '/' . $args[$i][$get_arr[1]];
	}

	$args = '';
	$args = $tmp_arr;
}

// colgroup 스트링 만들기
function colgroupList($args) {
	$args_cnt = count($args);
	for($i = 0; $i < $args_cnt; $i++)
	{
		$retunrStr .= "<col width=\"".$args[$i]."\">\n";
	}

	return $retunrStr;
}

// colgroup 스트링 만들기
function colgroupList2 ($args) {
	$args_cnt = count($args);
	$retunrStr = '<colgroup>';
	for($i = 0; $i < $args_cnt; $i++)
	{
		$retunrStr .= "<col width=\"".$args[$i]."\">\n";
	}
	$retunrStr .= '</colgroup>';
	return $retunrStr;
}

// 글 인코딩 하기
function enc_contents ($contents) {
	$result = htmlspecialchars(addslashes($contents));

	return $result;
}

// 글 디코딩 하기
function dec_contents ($contents) {
	$result = stripslashes($contents);

	return $result;
}

// 글 디코딩 하기
function dec_contents_view ($contents) {
	$result = htmlspecialchars_decode(nl2br(stripslashes($contents)));

	return $result;
}

// 에디터용 글 인코딩 하기
function enc_contents_edit ($contents) {
	$result = htmlspecialchars(addslashes(noScriptTags($contents)));

	return $result;
}


// 에디터용 글 디코딩 하기
function dec_contents_edit ($contents) {
	$result = html_entity_decode(stripslashes($contents));

	return $result;
}

// alert 메세지 출력
function put_msg($msg) {
	echo "<script>alert('$msg');</script>";
	exit;
}

// alert 메세지 출력
function put_msg_top($msg) {
	echo "<script>alert('$msg'); top.location.reload();</script>";
	exit;
}

// alert 메세지 출력 후 보냄
function put_msg_and_go($msg,$url) {
	echo "<script>alert('$msg'); location.replace('$url');</script>";
	exit;
}

// alert 메세지 출력 후 뒤로
function put_msg_and_back($msg) {
	echo "<script>alert('$msg'); history.back();</script>";
	exit;
}

// alert 메세지 출력 후 뒤로
function put_msg_and_top_back($msg) {
	echo "<script>alert('$msg'); top.history.back();</script>";
	exit;
}

// 해당 url 로 보냄
function go_url($url) {
	echo "<script>location.href='$url';</script>";
	exit;
}

// 해당 url 로 보냄
function top_go_url($url) {
	echo "<script>top.location.href='$url';</script>";
	exit;
}

// alert 후 해당 url 로 보냄
function put_msg_top_go_url($msg , $url) {
	echo "<script>alert('$msg'); top.location.href='$url';</script>";
	exit;
}

// alert 후 해당 url 로 보냄
function put_msg_top_go_replace($msg , $url) {
	echo "<script>alert('$msg'); top.location.replace('$url');</script>";
	exit;
}

// alert 메세지 출력 후 창 닫기
function put_msg_and_stop($msg) {
	echo $msg;
	exit;
}

// alert 메세지 출력 후 창 닫기
function put_msg_and_close($msg) {
	echo "<script>alert('$msg'); self.close();</script>";
	exit;
}

// alert 메세지 출력 후 부모창 리로드하고 창 닫기
function put_msg_and_close2($msg) {
	echo "<script>alert('$msg'); opener.location.reload(); self.close();</script>";
	exit;
}

// alert 메세지 출력 후 부모창 특정위치로 보내고 창 닫기
function put_msg_and_close3($msg, $url) {
	echo "<script>alert('$msg'); opener.location.href='$url'; self.close();</script>";
	exit;
}

// alert 메세지 출력 후 부모창의 최상위 레이어를 특정위치로 보내고 창 닫기
function put_msg_and_close4($msg, $url) {
	echo "<script>alert('$msg'); opener.top.location.href='$url'; self.close();</script>";
	exit;
}

// alert 메세지 출력 후  스크립트 실행 한 뒤 창 닫기
function put_msg_and_exec_script_and_close($msg, $script) {
	echo "
			<script>
				alert('$msg'); 
				$script			 
				self.close();
			</script>
	";
	exit;
}
// 스크립트 실행 후 창닫기
function exec_script_and_close($script) {
	echo "
			<script>
				$script			 
				self.close();
			</script>
	";
	exit;
}

// 해당 url 로 보냄
function go_replace($url) {
	echo "<script>location.replace('$url');</script>";
	exit;
}
// 해당 url 로 보냄
function go_replace_opener($url) {
	echo "<script>opener.location.replace('$url');</script>";
	exit;
}


//팝업을 띄운 후 본창 이동
function popup_and_go($popup_url, $popup_option, $url)
{
	echo "
		<script>
			var url = '".$popup_url."';
			var opt = '".$popup_option."';
			window.open(url, '', opt);
			location.href = '".$url."';
		</script>
	";
	exit;
}

function date_reform1($dateForm) // 0000.00.00
{
	$dateForm_arr = explode(" ",$dateForm);
	$dateForm_arr2 = explode("-",$dateForm_arr[0]);

	return $dateForm_arr2[0] . "." . $dateForm_arr2[1] . "." . $dateForm_arr2[2];
}

function date_reform2($dateForm) // 0000/00/00 00:00:00
{
	$dateForm_arr = explode(" ",$dateForm);
	$dateForm_arr2 = explode("-",$dateForm_arr[0]);

	return $dateForm_arr2[0] . "/" . $dateForm_arr2[1] . "/" . $dateForm_arr2[2] . ' ' . $dateForm_arr[1];
}

function date_reform3($dateForm)
{
	$dateForm1 = substr($dateForm,0,4);
	$dateForm2 = substr($dateForm,4,2);
	$dateForm3 = substr($dateForm,6,2);

	return $dateForm1 . "." . $dateForm2 . "." . $dateForm3;
}

//-----------------------------------------------
//  한글 문자 자르는 함수
//-----------------------------------------------
function fn_text_cut_kr($str,$start,$len) { 		// 한글 문자 자르기	

	$str = trim($str);
	$backcnt= 0; // 시작첫글자에서 뒤로간 byte 수 (space나 영/숫자가 나올때 까지 또는 문장의 맨 처음시작까지) 
	$cntcheck =0;

	if ($start>0 ) {
		if(ord($str[$start]) >= 128) { // 첫 시작글자가 한글이면 
			for ($i=$start;$i>0;$i--) {
				if (ord($str[$i]) >= 128) {
					$backcnt++;
				}else{
					break;
				}
			}

			if( (ord($str[0]) < 128) || ($backcnt != $start)) {    //첫글자가 한글이 아니거나, 영숫자 없고, 띄어 쓰기 없는 한글로만 되어 있는 경우가 아니면 
				$start= ($backcnt%2) ? $start : $start-1;    //첫글짜가 깨지면, 시작점 = (시작 byte -1byte) 
			}

			if (($backcnt%2)==1) {
				$cntcheck = 0;    //문장 시작 첫글자 안짤림 
			}else{
				$cntcheck = 1;        //문장 시작 첫글자 짤림 
			}

		}
	}

	$backcnt2= 0; // 마지막글자에서 뒤로간 byte 수 (space나 영/숫자가 나올때 까지) 

	for ($i=($len-1);$i>=0;$i--) {
		if (ord($str[$i+$start]) >= 128){
			$backcnt2++;
		}else{
			break;
		}
	}

	if (($backcnt2%2)==1) {
		$cntcheck2 = 1;    //문장 마지막 글자 짤림 
	}else{
		$cntcheck2 = 0;        //문장 마지막 글자 안짤림 
	}

	(int)$cnt=$len-abs($backcnt2%2);    //자를 문자열 길이 (byte) 
	if(($cntcheck+$cntcheck2)==2) $cnt+=2;    //$cntcheck가 짤리고, $cntcheck2가 짤리는 경우 

	$cutstr = substr($str,$start,$cnt);
	if(strlen($str) && strlen($str) > strlen($cutstr))
		$cutstr .= "..";
	return $cutstr;
}

// 이미지 비율대로 맞추기 (틀안에 들어가도록)
function get_re_size ($img_abs_root, $re_w, $re_h) {
	$image_size		= @getimagesize($img_abs_root);
	$width 				= $image_size[0];
	$height				= $image_size[1];

	if ($width > $height) {
		$rate1 = round(1 / round($height / $width , 3),2);
		$rate2 = round($re_w / $width,2);
		$tmp_height = $height * $rate2;

		if ($tmp_height > $re_h) {
			$tmp_width 	= $re_h * $rate1;
			$tmp_height = $re_h;
		} else {
			$tmp_width = $re_w;
		}
	} else if ($width < $height) {
		$rate1 = round(1 / round($width / $height , 3),2);
		$rate2 = round($re_h / $height,2);
		$tmp_width = $width * $rate2;

		if ($tmp_width > $re_w) {
			$tmp_height = $re_w * $rate1;
			$tmp_width 	= $re_w;
		}	else {
			$tmp_height = $re_h;
		}
	} else {
		if ($re_w > $re_h) {
			$tmp_height = $re_h;
			$tmp_width 	= $re_h;
		} else {
			$tmp_height = $re_w;
			$tmp_width 	= $re_w;
		}
	}

	$rst[] = round($tmp_width);
	$rst[] = round($tmp_height);

	return $rst;
}

// 이미지 비율대로 맞추기 (틀 밖에 맞추기)
function get_re_size2  ($img_abs_root, $re_w, $re_h) {
	$image_size		= @getimagesize($img_abs_root);
	$width 				= $image_size[0];
	$height				= $image_size[1];

	$rate_w = round(1 / round($width / $re_w , 3),2);
	$rate_h = round(1 / round($height / $re_h , 3),2);

	if ($rate_h > $rate_w) {
		$tmp_width = $width * $rate_h;
		$tmp_height = $height * $rate_h;
	} else if ($rate_h < $rate_w) {
		$tmp_width = $width * $rate_w;
		$tmp_height = $height * $rate_w;
	} else {
		$tmp_width = $width * $rate_w;
		$tmp_height = $height * $rate_h;
	}

	$rst[] = round($tmp_width);
	$rst[] = round($tmp_height);

	return $rst;
}

//Email 유효성 체크
function check_email($email)
{
	if (!eregi("^[^@ ]+@[a-zA-Z0-9\-\.]+\.+[a-zA-Z0-9\-\.]", $email))
	{
		return false;
	}else{
		return true;
	}
}

//2 Digit
function make2digit($val)
{
	if(strlen($val) < 2){
		$val = "0".$val;
	}

	return $val;
}

// 2의 자승 합산 풀기
function untieDigit ($value,$rst = array()) {
	if ($value) {
		$cmp_value = 0;
		$i = 1;
		while ($cmp_value <= $value) {
			$cmp_value = pow(2,$i);
			$i++;
		}
		$digit = $i - 2;

		if ($digit >= 0) {
			$digit_value = pow(2,$digit);
			if ($digit) {
				$mod_value = $value % $digit_value;
			}

			$rst[] = $digit_value;
			if ($mod_value) {
				$cnt = $cnt + 1;
				$rst = untieDigit ($mod_value,$rst);

			}
		}
	}
	sort($rst);
	return $rst;
}

// 2자승 의 자승값 구하기
function untieDigitMakeLog ($value) {
	if (is_array($value)) {
		$rst = "";
		foreach ($value as $v) {
			$rst[] = log($v) / log(2);
		}
	}
	return $rst;
}

// 스크립트 태그 , 스타일 태그, 주석태그 제거
function noScriptTags ($str) {
	// 모든태그 제거
	/*$pattern = array('/<!--(.*?)-->/s', '/<script[^>]*?>(.*?)<\/script>/is', '/<style[^>]*?>(.*?)<\/style>/is', '/<(.*?)>/s');*/
	$pattern = array('/<!--(.*?)-->/s', '/<script[^>]*?>(.*?)<\/script>/is', '/<style[^>]*?>(.*?)<\/style>/is');
	return preg_replace($pattern, '', $str);
}

function isblank($str)
{
	$p_arr	= array("　","\n","&nbsp;"," ");
	$temp	= str_replace($p_arr,"",$str);
	if(eregi("[^[:space:]]",$temp)) return 0;
	return 1;
}

// 영어와 숫자만 있는지 체크 [ 틀림0   ,  맞음1 ]
function isEngNum($str)
{
	if(eregi("[^a-zA-Z0-9]",$str)) return 0;
	return 1;
}

//숫자만 있는 체크 [ 맞음 1 , 틀림 0 ]
function isNum($str)
{
	if(eregi("[^0-9]",$str)) return 0;
	return 1;
}

# 특정 문자가 한글의 범위내(0xA1A1 - 0xFEFE)에 있는지 검사
function isHan($char)
{
	for($i = 0; $i < strlen($char); $i++) if(ord($char[$i]) < 0x80)  return 0;
	return 1;
}

// 이메일 체크 [ 맞음 이메일 , 틀림 빈값 ]
function ismail( $str )
{
	if( eregi("([a-z0-9\_\-\.]+)@([a-z0-9\_\-\.]+)", $str) ) return 1;
	return 0;
}

// 썬네일 이미지 명 가져오기
function getThumbName ($name) {
	$nameArr = explode('.', $name);

	return $nameArr[0] . '_s.' . $nameArr[1];
}

//카드번호에 '-' 넣기
function addDashForCardNumber($card_no)
{
	$rVal = false;

	if(strlen($card_no) == 16){
		$rVal = substr($card_no, 0, 4) . '-' . substr($card_no, 4, 4) . '-' . substr($card_no, 8, 4) . '-' . substr($card_no, 12, 4);
	}

	return $rVal;
}
//충전번호에 '-' 넣기
function addDashForChargeNumber($charge_no)
{
	$rVal = false;

	if(strlen($charge_no) == 14){
		$rVal = substr($charge_no, 0, 6) . '-' . substr($charge_no, 6, 2) . '-' . substr($charge_no, 8, 6);
	}

	return $rVal;
}

// 정보 숨김
function blindInfo ($value,$view_cnt) {
	$value_len = strlen($value);
	$rst = substr($value,0,$view_cnt);

	for ($i = 0; $i < $value_len - $view_cnt; $i++) {
		$addstar .= '*';
	}

	return $rst . $addstar;
}

// , 를 삭제하고 인트형으로 변환해서 보냄
function makeInt ($value) {
	return str_replace(',','',$value) * 1;
}

// 운영 수수료
function check_rate_fee ($value) {
	global $GL_aside_rate;
	$rst = 0;
	if ($value) {
		foreach ($GL_aside_rate as $k => $v) {
			if ($value <= $k) {
				$rst = $v;
				break;
			}
		}
	}
	return $rst;
}

function reserve_box_l ($image, $rate, $link = '') {
	global $GL_upimg_alliance_url;

	$image = $GL_upimg_alliance_url . $image;

	if ($image && $rate) {
		//소수점 둘째자리 버림..
		$rate = floor($rate*10)/10;
		$str = "
	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
		<tr>
			<td width=\"67\"><a href=\"$link\" target=\"_self\"><img src=\"".$image."\" border=\"0\"/></a></td>
			<td>
			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"37\" height=\"22\" background=\"/images/assets/contents_main/element4/percent_bg.gif\">
				<tr>
					<td align=\"center\" style=\"font-family:'돋움'; font-size: 12px; color: #ff5400;font-weight:bold; text-align:right; vertical-align:bottom; line-height:19px; padding-right:2px;\">".$rate."</td>
					<td style=\"font-size:12px; color:#ff5400; text-align:left; vertical-align:bottom; line-height:19px; padding-right:2px;font-family:'돋움';\">%</td>
				</tr>
			</table></td>
		</tr>
	</table>
	";
	} else {
		$str = "&nbsp;";
	}

	return $str;
}

// 클릭로그를 잡기위한 LINK 변환
function enc_log_link ($args) {
	extract($args);
	// pos 클릭한 섹션 위치, type 쇼핑몰 타입 , url 되돌려 보내야할 url
	$reurl = '&alli_idx=' . $alli_idx . '&pos=' . $pos . '&type=' . $type . '&target=' . $target;

	return $reurl;
}

// 엑셀 구간 설정
function excel_callback($buffer)
{
	$buffer_arr = explode('<excel>',$buffer);
	return $buffer_arr[1];
}

function make_paging ($args) {
	/*
	$args['show_row'] 	= 10;			
	$args['show_page'] 	= 10;				
	$args['total'] 			= 100;				
	$args['add_element'] 	= 'mp=2&dep=1';				
	$args['front_img'] 		= "/images/ic_pre.gif";
	$args['next_img'] 		= "/images/ic_next.gif";
	*/
	extract($args);
	$page	  	= (!$page) ? 1 : $page;
	$startnum 	= ($page - 1) * $show_row;

	$totalpages	= ceil($total / $show_row);
	$articlecount = $total - (($page - 1) * $show_row) + 1;

	$startpage 	= ((ceil(($page / $show_page) - 0.01) - 1) * $show_page) + 1;
	$endpage   	= $startpage + ($show_page - 1);
	$endpage   	= ($totalpages < $endpage) ? $totalpages : $endpage;
	$prevpage  	= ($startpage != 1) ? $startpage - $show_page : 1;
	$nextpage  	= (($endpage + 1) > $totalpages) ? $totalpages : $endpage + 1;
	if($total > 0)
	{
		if($show_page < $totalpages)		// 이전 이후 보여주기
		{
			$front = ($front_img)? "<img src='$front_img' border='0'>":"◀";
			$next = ($next_img)? "<img src='$next_img' border='0'>":"▶";
		}
		if($add_element) $add_element = "&".$add_element;

		$return_String = "<a href='$pagename?page=$prevpage"."$add_element'> ";
		$return_String .= $front;
		$return_String .= "</a>&nbsp;";
		for($i = $startpage ; $i <= $endpage ; $i++){
			$return_String .= ($i != $page)? "<a href='$pagename?page=$i"."$add_element'>":"";
			$return_String .= ($i == $page)? " &nbsp;&nbsp;<b>$i</b>&nbsp;&nbsp;" : " &nbsp;&nbsp;" . $i . "&nbsp;&nbsp; ";
			$return_String .= ($i != $page)? "</a>":"";
			$return_String .= ($i != $endpage) ? "  " : "";
		}

		$return_String .= "&nbsp;<a href='$pagename?page=$nextpage"."$add_element'> ";
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

function replaceSP($val)
{
	return str_replace("'", "''", $val);
}

// ex) makeExtract ('-', 'zipcode' , '158-525'); 
// rst) zipcode1 => 158 , zipcode2 => 525
function makeExtract ($bar,$name , $val) {
	$str_arr = explode($bar, $val);

	$i = 1;
	foreach ($str_arr as $v) {
		$rst[$name . $i] = $v;
		$i++;
	}
	return $rst;
}

function makeOrderNum () {
	$rand_num = rand(1,99999);
	$rand_num_len = strlen($rand_num);
	if (strlen($rand_num) < 5) {
		$feel_zero = 5 - $rand_num_len;
		for ($i = 0; $i < $feel_zero; $i++) {
			$add_zero .= '0';
		}
		$rand_num = $add_zero . $rand_num;
	}
	return $order_num = date('ymdHis') . '_' . $rand_num;
}

//그누보드 자동링크
function auto_link($str)
{
	// 속도 향상 031011
	$str = preg_replace("/&lt;/", "\t_lt_\t", $str);
	$str = preg_replace("/&gt;/", "\t_gt_\t", $str);
	$str = preg_replace("/&amp;/", "&", $str);
	$str = preg_replace("/&quot;/", "\"", $str);
	$str = preg_replace("/&nbsp;/", "\t_nbsp_\t", $str);
	$str = preg_replace("/([^(http:\/\/)]|\(|^)(www\.[^[:space:]]+)/i", "\\1<A HREF=\"http://\\2\" TARGET='_blank'>\\2</A>", $str);
	$str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,]+)/i", "\\1<A HREF=\"\\2\" TARGET='_blank'>\\2</A>", $str);
	// 이메일 정규표현식 수정 061004
	//$str = preg_replace("/(([a-z0-9_]|\-|\.)+@([^[:space:]]*)([[:alnum:]-]))/i", "<a href='mailto:\\1'>\\1</a>", $str);
	$str = preg_replace("/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i", "<a href='mailto:\\1'>\\1</a>", $str);
	$str = preg_replace("/\t_nbsp_\t/", "&nbsp;" , $str);
	$str = preg_replace("/\t_lt_\t/", "&lt;", $str);
	$str = preg_replace("/\t_gt_\t/", "&gt;", $str);

	return $str;
}

function getLastDay($year, $month)
{
	return date("Y-m-d", @mktime(0, 0, 0, $month+1, 1, $year) - 1 );
}

function getNextMonth($year, $month)
{
	return date("Y-m-d", @mktime(0, 0, 0, $month+1, 1, $year));
}

// 암호화
function decrypt_md5($hex_buf, $key="password")
{
	$len = strlen($hex_buf);
	for ($i=0; $i<$len; $i+=2)
		$buf .= chr(hexdec(substr($hex_buf, $i, 2)));

	$key1 = pack("H*", md5($key));
	while($buf)
	{
		$m = substr($buf, 0, 16);
		$buf = substr($buf, 16);

		$c = "";
		for($i=0;$i<16;$i++)
		{
			$c .= $m{$i}^$key1{$i};
		}

		$ret_buf .= $m = $c;
		$key1 = pack("H*",md5($key.$key1.$m));
	}

	return($ret_buf);
}

// 복호화
function encrypt_md5($buf, $key="password")
{
	$key1 = pack("H*",md5($key));
	while($buf)
	{
		$m = substr($buf, 0, 16);
		$buf = substr($buf, 16);

		$c = "";
		for($i=0;$i<16;$i++)
		{
			$c .= $m{$i}^$key1{$i};
		}
		$ret_buf .= $c;
		$key1 = pack("H*",md5($key.$key1.$m));
	}

	$len = strlen($ret_buf);
	for($i=0; $i<$len; $i++)
		$hex_data .= sprintf("%02x", ord(substr($ret_buf, $i, 1)));
	return($hex_data);
}


//비트연산 {배열로 넘어 온 값을 비트연산 값으로 변환}
function bit_get($_fld) {
	global ${"$_fld"};
	$fld = ${"$_fld"};
	if(!is_array($fld)) {
		${"$_fld"} = 0;
		return 0;
	}
	$tmp = 0;
	foreach($fld as $k=>$v) {
		$tmp = $tmp | (0+$v);
	}
	${"$_fld"} = $tmp;
}
//비트연산 값을 $sep 으로 구분 하여 String으로 반환
function bit_str($bit,$arr,$sep="") {

	if(!is_array($arr)) return NULL;

	$cnt = count($arr);
	$b = 1; $str = ""; $cnt_match =0;
	for($n=0;$n<$cnt;$n++) {
		if($b & $bit) {
			if($cnt_match>0)
				$str .= $sep;
			$str .= $arr[$b];
			$cnt_match ++;
		}
		$b <<= 1;
	}
	return $str;
}

function percentBox ($args) {
	if (is_array($args))
		extract($args);

	if ($img && $percent) {
		$rst = "    
		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"106\" height=\"34\">
		    <tr>
		        <td width=\"65\" align=\"center\"><img src=\"$img\"></td>
		        <td>
		    		<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"37\" height=\"22\" background=\"/images/assets/contents_main/element4/percent_bg.gif\">
		            <tr>
		                <td align=\"center\" style=\"font-family:'돋움'; font-size: 12px; color: #ff5400;font-weight:bold; text-align:right; vertical-align:bottom; line-height:19px; padding-right:2px;\">$percent</td>
		                <td style=\"font-size:12px; color:#ff5400; text-align:left; vertical-align:bottom; line-height:19px;font-family:'돋움';\">%</td>
		            </tr>
		        </table>
		        </td>
		    </tr>
		</table>
        ";
	}
	return $rst;
}


function display2Text($val)
{
	if($val == "1")
	{
		return '<span class="redbold"><i class="fa fa-eye fa-lg "></i>&nbsp;노출함</span>';
	}else{
		return '<span class="gray">노출안함</span>';
	}
}


function makePageParameter($ary)
{
	global $_GET;

	if(is_array($ary)){
		$paramRstArray = array();
		$parameters = "";
		foreach($ary as $v)
		{
			if(isset($_GET[$v]) && trim($_GET[$v]) != "")
			{
				$paramRstArray[] = $v."=".trim($_GET[$v]);
			}
		}
		if(count($paramRstArray) > 0)
		{
			$parameters = implode("&", $paramRstArray);
		}
	}
	return $parameters;
}


function htmlentities_utf8($str)
{
	return htmlentities($str, ENT_COMPAT, 'UTF-8');
}


function print_r2($var) {
	ob_start();
	print_r($var);
	$str = ob_get_contents();
	ob_end_clean();
	$str = preg_replace("/ /", "&nbsp;", $str);
	echo nl2br("$str");
}

function print_r3($array, $return = false) {
	$string = '<pre>' . htmlentities(print_r($array, true)) . '</pre>';
	if ($return) {
		return $string;
	}
	echo $string;
}


function returnConstants ($prefix) {
	foreach (get_defined_constants() as $key=>$value)
		if (substr($key,0,strlen($prefix))==$prefix)  $dump[$key] = $value;
	ksort($dump);
	if(empty($dump)) { return "Error: No Constants found with prefix '".$prefix."'"; }
	else { return $dump; }
}

// 세션변수값 얻음
function get_session($session_name)
{
	return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}

// 세션변수 생성
function set_session($session_name, $value)
{
	if (PHP_VERSION < '5.3.0')
		session_register($session_name);
	// PHP 버전별 차이를 없애기 위한 방법
	$$session_name = $_SESSION[$session_name] = $value;
}

//권한 체크
function adminAuthCheck($auth_level)
{
	global $member;
	if($auth_level > 0)
	{
		if($member["level"] < $auth_level)
		{
			put_msg_and_back("접근권한이 없습니다.");
			exit;
		}
	}
}

function adminAuthCheckTF($auth_level)
{
	global $member;

	if($member < $auth_level)
	{
		return false;
		exit;
	}
	return true;
}

function memberAuthCheck($auth_level)
{
	global $member;
	if($auth_level > 0)
	{
		if(isLogin()){
			if($member["level"] < $auth_level)
			{
				put_msg_and_back("접근권한이 없습니다.");
				exit;
			}
		}else{
			put_msg_and_back("로그인이 필요한 서비스입니다.");
			exit;
		}
	}
}

function memberAuthCheckTF($auth_level)
{
	global $member;

	if($member < $auth_level)
	{
		return false;
		exit;
	}
	return true;
}


function get_microtime()
{
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

// 파일명에서 특수문자 제거
function get_safe_filename($name)
{
	$pattern = '/["\'<>=#&!%\\\\(\)\*\+\?]/';
	$name = preg_replace($pattern, '', $name);

	return $name;
}

// 파일 확장자 체크
function ext_check($filename, $deny_ext)
{

	$ext = explode(".", strtolower($filename));
	$w_deny_ext = explode("|", strtolower($deny_ext));

	foreach($ext as $k => $v)
	{
		foreach($w_deny_ext as $kk => $vv)
		{
			if($v == $vv)
			{
				return $v;
				break;
			}
		}
	}

	return false;
}




function getBbsDefaultParameters()
{
	global $_GET, $_POST;

	$bbs_param = array("wb", "page", "searchVar", "searchWord");

	$returnVal = array();

	foreach($bbs_param as $k => $v)
	{
		if(in_array($v, $bbs_param))
		{
			$returnVal[] = $v."=".urlencode($_GET[$v]);
		}
	}
	return (count($returnVal)) ? join("&", $returnVal) : false;
}

function getMemberDefaultParameters()
{
	global $_GET, $_POST;

	$bbs_param = array("mt", "page", "searchVar", "searchWord");

	$returnVal = array();

	foreach($bbs_param as $k => $v)
	{
		if(in_array($v, $bbs_param))
		{
			$returnVal[] = $v."=".urlencode($_GET[$v]);
		}
	}
	return (count($returnVal)) ? join("&", $returnVal) : false;
}



function cut_str($str, $len, $suffix="…")
{
	$arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	$str_len = count($arr_str);

	if ($str_len >= $len) {
		$slice_str = array_slice($arr_str, 0, $len);
		$str = join("", $slice_str);

		return $str . ($str_len > $len ? $suffix : '');
	} else {
		$str = join("", $arr_str);
		return $str;
	}
}


// TEXT 형식으로 변환
function get_text($str, $html=0)
{
	/* 3.22 막음 (HTML 체크 줄바꿈시 출력 오류때문)
	$source[] = "/  /";
	$target[] = " &nbsp;";
	*/

	// 3.31
	// TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
	if ($html == 0) {
		$str = html_symbol($str);
	}

	$source[] = "/</";
	$target[] = "&lt;";
	$source[] = "/>/";
	$target[] = "&gt;";
	//$source[] = "/\"/";
	//$target[] = "&#034;";
	$source[] = "/\'/";
	$target[] = "&#039;";
	//$source[] = "/}/"; $target[] = "&#125;";
	if ($html) {
		$source[] = "/\n/";
		$target[] = "<br/>";
	}

	return preg_replace($source, $target, $str);
}


/*
// HTML 특수문자 변환 htmlspecialchars
function hsc($str)
{
    $trans = array("\"" => "&#034;", "'" => "&#039;", "<"=>"&#060;", ">"=>"&#062;");
    $str = strtr($str, $trans);
    return $str;
}
*/

// 3.31
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
	return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}

// 제목을 변환
function conv_subject($subject, $len, $suffix='')
{
	return get_text(cut_str($subject, $len, $suffix));
}

// 내용을 변환
function conv_content($content, $html, $filter=true)
{
	global $config, $board;

	if ($html == "Y")
	{
		$source = array();
		$target = array();

		$source[] = "//";
		$target[] = "";

		if ($html == 2) { // 자동 줄바꿈
			$source[] = "/\n/";
			$target[] = "<br/>";
		}

		// 테이블 태그의 개수를 세어 테이블이 깨지지 않도록 한다.
		$table_begin_count = substr_count(strtolower($content), "<table");
		$table_end_count = substr_count(strtolower($content), "</table");
		for ($i=$table_end_count; $i<$table_begin_count; $i++)
		{
			$content .= "</table>";
		}

		$content = preg_replace($source, $target, $content);

		if($filter)
			$content = html_purifier($content);
	}
	else // text 이면
	{
		// & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
		$content = html_symbol($content);

		// 공백 처리
		//$content = preg_replace("/  /", "&nbsp; ", $content);
		$content = str_replace("  ", "&nbsp; ", $content);
		$content = str_replace("\n ", "\n&nbsp;", $content);

		$content = get_text($content, 1);
		$content = url_auto_link($content);
	}

	return $content;
}

// http://htmlpurifier.org/
// Standards-Compliant HTML Filtering
// Safe  : HTML Purifier defeats XSS with an audited whitelist
// Clean : HTML Purifier ensures standards-compliant output
// Open  : HTML Purifier is open-source and highly customizable
function html_purifier($html)
{
	$f = file(DY_PLUGIN_PATH.'/htmlpurifier/safeiframe.txt');
	$domains = array();
	foreach($f as $domain){
		// 첫행이 # 이면 주석 처리
		if (!preg_match("/^#/", $domain)) {
			$domain = trim($domain);
			if ($domain)
				array_push($domains, $domain);
		}
	}
	// 내 도메인도 추가
	array_push($domains, $_SERVER['HTTP_HOST'].'/');
	$safeiframe = implode('|', $domains);

	include_once(DY_PLUGIN_PATH.'/htmlpurifier/HTMLPurifier.standalone.php');
	$config = HTMLPurifier_Config::createDefault();
	// data/cache 디렉토리에 CSS, HTML, URI 디렉토리 등을 만든다.
	$config->set('Cache.SerializerPath', DY_DATA_PATH.'/cache');
	$config->set('HTML.SafeEmbed', true);
	$config->set('HTML.SafeObject', true);
	$config->set('HTML.SafeIframe', true);
	$config->set('URI.SafeIframeRegexp','%^(https?:)?//('.$safeiframe.')%');
	$config->set('Attr.AllowedFrameTargets', array('_blank'));
	$purifier = new HTMLPurifier($config);
	return $purifier->purify($html);
}

// 게시판 첨부파일 썸네일 삭제
function delete_board_thumbnail($wb, $file)
{
	if(!$wb || !$file)
		return;

	$fn = preg_replace("/\.[^\.]+$/i", "", basename($file));
	$files = glob(DY_DATA_PATH.'/file/'.$wb.'/thumb-'.$fn.'*');
	if (is_array($files)) {
		foreach ($files as $filename)
			unlink($filename);
	}
}

// way.co.kr 의 wayboard 참고
function url_auto_link($str)
{
	global $g5;
	global $config;

	// 속도 향상 031011
	$str = preg_replace("/&lt;/", "\t_lt_\t", $str);
	$str = preg_replace("/&gt;/", "\t_gt_\t", $str);
	$str = preg_replace("/&amp;/", "&", $str);
	$str = preg_replace("/&quot;/", "\"", $str);
	$str = preg_replace("/&nbsp;/", "\t_nbsp_\t", $str);
	$str = preg_replace("/([^(http:\/\/)]|\(|^)(www\.[^[:space:]]+)/i", "\\1<A HREF=\"http://\\2\" TARGET='{$config['cf_link_target']}'>\\2</A>", $str);
	//$str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,]+)/i", "\\1<A HREF=\"\\2\" TARGET='$config['cf_link_target']'>\\2</A>", $str);
	// 100825 : () 추가
	// 120315 : CHARSET 에 따라 링크시 글자 잘림 현상이 있어 수정
	$str = preg_replace("/([^(HREF=\"?'?)|(SRC=\"?'?)]|\(|^)((http|https|ftp|telnet|news|mms):\/\/[a-zA-Z0-9\.-]+\.[가-힣\xA1-\xFEa-zA-Z0-9\.:&#=_\?\/~\+%@;\-\|\,\(\)]+)/i", "\\1<A HREF=\"\\2\" TARGET='{$config['cf_link_target']}'>\\2</A>", $str);

	// 이메일 정규표현식 수정 061004
	//$str = preg_replace("/(([a-z0-9_]|\-|\.)+@([^[:space:]]*)([[:alnum:]-]))/i", "<a href='mailto:\\1'>\\1</a>", $str);
	$str = preg_replace("/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i", "<a href='mailto:\\1'>\\1</a>", $str);
	$str = preg_replace("/\t_nbsp_\t/", "&nbsp;" , $str);
	$str = preg_replace("/\t_lt_\t/", "&lt;", $str);
	$str = preg_replace("/\t_gt_\t/", "&gt;", $str);

	return $str;
}


// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL, 추가파라미터
/*
<div class="paging">
	<ol>
		<li class="img"><a href=""><span class="pagell"><span>게시물 처음페이지</span></span></a></li>
		<li class="img"><a href=""><span class="pagel"><span>게시물 이전 페이지</span></span></a></li>
		<li><em title="현재페이지">1</em></li>
		<li><a href="#">2</a></li>
		<li><a href="#">3</a></li>
		<li><a href="#">4</a></li>
		<li><a href="#">5</a></li>
		<li><a href="#">6</a></li>
		<li><a href="#">7</a></li>
		<li><a href="#">8</a></li>
		<li><a href="#">9</a></li>
		<li><a href="#">10</a></li>
		<li class="img"><a href=""><span class="pagerr"><span>게시물 다음 페이지</span></span></a></li>
		<li class="img"><a href=""><span class="pager"><span>게시물 마지막 페이지</span></span></a></li>
	</ol>
</div>

<div id="page_num">
	<span class="box"><a href="#"><img src="../images/sub/pagenum_l1.jpg"></a></span>
	<span class="box"><a href="#"><img src="../images/sub/pagenum_l2.jpg"></a></span>
	<span class="num"><a href="#">1</a></span>
	<span class="num"><a href="#">2</a></span>
	<span class="num"><a href="#">3</a></span>
	<span class="num"><a href="#">4</a></span>
	<span class="num"><a href="#">5</a></span>
	<span class="num"><a href="#">6</a></span>
	<span class="num"><a href="#">7</a></span>
	<span class="num"><a href="#">8</a></span>
	<span class="num"><a href="#">9</a></span>
	<span class="box"><a href="#"><img src="../images/sub/pagenum_r1.jpg"></a></span>
	<span class="box"><a href="#"><img src="../images/sub/pagenum_r2.jpg"></a></span>
</div>
*/
function get_paging($write_pages, $cur_page, $total_page, $url, $add="")
{
	$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);

	$str = '';

	$start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
	$end_page = $start_page + $write_pages - 1;

	if ($end_page >= $total_page) $end_page = $total_page;

	if ($start_page > 1) $str .= '<li class="img"><a href="'.$url.($start_page-1).$add.'"><span class="pagell"><span>게시물 이전 페이지</span></span></a></li>'.PHP_EOL;

	if ($cur_page > 1) {
		$str .= '<li class="img"><a href="'.$url.'1'.$add.'"><span class="pagel"><span>게시물 처음페이지</span></span></a></li>'.PHP_EOL;
	}

	if ($total_page > 1) {
		for ($k=$start_page;$k<=$end_page;$k++) {
			if ($cur_page != $k)
				$str .= '<li><a href="'.$url.$k.$add.'">'.$k.'</a></li>'.PHP_EOL;
			else
				$str .= '<li><a href="'.$url.$k.$add.'"><em title="현재페이지">'.$k.'</em></a></li>'.PHP_EOL;
			//$str .= '<span><em title="현재페이지">'.$k.'</em></span>'.PHP_EOL;
		}
	}

	if ($total_page > $end_page) $str .= '<li class="img"><a href="'.$url.($end_page+1).$add.'"><span class="pagerr"><span>게시물 다음 페이지</span></span></a></li>'.PHP_EOL;


	if ($cur_page < $total_page) {
		$str .= '<li class="img"><a href="'.$url.$total_page.$add.'"><span class="pager"><span>게시물 마지막 페이지</span></span></a></li>'.PHP_EOL;
	}

	if ($str)
		return "<div class=\"paging\"><ol>{$str}</ol></div>";
	else
		return "";
}

function get_paging2($write_pages, $cur_page, $total_page, $url, $add="")
{
	$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);

	$str = '';
	if ($cur_page > 1) {
		$str .= '<span class="box"><a href="'.$url.'1'.$add.'"><img src="/images/sub/pagenum_l1.jpg"></a></span>'.PHP_EOL;
	}

	$start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
	$end_page = $start_page + $write_pages - 1;

	if ($end_page >= $total_page) $end_page = $total_page;

	if ($start_page > 1) $str .= '<span class="box"><a href="'.$url.($start_page-1).$add.'"><img src="/images/sub/pagenum_l2.jpg"></a></span>'.PHP_EOL;

	if ($total_page > 1) {
		for ($k=$start_page;$k<=$end_page;$k++) {
			if ($cur_page != $k)
				$str .= '<span class="num"><a href="'.$url.$k.$add.'">'.$k.'</a></span>'.PHP_EOL;
			else
				$str .= '<span class="num"><a href="'.$url.$k.$add.'"><strong>'.$k.'</strong></a></span>'.PHP_EOL;
			//$str .= '<span><em title="현재페이지">'.$k.'</em></span>'.PHP_EOL;
		}
	}

	if ($total_page > $end_page) $str .= '<span class="box"><a href="'.$url.($end_page+1).$add.'"><img src="/images/sub/pagenum_r1.jpg"></a></span>'.PHP_EOL;


	if ($cur_page < $total_page) {
		$str .= '<span class="box"><a href="'.$url.$total_page.$add.'"><img src="/images/sub/pagenum_r2.jpg"></a></span>'.PHP_EOL;
	}

	if ($str)
		return "<div class=\"paging\"><ol>{$str}</ol></div>";
	else
		return "";
}



// 에디터 이미지 얻기
function get_editor_image($contents, $view=true)
{
	if(!$contents)
		return false;

	// $contents 중 img 태그 추출
	if ($view)
		$pattern = "/<img([^>]*)>/iS";
	else
		$pattern = "/<img[^>]*src=[\'\"]?([^>\'\"]+[^>\'\"]+)[\'\"]?[^>]*>/";
	preg_match_all($pattern, $contents, $matchs);

	return $matchs;
}



function real_age_cal($fullbirth)
{
	$birthday = date("Ymd", strtotime($fullbirth)); //생년월일
	$nowday =  date('Ymd'); //현재날짜
	return floor(($nowday - $birthday) / 10000); //만나이
}

function get_fullbirth_by_ssn($ssn)
{
	$jumin1 = substr($ssn, 0, 6);
	$jumin2 = substr($ssn, 6, 7);

	//생년구하기
	$k = substr($jumin2, 0, 1);

	if($k == "1" || $k == "2" || $k == "5" || $k == "6"){
		$y = "19".substr($jumin1, 0, 2);
	}else if($k == "3" || $k == "4" || $k == "7" || $k == "8"){
		$y = "20".substr($jumin1, 0, 2);
	}

	//생월 구하기
	$m = substr($jumin1, 2, 2);

	//생일 구하기
	$d = substr($jumin1, 4, 2);

	return $y.$m.$d;
}

function ssn_add_hipen($ssn)
{
	$pattern = '/([\d]{6})([\d]{7})/';
	return preg_replace($pattern, '$1-$2', $ssn);
}

// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
	setcookie(md5($cookie_name), base64_encode($value), DY_SERVER_TIME + $expire, '/', DY_COOKIE_DOMAIN);
}


// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
	$cookie = md5($cookie_name);
	if (array_key_exists($cookie, $_COOKIE))
		return base64_decode($_COOKIE[md5($cookie_name)]);
	else
		return "";
}

function addhttp($url) {
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
	return $url;
}

//전화번호 자동 하이픈
function add_hyphen($tel)
{
	$tel = preg_replace("/[^0-9]/", "", $tel);    // 숫자 이외 제거
	if (substr($tel,0,2)=='02')
		return preg_replace("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
	else if (strlen($tel)=='8' && (substr($tel,0,2)=='15' || substr($tel,0,2)=='16' || substr($tel,0,2)=='18'))
		// 지능망 번호이면
		return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel);
	else if (strlen($tel) == 8)
		// 그냥 8자리일 경우
		return preg_replace("/([0-9]{4})([0-9]{4})$/", "\\1-\\2", $tel);
	else if (strlen($tel) == 7)
		// 그냥 7자리일 경우
		return preg_replace("/([0-9]{3})([0-9]{4})$/", "\\1-\\2", $tel);
	else
		return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $tel);
}

//MSSQL 에서 넘어 오는 datetime string 을 변환 하여 반환
function mssqlDateTimeStringConvert($val, $convert_type = 1)
{
	$dt = strtotime($val);
	$convert = "Y-m-d H:i:s";
	switch ($convert_type)
	{
		case 1 :
			$convert = "Y-m-d H:i:s";
			break;
	}

	return date($convert, $dt);
}

//숫자를 알파벳으로 0~부터 A~
function getNameFromNumber($num) {
	$numeric = $num % 26;
	$letter = chr(65 + $numeric);
	$num2 = intval($num / 26);
	if ($num2 > 0) {
		return getNameFromNumber($num2 - 1) . $letter;
	} else {
		return $letter;
	}
}

function generate_mail_id($uid) {
	$id = date("YmdHis",time());
	mt_srand((float) microtime() * 1000000);
	$randval = mt_rand();
	$id .= $randval."@$uid";
	return $id;
}

/**
 * 메일 보내기
 * @param $fname : From Name
 * @param $fmail : From Email
 * @param $to : To Email
 * @param $subject : Subject
 * @param $content : Content
 * @param int $type : text=0, html=1, text+html=2
 * @param string $file : file array
 * @param string $cc : 참조
 * @param string $bcc : 숨은참조
 * @return bool|void
 */
function mailer2($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
{
	$rst = false;

    $boundary = uniqid(time());

    $header = "Message-ID: <".generate_mail_id(preg_replace("/@.+$/i","",$to)).">\r\n".
              "From:=?utf-8?B?".base64_encode($fname)."?=<$fmail>\r\n";
    if ($cc)  $header .= "Cc: $cc\n";
    if ($bcc) $header .= "Bcc: $bcc\n";
    $header .= "MIME-Version: 1.0\n";
    $header .= "X-Mailer: DYFLUX Mailer : {$_SERVER['SERVER_ADDR']} : {$_SERVER['REMOTE_ADDR']} : ".DY_URL." : {$_SERVER['PHP_SELF']} : {$_SERVER['HTTP_REFERER']} \n";
    $header .= "Date: ".date ("D, j M Y H:i:s T",time())."\r\n";
               //"To: $to\r\n".
               //"Subject: =?utf-8?B?".base64_encode($subject)."?=\r\n";

    if ($file == "") {
        $header .= "Content-Type: MULTIPART/ALTERNATIVE;\n".
                   "              BOUNDARY=\"$boundary\"\n\n";
    } else {
        $header .= "Content-Type: MULTIPART/MIXED;\n".
                   "              BOUNDARY=\"$boundary\"\n\n";
    }

    if ($type == 2)
        $content = nl2br($content);

    $strip_content  = stripslashes(trim($content));
    $encode_content = chunk_split(base64_encode($strip_content));

    $body = "";
    $body .= "\n--$boundary\n";
    $body .= "Content-Type: TEXT/PLAIN; charset=utf-8\n";
    $body .= "Content-Transfer-Encoding: BASE64\r\n\r\n";
    $body .= $encode_content;
    $body .= "\n--$boundary\n";

    if ($type) {
        $body .= "Content-Type: TEXT/HTML; charset=utf-8\n";
        $body .= "Content-Transfer-Encoding: BASE64\r\n\r\n";
        $body .= $encode_content;
        $body .= "\n--$boundary\n";
    }

    if ($file != "") {
        foreach ($file as $f) {
            $body .= "\n--$boundary\n";
            $body .= "Content-Type: APPLICATION/OCTET-STREAM; name=$fname\n";
            $body .= "Content-Transfer-Encoding: BASE64\n";
            $body .= "Content-Disposition: inline; filename=$fname\n";

            $body .= "\n";
            $body .= chunk_split(base64_encode($f['data']));
            $body .= "\n";
        }
        $body .= "--$boundary\n";
    }

	$body .= "\n--$boundary--\n";

    $mails['to'] = $to;
    $mails['from'] = $fmail;
    $mails['text'] = $header.$body;

    $rst = @mail($to, $subject, $body, $header, "-f $fmail");

    return $rst;
}

/**
 * 메일 보내기 - phpmailer 사용
 * @param $fname : From Name
 * @param $fmail : From Email
 * @param $to : To Email
 * @param $subject : Subject
 * @param $content : Content
 * @param int $type : text=0, html=1, text+html=2
 * @param string $file : file array
 * @param string $cc : 참조
 * @param string $bcc : 숨은참조
 * @return bool|void
 */
function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
{
	$rst = false;

	$mail = new \PHPMailer\PHPMailer\PHPMailer(true);

	try {
		//Server settings
		$mail->SMTPDebug = 0;                                // Enable verbose debug output
		$mail->isSMTP();                                     // Set mailer to use SMTP
		$mail->Host       = DY_ADMIN_MAIL_SENDER_SMTP_HOST;  // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                            // Enable SMTP authentication
		$mail->Username   = DY_ADMIN_MAIL_SENDER_SMTP_ID;    // SMTP username
		$mail->Password   = DY_ADMIN_MAIL_SENDER_SMTP_PW;    // SMTP password
		$mail->SMTPSecure = 'tls';                           // Enable TLS encryption, `ssl` also accepted
		$mail->Port       = DY_ADMIN_MAIL_SENDER_SMTP_PORT;  // TCP port to connect to

		$mail->CharSet = "UTF-8";
		$mail->Encoding = "base64";

		//Recipients
		$mail->setFrom($fmail, $fname);

		if(strpos($to, ",") !== false){
			$tmp_to_ary = explode(",", $to);
			foreach($tmp_to_ary as $tt){
				$mail->addAddress($tt);               // Name is optional
			}

		} elseif(strpos($to, ";") !== false){
			$tmp_to_ary = explode(";", $to);
			foreach($tmp_to_ary as $tt){
				$mail->addAddress($tt);               // Name is optional
			}
		} else {
			$mail->addAddress($to);               // Name is optional
		}

		//$mail->addReplyTo('info@example.com', 'Information');
		//$mail->addCC('cc@example.com');
		//$mail->addBCC('bcc@example.com');

		// Attachments
		//$mail->addAttachment('/var/tmp/file.tar.gz');       // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');  // Optional name

		if ($type == 2)
			$content = nl2br($content);

		// Content
		$mail->isHTML(true);                         // Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body    = $content;
		$mail->AltBody = $content;

		$mail->send();
		$rst = true;
	} catch (Exception $e) {

		//로그 포맷
		$loggerFormat = "%datetime% [[^^]] %level_name% [[^^]] %message% [[^^]] %context% [[^^]] %extra%\n";
		$loggerTimeFormat = "Y-m-d H:i:s";
		$formatter = new \Monolog\Formatter\LineFormatter($loggerFormat, $loggerTimeFormat);

		// 로거 채널 생성
		$log = new \Monolog\Logger('Error.Log');

		// 경로
		$log_path = DY_LOG_PATH;
		//error_mail(format_error( $errno, $errstr, $errfile, $errline));
		// log/your.log 파일에 로그 생성. 로그 레벨은 Info
		$loggerHandler = new \Monolog\Handler\StreamHandler($log_path . "/PHP_" . date('Y-m-d') . ".log", \Monolog\Logger::ERROR);
		$loggerHandler->setFormatter($formatter);
		$log->pushHandler($loggerHandler);

		$error_ary = array();
		$error_ary["no"] = 0;
		$error_ary["str"] = $mail->ErrorInfo;
		$error_ary["file"] = $_SERVER["SCRIPT_FILENAME"];
		$error_ary["line"] = 0;
		$error_ary["querystring"] = $_SERVER["QUERY_STRING"];
		$error_ary["postdata"] = $_POST;

		$log->addError($mail->ErrorInfo, $error_ary);

		$rst = false;
	}

	return $rst;
}

/**
 * 날짜 형식 유효성 체크
 * @param $date : 날짜 string
 * @param string $format : 체크할 날짜 형식 (기본형식 Y-m-d H:i:s - 2000-01-00 23:59:59)
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d H:i:s')
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

/**
 * 숫자 형식 체크 0-9 만 허용
 * @param $val
 * @return false|int
 */
function validateNumber($val)
{
	return preg_match('/^[0-9]*$/', $val);
}

function excelColumnRange($lower, $upper) {
	++$upper;
	for ($i = $lower; $i !== $upper; ++$i) {
		yield $i;
	}
}

function monthsBetween($startDate, $endDate) {
	$retval = "";

	// Assume YYYY-mm-dd - as is common MYSQL format
	$splitStart = explode('-', $startDate);
	$splitEnd = explode('-', $endDate);

	if (is_array($splitStart) && is_array($splitEnd)) {
		$difYears = $splitEnd[0] - $splitStart[0];
		$difMonths = $splitEnd[1] - $splitStart[1];
		$difDays = $splitEnd[2] - $splitStart[2];

		$retval = ($difDays > 0) ? $difMonths : $difMonths - 1;
		$retval += $difYears * 12;
	}
	return $retval;
}

function mytory_asterisk($string) {
	$string = trim($string);
	$length = mb_strlen($string, 'utf-8');
	$string_changed = $string;
	if ($length <= 2) {
		// 한두 글자면 그냥 뒤에 별표 붙여서 내보낸다.
		$string_changed = mb_substr($string, 0, 1, 'utf-8') . '*';
	}
	if ($length >= 3) {
		// 3으로 나눠서 앞뒤.
		$leave_length = floor($length/3); // 남겨 둘 길이. 반올림하니 너무 많이 남기게 돼, 내림으로 해서 남기는 걸 줄였다.
		$asterisk_length = $length - ($leave_length * 2);
		$offset = $leave_length + $asterisk_length;
		$head = mb_substr($string, 0, $leave_length, 'utf-8');
		$tail = mb_substr($string, $offset, $leave_length, 'utf-8');
		$string_changed = $head . implode('', array_fill(0, $asterisk_length, '*')) . $tail;
	}
	return $string_changed;
}

function address_asterisk($addr){
	$addr = trim($addr);
	$len = mb_strlen($addr, 'utf-8');
	$addr_change = $addr;
	$addr_change_ary = array();

	$addr_ary = explode(" ", $addr);

	foreach ($addr_ary as $key => $ar){
		if($key > 1){
			$ar = str_repeat("*", mb_strlen($ar, 'utf-8'));
		}

		$addr_change_ary[] = $ar;
	}

	$addr_change = implode(" ", $addr_change_ary);

	return $addr_change;
}

function phone_asterisk($phone){
	$phone = trim($phone);
	//$phone_change = $phone;
	//$phone_remove_h = str_replace("-", "", $phone);

	return preg_replace('/-(\d+)-/e','-'.str_repeat('*',strlen('\\1')).'-', $phone);
}


function array_asterisk($matches)
{
	$return = "";
	foreach($matches as $k => $v){
		if($k > 0) {
			if ($k > 1) $return .= "-";
			if ($k == 2) {
				$return .= str_repeat("*", strlen($v));
			} else {
				$return .= $v;
			}
		}
	}

	return $return;
}

//전화번호 자동 하이픈
function add_hyphen_and_asterisk($tel)
{
	$tel = preg_replace("/[^0-9]/", "", $tel);    // 숫자 이외 제거
	if (substr($tel,0,2)=='02')
		return preg_replace_callback("/([0-9]{2})([0-9]{3,4})([0-9]{4})$/", array_asterisk, $tel);
	else if (strlen($tel)=='8' && (substr($tel,0,2)=='15' || substr($tel,0,2)=='16' || substr($tel,0,2)=='18'))
		// 지능망 번호이면
		return preg_replace_callback("/([0-9]{4})([0-9]{4})$/", array_asterisk, $tel);
	else if (strlen($tel) == 8)
		// 그냥 8자리일 경우
		return preg_replace_callback("/([0-9]{4})([0-9]{4})$/", array_asterisk, $tel);
	else if (strlen($tel) == 7)
		// 그냥 7자리일 경우
		return preg_replace_callback("/([0-9]{3})([0-9]{4})$/", array_asterisk, $tel);
	else
		return preg_replace_callback("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "array_asterisk", $tel);
}

//토큰 생성
function generateToken($length = 20)
{
	return bin2hex(random_bytes($length));
}

/**
 * 엔터값[PHP_EOL] 빈칸으로 치환
 * phpspreadsheet 를 통해 엑셀을 읽어들일 경우 엔터값을 그대로 가져옴
 * 현재 함수를 array_walk_recursive 를 이용하여 엔터값을 빈칸으로 바꿈
 * @param $item
 * @param $key
 */
function removeXlsLinebreak(& $item, $key)
{
	$item = str_replace(PHP_EOL, " ", $item);
}
?>
