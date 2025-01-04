<?php

if (!file_exists('setting.json')) { //If setting file doesn't exist, will create it here
	$setting["number_line"] = 5000;
	$setting["number_line_description"] = "By default number_line is at 5000, if you want a smaller amount of file simply put a higher number or the reverse for more file";
	$setting["number_padding"] = 4;
	$setting["number_padding_description"] = "By default number_padding is at 4, if you have more than 9999 files extracted you need to put a higher padding number";
	file_put_contents("setting.json", json_encode($setting, JSON_PRETTY_PRINT)); //make pretty so user can easily read it
	echo "Created setting file";
}
$setting = file_get_contents("setting.json");
$setting = json_decode($setting);

$PADDING_NUMBER = '%0' . $setting->number_padding . 'd';
$NUMBER_OF_LINE = $setting->number_line;

$json = file_get_contents("ManualTransFile.json");
$data = json_decode($json);

$currentLine = 0;
$indexFile = 1;
if (!file_exists('extract')) {
	mkdir('extract', 0777, true);
}
$file = fopen('extract/extracted' . sprintf($PADDING_NUMBER, $indexFile) . '.txt', 'w');
foreach ($data as $key => $value) {
	if ($currentLine >= $NUMBER_OF_LINE) {
		$currentLine = 0;
		fclose($file);
		//Remove white space sugpi translation give weird result when translating white space
		$corrected = file_get_contents('extract/extracted' . sprintf($PADDING_NUMBER, $indexFile) . '.txt');
		$corrected = rtrim($corrected);
		file_put_contents('extract/extracted' . sprintf($PADDING_NUMBER, $indexFile) . '.txt', $corrected);

		$indexFile++;
		$file = fopen('extract/extracted' . sprintf($PADDING_NUMBER, $indexFile) . '.txt', 'w');
	}
	$matches = "";
	$value = str_replace("　", "  ", $value);
	preg_match('/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u', $value, $matches, PREG_UNMATCHED_AS_NULL); //We only take japanese character if there none, no need to translate
	if (!empty($matches)) {
		$temp = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "|||", $value))); //Dialog box has multiple line but we want to seperate them in order to translate properly
		if (str_contains($temp, "|||")) {
			$temp = explode("|||", $temp);
			foreach ($temp as $line) {
				$matches = "";
				preg_match('/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u', $value, $matches, PREG_UNMATCHED_AS_NULL);
				if (!empty($matches)) {
					$temp = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "", $line)));
					if (!empty($temp)) {
						fwrite($file, $temp . PHP_EOL);
						$currentLine++;
					}
				}
			}
		} else {
			fwrite($file, $value . PHP_EOL);
			$currentLine++;
		}
	}
}
fclose($file);
//Remove white space sugpi translation give weird result when translating white space
$corrected = file_get_contents('extract/extracted' . sprintf($PADDING_NUMBER, $indexFile) . '.txt');
$corrected = rtrim($corrected);
file_put_contents('extract/extracted' . sprintf($PADDING_NUMBER, $indexFile) . '.txt', $corrected);
?>
<html>

<body>
	<?php
	echo "<p>Current settings:</p>";
	echo "<p>Number of lines extracted: " . $NUMBER_OF_LINE . "</p>";
	echo "<p>Number of padding extracted: " . $setting->number_padding . "</p>";
	echo "<p>Extraction of process Done</p>";
	echo "<p>Resulted in a number of files: " . $indexFile . " extracted</p>";
	?>
</body>

</html>