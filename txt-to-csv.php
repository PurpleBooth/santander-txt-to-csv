<?php


$data = file_get_contents($argv[1]);

$blocks = explode("\t\t\t\t\t\t\t", $data);

$dateRange = $blocks[0];

$blocks = explode("\t\t\t\t\t\t", $blocks[1]);

$accountDetails = array_shift($blocks);
$extractedRows = array();

foreach ($blocks as $row) {
    $extractedRows[] = extractRow(trim($row));
}

function extractRow($row)
{
    $lines = explode("\n", $row);

    $extractedRows = array();

    foreach ($lines as $line) {
        list($key, $value) = extractValue($line);

        $extractedRows[$key] = $value;
    }

    return $extractedRows;
}

function extractValue($line)
{
    $valueKey = explode(":\xA0", $line);

    return array(trim($valueKey[0]), trim($valueKey[1], " \t\n\r\0\x0B\xA0"));
}

$lastRow = array_pop($extractedRows);
array_push($extractedRows, $lastRow);
fputcsv(STDOUT, array_keys($lastRow));

foreach ($extractedRows as $fields) {
    fputcsv(STDOUT, $fields);
}

