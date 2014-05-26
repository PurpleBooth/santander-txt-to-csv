<?php

/**
 * Usage: php txt-to-csv.php whatever-the-csv-is.csv
 */

/**
 * Character string that splits the date range with the rest of the file
 */
const DATE_RANGE_CONTENT_DELIMITER = "\t\t\t\t\t\t\t";

/**
 * Character string that splits the account details and each transaction block
 */
const TRANSACTION_BLOCK_DELIMITER = "\t\t\t\t\t\t";

/**
 * Inside a block, what separates each key value line
 */
const BLOCK_KEY_VALUE_DELIMITER = "\n";

/**
 * Inside a key value line, what separates a key and a value. A0 is none breaking space
 */
const KEY_VALUE_DELIMITER = ":\xA0";

/**
 * What characters should be trimmed off the value. (It's the standard trim characters, plus none breaking Space)
 */
const TRIMMABLE_CHARACTERS = " \t\n\r\0\x0B\xA0";

/**
 * Take a block (which represents a single transaction) and turn it into a key value array
 *
 * The input looks something like this (where the spaces are None breaking spaces (character A0).
 *
 * Key Name 1: Value
 * Key Name 2: Value
 * Key Name 3: Value
 *
 * @param string $row
 * @return array
 */
function extractRow($row)
{
    $lines = explode(BLOCK_KEY_VALUE_DELIMITER, $row);
    $extractedRows = array();

    foreach ($lines as $line) {
        list($key, $value) = extractValue($line);

        $extractedRows[$key] = $value;
    }

    return $extractedRows;
}

/**
 * Take a line which is roughly "Key Name"+KEY_VALUE_DELIMITER+"Value"
 *
 * And return array( 'Key Name', 'Value' );
 *
 * @param string $line
 * @return array
 */
function extractValue($line)
{
    $valueKey = explode(KEY_VALUE_DELIMITER, $line);

    return array(trim($valueKey[0]), trim($valueKey[1], TRIMMABLE_CHARACTERS));
}

// Read the file first
$data = file_get_contents($argv[1]);

// Split the date range and the transaction blocks
$dateAndBlocks = explode(DATE_RANGE_CONTENT_DELIMITER, $data);
$dateRange = $dateAndBlocks[0];

// Get the transaction details in an array of unprocessed blocks
$blocks = explode(TRANSACTION_BLOCK_DELIMITER, $dateAndBlocks[1]);

// Get rid of the first block, as all it does is contain the account number
$accountDetails = array_shift($blocks);

$extractedRows = array();

// For each transaction block extract a key value array of the blocks
foreach ($blocks as $row) {
    $extractedRows[] = extractRow(trim($row));
}

// Assume that all of the blocks will have the same keys in it
// Use the keys from the last transaction block as the header for the CSV file
$lastRow = array_pop($extractedRows);
array_push($extractedRows, $lastRow);
fputcsv(STDOUT, array_keys($lastRow));

// Output each of the transactions as a line in the CSV file
foreach ($extractedRows as $fields) {
    fputcsv(STDOUT, $fields);
}

