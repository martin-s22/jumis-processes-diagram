<?php
// ClickUp API settings
define('CLICKUP_API_KEY', 'pk_164563706_VIM4ZPFTGVB92NNNXSA0PJDAW1IOBLQ2');

// Folder IDs in the Jumis Luxury space
$folderIds = [
    'ТАПЕТАРИЈА' => '90124332407',
    'СТОЛИЧАРА' => '90124349551',
    'РАМНИ ПОВРШИНИ' => '90124349660',
    'КНИГОВОДСТВО' => '90124349673',
    'САЛОН КОЧАНИ' => '90124349695',
    'САЛОН СКОПЈЕ' => '90124349717',
    'ПЕЛЕТАРА' => '90124349760',
    'МАГАЦИН' => '90124349788',
    'БРАВАРИЈА' => '90124349812',
    'ФАРБАРА' => '90124349837'
];

// Pantheon API settings
define('PANTHEON_API_KEY', 'your_pantheon_api_key');
define('PANTHEON_API_URL', 'https://pantheon.example.com/api/products'); // Replace with the correct Pantheon endpoint

// Email-to-ClickUp User ID Map
$emailToClickUpIdMap = [
    'aleksandarzarkovski@gmail.com' => 87730729,
    'nikola.jumis@yahoo.com' => 87730716,
    'export@jumisluxury.mk' => 87730701,
    'jumis_smetkovodstvo@yahoo.com' => 87730700,
    'info@jumisluxury.mk' => 87730698,
    'info@mebeljumis.mk' => 87730697,
    'jumis_jovanov@yahoo.com' => 87730644,
    'jumispogon@yahoo.com' => 87730638,
    'elena.a@mebeljumis.mk' => 87730586,
    'jumis.tapetarija@gmail.com' => 87730584,
    'goran.jumis@yahoo.com' => 87730572,
    'chairs@mebeljumis.mk' => 87730123,
    'martin.serafimov@gmail.com' => 164563706
];

// Task status mapping
$statusMap = [
    'незавршен' => 'Not Started',
    'во изработка' => 'In Progress',
    'комплетиран' => 'Completed'
];

// Function to fetch data from Pantheon API
function fetchPantheonProducts() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PANTHEON_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . PANTHEON_API_KEY
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Pantheon API Error: ' . curl_error($ch);
    }
    curl_close($ch);

    return json_decode($response, true); // Decode JSON response
}

// Function to create a task in ClickUp
function createClickUpTask($folderId, $product, $classificationToEmailMap, $emailToClickUpIdMap) {
    $clickUpUrl = "https://api.clickup.com/api/v2/list/$folderId/task";

    // Determine the assignee based on classification
    $classification = $product['Класификација']; // Field for classification
    $email = $classificationToEmailMap[$classification] ?? null;

    if (!$email || !isset($emailToClickUpIdMap[$email])) {
        echo "No valid assignee for classification: $classification\n";
        return;
    }

    $assigneeId = $emailToClickUpIdMap[$email];

    // Prepare the payload with mapped fields
    $payload = [
        'name' => $product['Назив на артиклот'], // Task Name
        'description' => 'Број на нарачка: ' . $product['Број на нарачка'], // Task Description
        'custom_id' => $product['Идент'], // Task ID
        'start_date' => strtotime($product['Датум']) * 1000, // Start Date in milliseconds
        'due_date' => strtotime($product['Важност']) * 1000, // Due Date in milliseconds
        'tags' => [$product['Количина']], // Tags
        'assignees' => [$assigneeId], // Assignee(s)
        'status' => $status // Task Status
    ];

    // cURL request to ClickUp API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $clickUpUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . CLICKUP_API_KEY,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'ClickUp API Error: ' . curl_error($ch);
    } else {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode === 200) {
            echo "Task created: " . $payload['name'] . " in folder ID $folderId\n";
        } else {
            echo "Failed to create task: " . $payload['name'] . ". Response: " . $response . "\n";
        }
    }
    curl_close($ch);
}

// Main script execution
$products = fetchPantheonProducts();

if (!empty($products)) {
    foreach ($products as $product) {
        // Iterate over each folder and create a task
        foreach ($folderIds as $folderName => $folderId) {
            createClickUpTask($folderId, $product, $classificationToEmailMap, $emailToClickUpIdMap);
        }
    }
} else {
    echo "No products found in Pantheon API.\n";
}