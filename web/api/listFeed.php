<?php

require_once "common.php";

$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();

$sheet = $_REQUEST["sheet"];
$tab = $_REQUEST["tab"];

$spreadsheet = $spreadsheetService->getSpreadsheetById($sheet);
$method = strtolower($_SERVER['REQUEST_METHOD']);
$sheet = $spreadsheet->getWorksheets()->getByTitle($tab);
$entries = $sheet->getListFeed()->getEntries();
if ($method === "get") {
    $json = array();
    foreach ($entries as $entry) {
        $json[] = $entry->getValues();
    }
    echo json_encode($json);
} else if ($method === "post") {
    $entry = json_decode($_REQUEST["entry"]);
    $sheet->getListFeed()->insert($entry);
}