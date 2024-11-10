<?php

include_once "../_init_.php";
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control:no-cache");
header("Pragma:no-cache");

$crm = new CustomReportManager();

$data = $_POST;
