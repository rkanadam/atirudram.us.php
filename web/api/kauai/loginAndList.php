<?php

require_once "../common.php";
require_once "$base/api/Spreadsheet.php";

$method = strtolower($_SERVER["REQUEST_METHOD"]);
if ($method !== "post") {
    //return;
}
$email = $_REQUEST["email"];
$phone = $_REQUEST["password"];
header("Access-Control-Allow-Origin: *");
$sheet = new Spreadsheet("1n7UYI3LnoewmzalMiMwr98CjTTXskb_cJtF2Kh3hA8s", "Form Responses 1");
$entry = $sheet->getListFeed(array("sq" => "e-mailaddress = \"$email\" and phonenumber = \"$phone\""));
if (sizeof($entry)) {
    echo json_encode($sheet->getListFeed());
} else {

}
