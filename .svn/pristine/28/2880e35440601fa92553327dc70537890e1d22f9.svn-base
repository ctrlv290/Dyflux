<?php

$str = '은행 : 국민은행
계좌번호 : 194601-04-191376
예금주 : (주)덕윤
금액 : 90,000
안녕하세요 저는';
$str = str_replace("\r\n", "\n", $str);

echo strlen($str);
echo "<Br>";
echo mb_strlen("UTF-8", $str);
echo "<Br>";
echo mb_strlen($str, "UTF-8");
echo "<Br>";
echo mb_strlen($str, "EUC-KR");
echo "<Br>";
echo mb_strlen($str, "CP949");
echo "<Br>";
echo mb_strwidth($str, "UTF-8");
echo "<Br>";
echo mb_strwidth($str, "EUC-KR");

exit;
include_once "_init_.php";

function cmp($a, $b)
{
	return strcmp($a["email"], $b["email"]);
}

$array = array(

	array(
		"name" => "강신욱",
		"email" => "styler@naver.com"
	),

	array(
		"name" => "정병영",
		"email" => "abcd@naver.com"
	),

	array(
		"name" => "김선혜",
		"email" => "zzz@naver.com"
	),

);

usort($array, "cmp");

while (list($key, $value) = each($array)) {
	print_r2($value);
}

exit;


require 'vendor/autoload.php';

$mail = new \PHPMailer\PHPMailer\PHPMailer();

try {
	//Server settings
	$mail->SMTPDebug = 0;                                       // Enable verbose debug output
	$mail->isSMTP();                                            // Set mailer to use SMTP
	$mail->Host       = DY_ADMIN_MAIL_SENDER_SMTP_HOST;  // Specify main and backup SMTP servers
	$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
	$mail->Username   = DY_ADMIN_MAIL_SENDER_SMTP_ID;                     // SMTP username
	$mail->Password   = DY_ADMIN_MAIL_SENDER_SMTP_PW;                               // SMTP password
	$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
	$mail->Port       = DY_ADMIN_MAIL_SENDER_SMTP_PORT;                                    // TCP port to connect to

	//Recipients
	$mail->setFrom(DY_ADMIN_MAIL_SENDER_EMAIL, 'Mailer');
	$mail->addAddress('styler@naver.com');               // Name is optional
//	$mail->addReplyTo('info@example.com', 'Information');
//	$mail->addCC('cc@example.com');
//	$mail->addBCC('bcc@example.com');

	// Attachments
	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	// Content
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Subject = 'Here is the subject';
	$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	$mail->send();
	echo 'Message has been sent';
} catch (\PHPMailer\PHPMailer\Exception $e) {

} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

exit;
$login_id = "1004";
var_dump(preg_match("/[^a-zA-Z0-9\-\_]/", $login_id));
exit;

print_r2(get_defined_constants(true));
exit;
//phpinfo();

$a = "TEST1가2223";



var_dump(preg_match("/[^a-zA-Z1-9.\-\_]/", $a));

exit;

$cs_file_array = array("1");
list($cs_file1, $cs_file2, $cs_file3, $cs_file4, $cs_file5) = $cs_file_array;

if(!$cs_file1) $cs_file1 = 0;
if(!$cs_file2) $cs_file2 = 0;
$cs_file3 = $cs_file3 || 0;
$cs_file4 = $cs_file4 || 0;
$cs_file5 = $cs_file5 || 0;

var_dump($cs_file1);
var_dump($cs_file2);
var_dump($cs_file3);
var_dump($cs_file4);
var_dump($cs_file5);

$dbuser="tomskevin";
$dbpass="tomskevindev$#!1";

$dbsid = "(
  DESCRIPTION =
  (ADDRESS_LIST = 
   (ADDRESS = 
    (PROTOCOL = TCP)
    (HOST = 203.248.116.111)
    (PORT = 1521)
   )
  )
  
  (CONNECT_DATA =
   (SERVER = DEDICATED)
   (SERVICE_NAME = CGISDEV)
  )
) ";

$conn = @oci_connect($dbuser,$dbpass,$dbsid);


if(!$conn) {

	echo "No Connection";

	exit;

} else {

	echo "Connect Success!";

	oci_close($conn);

}
exit;

//
//$val = "1/1/2018";
//
//var_dump(date('Y-m-d', strtotime($val)));
//
//if($timestamp = strtotime($val) !== false) {
//	echo date('Y-m-d', strtotime($val));
//}else{
//	echo "err";
//}


$qry = "WITH cte(product_idx, product_option_idx, product_nm, product_option_nm, stock)
AS
(
	Select 
		P.product_idx, PO.product_option_idx, P.product_name, PO.product_option_name
		, SUM(stock_amount * stock_type) as stock
	From
		DY_PRODUCT P
		Inner Join DY_PRODUCT_OPTION PO On P.product_idx = PO.product_idx
		Inner Join DY_STOCK S On PO.product_option_idx = S.product_option_idx
		Group by P.product_idx, PO.product_option_idx, P.product_name, PO.product_option_name
)
Select * From cte Order by stock DESC";


$qry = "
	IF EXISTS (
				Select * From DY_PRODUCT
	)
		BEGIN
			SELECT 'OVERLAP' -- 이미 입력된 정보가 있을 경우
		END
	ELSE
		BEGIN
			SELECT 'NONE'
		END

";

$C_Dbconn = new Dbconn();
$C_Dbconn->db_connect();
$_list = $C_Dbconn -> execSqlList($qry);
$C_Dbconn->db_close();

print_r2($_list);

echo date("Y-m-d H:i:s") . "<br>";
$current_minute = Floor(date("i") / 10);
echo $current_minute
?>