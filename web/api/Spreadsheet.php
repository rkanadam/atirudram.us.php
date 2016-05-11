<?php

require_once "common.php";

class Spreadsheet
{
    function __construct($spreadSheetId, $worksheetName)
    {
        $this->spreadSheetId = $spreadSheetId;
        $this->worksheetName = $worksheetName;
        $this->spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
        $this->spreadsheet = $this->spreadsheetService->getSpreadsheetById($this->spreadSheetId);
        $this->sheet = $this->spreadsheet->getWorksheets()->getByTitle($this->worksheetName);
    }

    function getListFeed($query = array ())
    {
        $entries = $this->sheet->getListFeed($query)->getEntries();
        $values = array();
        foreach ($entries as $entry) {
            $values[] = $entry->getValues();
        }
        return $values;
    }

    function insertIntoListFeed($values)
    {
        return $this->sheet->getListFeed()->insert($values);

    }
}

