<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productInfoArray = json_decode($_POST['info'] ?? '[]', true);
    $imagepath = $_POST['image_url'] ?? '';
    $imageUrl = base64_encode(file_get_contents($imagepath));
    // Convert structured info to readable prompt
    $productInfoText = "Generate 100 worded product description, product tags, meta tag title, meta tag description, meta tag keywords and image alt text for:\n";
    foreach ($productInfoArray as $key => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        $productInfoText .= ucfirst($key) . ": " . $value . "\n";
    }

    $data = [
        "model" => "gpt-4o",
        "messages" => [[
            "role" => "user",
            "content" => [
                ["type" => "text", "text" => $productInfoText],
                ["type" => "image_url", "image_url" => [ "url" => "data:image/jpeg;base64," . $imageUrl ]]
            ]
        ]],
        "max_tokens" => 500
    ];
    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer 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',
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $response;
}
