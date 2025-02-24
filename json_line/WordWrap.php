<?php
ini_set('memory_limit', '500M');
$json = file_get_contents("translationDone.json");
$data = json_decode($json);

$setting = file_get_contents("setting.json");
$setting = json_decode($setting);

$FORMAT_NUMBER = $setting->format_number;
$FORMAT_WORD_CUT = $setting->format_word_cut;

foreach ($data as $key => $value) {
    $temp = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "", $value)));
    if (strlen($value) >= $FORMAT_NUMBER) {
        if (!$FORMAT_WORD_CUT) {
            $newLine = wordwrap($temp, $FORMAT_NUMBER);
        } else {
            $newLine = wordwrap($temp, $FORMAT_NUMBER, "\n",true);
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