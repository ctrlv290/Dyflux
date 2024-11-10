<?php

$data_url = "<?xml version='1.0' encoding='UTF-8'?>";
$data_url .= "<data>";
$data_url .= "<statusData idx='1'>";
$data_url .= "<orderNo>1907111126123985</orderNo>";
$data_url .= "<sno>948</sno>";
$data_url .= "<orderStatus>g1</orderStatus>";
$data_url .= "</statusData>";
$data_url .= "</data>";

header("Content-type: text/xml");
echo $data_url;