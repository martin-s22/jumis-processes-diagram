<?php

// API endpoint URL
define('API_URL', 'http://192.168.88.25/api/Ident/retrieve'); // Replace with the correct endpoint if needed

// API token (if authentication is required, otherwise remove the Authorization header)
define('API_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1laWQiOiI1ODQiLCJwYXdzL1VzZXJJZCI6IjU4NCIsInVuaXF1ZV9uYW1lIjoiTVMiLCJDb25uU3RyIjoiQVFBQUFOQ01uZDhCRmRFUmpIb0F3RS9DbCtzQkFBQUF2bWNqNU92N3YwT2RmVm04eDhjejRnQUFBQUFDQUFBQUFBQVFaZ0FBQUFFQUFDQUFBQUF5a3U0MkZwRmFjMTc3MllHS3dITGtGb3hHeSswS0hPK2N3cjQwRmo4ck5RQUFBQUFPZ0FBQUFBSUFBQ0FBQUFDOUhIaEdlc2Y4S2Y4dm9IUUNiN2R3eEE5Rk1zd2E5ZkhLayttUGVhUGozTkFBQUFESSs5c0RxV0t4VjFtMnpPN24zUlhFUHdaU2ViZ1B2WU5aWTNuaHVGODRPYThGemQ0SXRoekc5eEt6K3J0dlhBQWZDYTlzVjQyaEVVOU5lRkhMdm1Pc2pHSnhzT1lEcCtvRDAxZllZb3dBRDRVTEJVU2pBUDZDMFpJUVlvbllRL2Y5MzVnYVVmcFhFVlVYb1M5OXcwbXdvTFNnYUxhQUFud2kwOTJFeGRWOUtDdlR3TVptVjdJdWRkbXpHSzgybytpVDBoZDE1dCs3WDRDV3UrbnhtazhHVUVITUxFOTJOcm9YMmluZnBHbFI0UEZCd3p4QnFLa1RTY2F2U2Z2c0ZMN29HbVA2V2E3Z3BxajFlVGxuWk91NlFBQUFBQ0ZnZzBXeXQyemFjS2dRS29DcE4wU1BtKzdDZUF2MFJ4eFluNkdMR1FCTTkyRkFFR3VmeUQveVBpU1VCU09ab1dDZFpxR2c5R2dpNDdTcGtSendCNlk9IiwiSVZzIjoiIiwiRHRFeHAiOiIyMi4xLjIwMjUgMTM6MjU6MDIiLCJuYmYiOjE3Mzc1NTA1MDIsImV4cCI6MTczNzU1MjMwMiwiaWF0IjoxNzM3NTUwNTAyfQ.cLG8Z9N71QIcBVclpXRdFM57fRnsQ0-JjvN4XIDyogs'); // Replace with your API token or remove if not needed

// Payload for the POST request
$data = [
    "start" => 0,
    "length" => 10, // Number of results to return
    "fieldsToReturn" => "Шифра,Назив на артиклот,Количина,Датум,Рок на испорака,Оддел,Испртена,Нарачател,Статус,Достава",
    "customConditions" => [
        "condition" => "Назив на артиклот = ? AND Шифра = ?",
        "params" => ["Кујна", "630-0121 КУЈНА"]
    ],
    "sortColumn" => "Датум", // Sort by Датум
    "sortOrder" => "ASC", // Sort in ascending order
    "withSubSelects" => 0 // Flat structure
];

// Convert the payload to JSON
$jsonData = json_encode($data);

// cURL initialization
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . API_TOKEN, // Include the API token for authentication
    'Content-Type: application/json' // Specify the content type
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

// Execute the request and fetch the response
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode === 200) {
        // Decode and display the response (if successful)
        $responseData = json_decode($response, true);
        echo "API Response:\n";
        print_r($responseData);
    } else {
        // Display the error response (if failed)
        echo "Failed to retrieve data. HTTP Code: $httpCode\n";
        echo "Response: $response\n";
    }
}

// Close cURL session
curl_close($ch);