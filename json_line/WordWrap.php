<?php

$json = file_get_contents("translationDone.json");
$data = json_decode($json);

$setting = file_get_contents("setting.json");
$setting = json_decode($setting);

$format_number = $setting->format_number;
$format_word = $setting->format_word_cut;

foreach ($data as $key => $value) {
    $temp = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "", $value)));
    if (strlen($value) >= $format_number) {
        if (!$format_word) {
            $newLine = wordwrap($temp, $format_number);
        } else {
            $newLine = wordwrap($temp, $format_number, "\n",true);
        }
        $data->$key = mb_convert_encoding($newLine, "UTF-8", mb_detect_encoding($newLine));;
    }
}

$jsonString = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
// Write in the file
$fp = fopen("translationDoneFormat.json", 'w');
fwrite($fp, $jsonString);
fclose($fp);
?>
<html>

<body>
    <?php
    echo "<p>Formatting done:</p>";
    echo "<p>translationDoneFormat.json has been created </p>";
    ?>
</body>

</html>