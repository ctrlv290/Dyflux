<?php
include_once "./_init_.php";

//자동 로그인 쿠키 삭제
set_cookie("DY_TOKEN", null, -1);

unset($_SESSION["dy_member"]);
session_destroy();

go_replace("/");
?>