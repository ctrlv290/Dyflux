<?php

$_this_forder = explode('/', str_replace('\\', '/', dirname(__FILE__)));
array_pop($_this_forder);
$inc_folder_path = implode('/', $_this_forder) . "/_INCLUDE/";
$config_file = $inc_folder_path . 'config.inc';
$function_file = $inc_folder_path . 'func.inc';

if (file_exists($config_file)) {
    include_once($config_file);
    include_once($function_file);
}

require 'vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

register_shutdown_function("fatal_handler");

function fatal_handler()
{

    global $_POST;

    $errfile = "unknown file";
    $errstr = "shutdown";
    $errno = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if ($error !== NULL) {
        $errno = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr = $error["message"];

        if ($errno == 1 || $errno == 16 || $errno == 64) {

            //로그 포맷
            $loggerFormat = "%datetime% [[^^]] %level_name% [[^^]] %message% [[^^]] %context% [[^^]] %extra%\n";
            $loggerTimeFormat = "Y-m-d H:i:s";
            $formatter = new LineFormatter($loggerFormat, $loggerTimeFormat);

            // 로거 채널 생성
            $log = new Logger('Error.Log');

            // 경로
            $log_path = DY_LOG_PATH;
            //error_mail(format_error( $errno, $errstr, $errfile, $errline));
            // log/your.log 파일에 로그 생성. 로그 레벨은 Info
            $loggerHandler = new StreamHandler($log_path . "/PHP_" . date('Y-m-d') . ".log", Logger::ERROR);
            $loggerHandler->setFormatter($formatter);
            $log->pushHandler($loggerHandler);

            $error_ary = array();
            $error_ary["no"] = $errno;
            $error_ary["str"] = $errstr;
            $error_ary["file"] = $errfile;
            $error_ary["line"] = $errline;
            $error_ary["querystring"] = $_SERVER["QUERY_STRING"];
            $error_ary["postdata"] = $_POST;

            $log->addError($errstr, $error_ary);

            //$log->addError('=================================================');
            //$log->addError('Error No : ' . $errno);
            //$log->addError('Error Str : ' . $errstr);
            //$log->addError('Error File : ' . $errfile);
            //$log->addError('Error Line : ' . $errline);
            //$log->addError('=================================================');
        }
    }
}

function classAutoloader($path)
{
    //$path = str_replace("DYFLUX\\", DY_CLASS_PATH . "/", $path);
    $path = DY_CLASS_PATH . "/" . $path;
    $path = $path . '.php';
//	var_dump($path);
    require_once $path;
}

spl_autoload_register('classAutoloader');

$bbs = new BBS();
$noticeList = $bbs->getMainNoticeList();

$banner = new Banner();
$bannerList = $banner->getUseBanner("MAIN");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DYFLUX</title>
    <meta property="og:title" content="DYFLUX"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="<?=DY_URL?>/"/>
    <meta property="og:image" content="<?=DY_URL?>/images/og_meta.png"/>
    <meta property="og:description" content="DYFLUX"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">

    <link rel="stylesheet" type="text/css" href="css/main_reset.css"/>
    <link rel="stylesheet" type="text/css" href="css/main_fonts.css"/>
    <link rel="stylesheet" type="text/css" href="css/slick.css"/>
    <link rel="stylesheet" type="text/css" href="css/slick-theme.css"/>
    <link rel="stylesheet" type="text/css" href="css/main.css"/>

    <script type="text/javascript" src="/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="/js/slick.min.js"></script>
    <style>
        /*iframe {border: none;overflow-y: scroll;}*/
        /*.slick-next {right: 15px !important;z-index: 99;}*/
        /*.slick-prev {left: 15px !important;z-index: 99;}*/
        .slick-prev:before, .slick-next:before {color: #c4c4c4 !important;}
    </style>
    <script>
        var returnUrl = "<?=$_GET["return_url"]?>";
    </script>
</head>
<body class="<?=(IS_DEV_SITE) ? "dev": "" ?>">
<!--
<div class="show" style="">
	<p>팝업이에용</p>
	<iframe src="notice_list.html"></iframe>
</div>-->

<div class="wrap">
    <div class="wrap_header">
        <div class="wrap_in">
            <div class="in">
                <a href="javascript:;" class="logo"><img src="images/logo.png" alt="dy flux" /></a>
            </div>
        </div>
    </div>

    <div class="wrap_content">
        <div class="wrap_in">
            <div class="in">

                <div class="visual_slide">
                    <?php
                    foreach($bannerList as $bn) {
                        $img = '<img src="'.DY_BANNER_FILE_URL . "/" . $bn["banner_image"].'" />';
                        if($bn["banner_click_url"]){
                            $img = '<a href="'.$bn["banner_click_url"].'" target="'.$bn["banner_click_target"].'">'.$img.'</a>';
                        }
                        ?>
                        <div class="slide">
                            <?=$img?>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="contents">
                    <div class="cont_set cont_set01">
                        <form name="login_form" class="login_form">
                            <div class="login_set">
                                <input type="text" class="id" id="member_id" name="member_id" title="아이디" placeholder="아이디" />
                                <input type="password" class="password enterDoSearch" id="member_pw" name="member_pw" title="비밀번호" placeholder="비밀번호" />
                            </div>
                            <div class="id_chk">
                                <label for="save_id">
                                    <input type="checkbox" id="save_id" name="save_id" value="Y" />
                                    <span>로그인 상태 유지</span>
                                </label>
                            </div>
                            <button type="button" class="btn-login"><img src="images/login_bt.png" alt="dy flux 로그인" /></button>
                            <a href="join.php" class="apply_btn">협력사 신청</a>
                        </form>
                    </div><!-- cont_set01 -->
                    <div class="cont_set cont_set02">
                        <div class="notice_set">
                            <dl class="title_set">
                                <dt>공지사항</dt>
                                <dd><a href="javascript:;" class="btn-notice-more">더보기</a></dd>
                            </dl>
                            <ul class="list_set">
                                <?php
                                foreach($noticeList as $n){
                                    $is_new = '';
                                    $dt = new DateTime();
                                    $dt->setTimestamp(strtotime($n["bbs_regdate"]));
                                    $now = new DateTime();

                                    if($now->diff($dt)->days < 8){
                                        $is_new = '<span class="new"></span>';
                                    }

                                    ?>
                                    <li><a href="javascript:;" class="btn-notice-view" data-idx="<?=$n["bbs_idx"]?>" title="<?=$n["bbs_title"]?>"><?=$n["bbs_title"]?></a><?=$is_new?></li>
                                <?php }?>
                            </ul>
                            <div class="btn_set">
                                <a href="http://www.duckyun.com/" class="company" target="_blank">회사소개<span></span></a>
                                <a href="https://www.instagram.com/water_run__/" class="blog" target="_blank">인스타그램<span></span></a>
                            </div>
                        </div>
                    </div><!-- cont_set02 -->
                    <div class="cont_set cont_set03">
                        <dl class="center_set">
                            <dt>고객센터</dt>
                            <dd>031-811-5500</dd>
                        </dl>
                        <div class="notice_set">
                            <dl class="time">
                                <dt><img src="images/ico_banner01.png" alt="" />업무시간</dt>
                                <dd>
                                    <p>평일 09시 00분 ~ 18시 00분</p>
                                    <p>(토,일/공휴일 휴무)</p>
                                    <p>(점심시간 :11:30~12:30)</p>
                                </dd>
                            </dl>
                            <dl class="account">
                                <dt><img src="images/ico_banner02.png" alt="" />계좌번호</dt>
                                <dd>
                                    <p>국민은행 194601-04-191347 </p>
                                    <p>예금주 : ㈜덕윤</p>
                                </dd>
                            </dl>
                        </div>
                    </div><!-- cont_set03 -->
                </div><!-- contents -->

            </div><!-- in -->
        </div><!-- wrap_in -->
    </div><!-- wrap_content -->

    <div class="wrap_footer">
        <div>
            <p><strong>(주)덕윤</strong><span>경기도 고양시 일산동구 정발산로24 웨스턴타워 4차 416-417 호</span></p>
        </div>
        <div>
            <p><strong>대표</strong><span>곽동호</span></p>
            <p><strong>개인정보 책임자</strong><span>오규화(rlikesky@duckyun.com)</span></p>
        </div>
        <div>
            <p><strong>사업자등록번호</strong><span>128-87-12256</span></p>
            <p><strong>통신판매업신고</strong><span>2014-고양일산동-0770</span></p>
        </div>
    </div><!-- wrap_footer -->
</div>


<div class="notice_wrap">
    <div class="notice_list">
        <div class="notice_list_in">
            <div class="title_set">
                <p class="title">공지사항</p>
                <button type="button" class="btn-notice-close"><img src="images/close.png" alt="" /></button>
            </div>

            <iframe src="about:_blank" class="notice_iframe"></iframe>
        </div>
    </div>
</div>
<script src="/js/page/main.js?v=<?=time()?>"></script>
</body>
</html>
