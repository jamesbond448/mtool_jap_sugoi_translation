<?php

$setting = file_get_contents("setting.json");
$setting = json_decode($setting);

$contextArray = [];
$contextLength = $setting->context_length;
$fileSkipAmount = $setting->file_skip_amount;
$filePaddingNumber = $setting->number_padding;

// Function to process each line through a GPT endpoint
function processLineWithGPT($line, $context)
{
    global $setting;
    $url = $setting->url; 
    $model = $setting->model;
    $systemPrompt = file_get_contents('GPTPrompt.txt');

    // JSON data to send
    $jsonData = [
        "model" => $model,
        "messages" => [
            [
                "role" => "system",
                "content" => $systemPrompt
            ],
            [
                "role" => "user",
                "content" => $line
            ]
        ],
        "max_tokens" => 4096,
        "n" => 1,
        "top_p" => 0.2,
        "temperature" => 0.1,
        "stream" => false,
        "frequency_penalty" => 0
    ];

    $jsonData['messages'] = [...$context, ...$jsonData['messages']];

    // Encode the data to JSON
    $jsonBody = json_encode($jsonData, JSON_UNESCAPED_UNICODE);

    // Setup the request headers
    $headers = array(
        'Content-Type: application/json',
    );

    // Prepare the context for the HTTP request
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $jsonBody
        )
    );

    $context = stream_context_create($options);

    // Send the request
    $response = @file_get_contents($url, false, $context);

    // Check if the request was successful
    if ($response === FALSE) {
        return "Error sending request";
    }

    return $response;
}

// Directory where input files are located
$dir = 'extract/';

// Open directory
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        // Check if file matches the pattern 'extracted\d{n}.txt' where n is the padding number from the user setting
        if (preg_match('/^extracted(\d{' . $filePaddingNumber . '})\.txt$/', $file, $matches)) {
            $padding = $matches[1];
            if ($padding <= $fileSkipAmount) {
                continue;
            }
            $inputFilePath = $dir . $file;
            $outputFilePath = $dir . "extracted_output" . $padding . ".txt";

            // Open input file for reading
            $inputFile = fopen($inputFilePath, 'r');
            if ($inputFile) {
                // Open output file for writing
                $outputFile = fopen($outputFilePath, 'w');
                if ($outputFile) {
                    while (($line = fgets($inputFile)) !== false) {
                        // Prepare and process the line
                        $preparedLine = trim($line); // You might need more preparation here

                        $processedLine = processLineWithGPT($preparedLine, $contextArray);
                        $decoded = json_decode($processedLine, true);
                        
                        $content = '';
                        // If JSON decoding was successful and there's content in delta
                        if ($decoded !== null && isset($decoded['choices'][0]['message']['content'])) {
                            $content .= preg_replace('/\R/', '', $decoded['choices'][0]['message']['content']);
                            $context1 = [
                                "role" => "user",
                                "content" => $preparedLine
                            ];
                            $context2 = $decoded['choices'][0]['message'];
                            $contextArray[] = $context1;
                            $contextArray[] = $context2;
                            if (count($contextArray) > $contextLength * 2) {
                                array_shift($contextArray);
                                array_shift($contextArray);
                            }
                        }
                        
                        // Write to output file
                        fwrite($outputFile, $content . "\n");
                        // debug output
                        // echo $preparedLine . ' >> ' . $content;
                    }
                    fclose($outputFile);
                } else {
                    echo "Could not open output file for writing.\n";
                }
                fclose($inputFile);
            } else {
                echo "Could not open input file for reading.\n";
            }
        }
    }
    closedir($handle);
}

?>
