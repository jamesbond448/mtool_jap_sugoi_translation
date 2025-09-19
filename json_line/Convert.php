<?php
ini_set('memory_limit', '500M');
$json = file_get_contents("ManualTransFile.json");
$data = json_decode($json);
if ($data == null) { //Json decode sometime will give an error, simple solution, skip the problematic line
    if (file_exists("LastErrorJson.json")) {
        $errorLine = json_decode(file_get_contents("LastErrorJson.json"));
    }
    $newFile = "";
    $newFileAttempt = "";
    $line_number = 1;
    $handle = fopen("ManualTransFile.json", "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $isErrorLine = false;
            foreach ($errorLine as $errL) {
                if ($line_number == $errL) $isErrorLine = true;
            }
            if (!$isErrorLine) {
                $newFile .= $line . PHP_EOL;
            }
            $line_number++;
        }
        fclose($handle);
        $newFile = substr($newFile, 0, -4) . PHP_EOL;
        $newFile .= "}";
        $data = json_decode($newFile);
    }
}

$directory = scandir("extract");
$translation = "";
foreach ($directory as $file) {
    if (str_contains("extract/" . $file, "_output") && is_file("extract/" . $file)) $translation .= file_get_contents("extract/" . $file);
}

$setting = file_get_contents("setting.json");
$setting = json_decode($setting);

$LLM_EXTRACT = $setting->llm_extract;

//$translation = file_get_contents("extract/extracted_output.txt");
$translation = preg_split('/\r\n|\r|\n/', $translation);

$i = 0;
foreach ($data as $key => $value) {
    $matches = "";
    $value = str_replace("　", "  ", $value);
    preg_match('/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u', $value, $matches, PREG_UNMATCHED_AS_NULL);
    if (!empty($matches)) {
        if ($LLM_EXTRACT) { //Will extract one line exactly as one line ignore formating
            $temp = str_replace("\\n", "\n", $translation[$i]);
            $data->$key = mb_convert_encoding($temp, "UTF-8", mb_detect_encoding($translation[$i]));
            $i++;
            continue;
        }
        $temp = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "|||", $value)));
        if (str_contains($temp, "|||")) {
            $temp = explode("|||", $temp);
            $newLine = "";
            $numberOfSpecialCharacter = count($temp);
            $indexOfSpecialCharacter = 0;
            foreach ($temp as $line) {
                $matches = "";
                preg_match('/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u', $value, $matches, PREG_UNMATCHED_AS_NULL);
                if (!empty($matches)) {
                    $temp1 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "", $line)));
                    if (!empty($temp1)) {
                        $newLine .= $translation[$i];
                        $i++;
                        if ($indexOfSpecialCharacter < $numberOfSpecialCharacter - 1) { //Last character of line
                            $minimumSkip = false;
                            if (str_contains($value, "\r")) {
                                $newLine .= "\r";
                                $minimumSkip = true;
                            }
                            if (str_contains($value, "\n")) {
                                $newLine .= "\n";
                                $minimumSkip = true;
                            }
                            if (!$minimumSkip) {
                                $newLine .= "\n";
                            }
                            if (!str_contains($temp[$indexOfSpecialCharacter + 1], "　")) {
                                $newLine .= "  ";
                            }
                        }
                    }
                }
                $indexOfSpecialCharacter++;
            }
            if (!empty($newLine)) {
                $data->$key = $newLine;
            }
        } else {
            $data->$key = mb_convert_encoding($translation[$i], "UTF-8", mb_detect_encoding($translation[$i]));
            $i++;
        }
    }
}


$jsonString = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// Write in the file
$fp = fopen("translationDone.json", 'w');
fwrite($fp, $jsonString);
fclose($fp);
?>
<html>

<body>
    <?php
    echo "<p>Convertion done:</p>";
    echo "<p>translationDone.json has been created </p>";
    ?>
</body>

</html>
