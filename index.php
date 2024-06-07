<?php
require __DIR__ . '/vendor/autoload.php';

$client = new \Google_Client ();
$client->setApplicationName('google sheets');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');

$path = 'voltaic-space-389820-13609455d67c.json';
$client->setAuthConfig($path);

// configure the Sheets Service
$service = new \Google_Service_Sheets ($client);

$spreadsheetId = '10SoJWI1LJbLkX-dwVqKK0ORAH6sh7yk1KJUxwGDLTy8';

$inputData = [
    5,
    'ffff',
    'gggg',
    'bbbb',
];

$range = 'Sheet1';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$contents = $response->getValues();

//get the position of the row to insert
$startIndex = 0;
foreach ($contents as $item) {
    if ($item[0] < $inputData[0]) {
        $startIndex++;
    } else {
        break;
    }

}

//insert a blank row at startIndex
$request = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
    'requests' => [
        'insertDimension' => [
            'range' => [
                'startIndex' => $startIndex,
                'endIndex' => $startIndex + 1,
                'dimension' => 'ROWS',
                'sheetId' => 0,
            ],
        ],
    ],
]);

$result = $service->spreadsheets->batchUpdate($spreadsheetId, $request);

//update the row at startIndex
$rows = [$inputData];
$valueRange = new \Google_Service_Sheets_ValueRange ();
$valueRange->setValues($rows);
$range = 'Sheet1!A' . $startIndex + 1;
$options = ['valueInputOption' => 'USER_ENTERED'];
$service->spreadsheets_values->update($spreadsheetId, $range, $valueRange, $options);
