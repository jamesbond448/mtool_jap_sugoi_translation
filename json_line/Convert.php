<?php

$json = file_get_contents("ManualTransFile.json");
$data = json_decode($json);

$directory = scandir("extract");
$translation = "";
foreach ($directory as $file) {
    if (str_contains("extract/" . $file, "_output") && is_file("extract/" . $file)) $translation .= file_get_contents("extract/" . $file);
}

//$translation = file_get_contents("extract/extracted_output.txt");
$translation = preg_split('/\r\n|\r|\n/', $translation);

$i = 0;
foreach ($data as $key => $value) {
    $matches = "";
    $value = str_replace("　", "  ", $value);
    preg_match('/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u', $value, $matches, PREG_UNMATCHED_AS_NULL);
    if (!empty($matches)) {
        // ||| === <unk><unk><unk>"
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
            $data->$key = $translation[$i];
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