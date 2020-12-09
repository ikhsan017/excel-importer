<?php

if ($argc == 1) {
	die('Please specify excel file');
}

$input = $argv[1];
$output = isset($argv[2]) ? $argv[2] : str_replace(['.xlsm', '.xlsx', '.xls'], '.json', $input) ;
require './vendor/autoload.php';

echo "Importing formula from $input\n";

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($input);
$sheetcount = $spreadsheet->getSheetCount();
$sheetArray = [];

echo "Found $sheetcount sheets\n";
for ($i = 0; $i < $sheetcount; $i++) {

    $worksheet = $spreadsheet->getSheet($i);
    $maxRow = $worksheet->getHighestDataRow();
    $maxCol = $worksheet->getHighestDataColumn();
    $maxCol = ++$maxCol;
    $flattenArray = [];

    echo "Processing sheet \"" . $worksheet->getTitle() . "\"\n";

    for ($col = 'A'; $col <= $maxCol; ++$col) {
        for ($row = 1; $row <= $maxRow; ++$row) {
            $cell = $worksheet->getCell($col . $row);
            $format = $cell->getStyle()->getNumberFormat()->getFormatCode();

            if ($cell->isFormula()) {
                $flattenArray[$col . $row] = [
                    'format'  => $format == 'General' ? '' : $format,
                    'formula' => ltrim($cell->getValue(), '='),
                ];
            }
        }
    }

    $sheetArray[$worksheet->getTitle()] = $flattenArray;
}

$sheetJson = json_encode($sheetArray, JSON_PRETTY_PRINT);

echo "Writing output to $output\n";
$file = fopen($output, 'w+');
fwrite($file, $sheetJson);
fclose($file);
