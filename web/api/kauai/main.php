<?php

require_once "../common.php";
require_once "$base/api/Spreadsheet.php";

$email = $_REQUEST["email"];
$phone = $_REQUEST["password"];
$method = strtolower($_SERVER["REQUEST_METHOD"]);
$action = strtolower($_REQUEST["action"]);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($method === "options") {
    //This is usually a preflight. No processing required
    return;
}

if ($method !== "post") {
    //Handle only post content
    return;
}


function login()
{
    global $email, $phone;
    $responsesSheet = new Spreadsheet("1n7UYI3LnoewmzalMiMwr98CjTTXskb_cJtF2Kh3hA8s", "Form Responses 1");
    $entry = $responsesSheet->getListFeed(array("sq" => "e-mailaddress = \"$email\" and phonenumber = \"$phone\""));
    return sizeof($entry) > 0 ? true : false;
}

$return = false;
if (login()) {
    $accommodationSheet = new Spreadsheet("1n7UYI3LnoewmzalMiMwr98CjTTXskb_cJtF2Kh3hA8s", "Accomodation");
    if ($action === "login") {
        //This is listing for entries
        $entries = $accommodationSheet->getListFeed(array("sq" => "primary = \"$email\""));
        if (empty($entries)) {
            //In the accommodation sheet these entries are not there, must be a new registration
            //insert them into the sheet before returning to the client
            $entries = array();
            $primary = array("firstname" => $entry["firstname"], "lastname" => $entry["lastname"], "gender" => $entry["gender"], "primary" => strtolower($entry["e-mailaddress"]));
            $sheet->insertIntoListFeed($primary);
            $entries[] = $primary;
            if (strtolower($entry["areyoumarried"]) === "yes") {
                $spouse = array("firstname" => $entry["firstnameofthespouse"], "lastname" => $entry["lastnameofthespouse"], "gender" => $entry["genderofthespouse"], "primary" => $primary["primary"]);
                $sheet->insertIntoListFeed($spouse);
                $entries[] = $spouse;
            }
        }
        $return = $entries;
    } else if ($action === "put") {
        $accommodationSheet = new Spreadsheet("1n7UYI3LnoewmzalMiMwr98CjTTXskb_cJtF2Kh3hA8s", "Accomodation");
        $entries = json_decode($_REQUEST["entries"]);
        if ($entries && !empty($entries)) {
            $accommodationSheet->removeFromListFeed(array("sq" => "primary = \"$email\""));
            foreach ($entries as $entry) {
                $accommodationSheet->insertIntoListFeed($entry);
            }
        }
        $return = true;
    }
} else {
    echo "Login failed! " . (print_r($_REQUEST, true));
}
echo json_encode($return);
