<?php

$json = file_get_contents("ManualTransFile.json");
$data = json_decode($json);

$currentLine = 0;
$indexFile = 1;
$file = fopen('extract/extracted' . sprintf('%02d', $indexFile) . '.txt', 'w');
foreach($data as $key => $value) 
{
	if($currentLine >= 5000){
		$currentLine = 0;
		fclose($file);
		$indexFile++;
		$file = fopen('extract/extracted' . sprintf('%02d', $indexFile) . '.txt', 'w');
	}
	$matches = "";
	$value = str_replace("　", "  ", $value);
	preg_match('/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u', $value, $matches, PREG_UNMATCHED_AS_NULL);//We only take japanese character if there none, no need to translate
	if(!empty($matches)){
		$temp = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "|||", $value)));//Dialog box has multiple line but we want to seperate them in order to translate properly
		if(str_contains($temp, "|||")){
			$temp = explode("|||", $temp);
			foreach($temp as $line){
				$matches = "";
				preg_match('/[\x{3000}-\x{303F}]|[\x{3040}-\x{309F}]|[\x{30A0}-\x{30FF}]|[\x{FF00}-\x{FFEF}]|[\x{4E00}-\x{9FAF}]|[\x{2605}-\x{2606}]|[\x{2190}-\x{2195}]|\x{203B}/u', $value, $matches, PREG_UNMATCHED_AS_NULL);
				if(!empty($matches)){
					$temp = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", "", $line)));
					if(!empty($temp)){
						fwrite($file, $temp . PHP_EOL);
						$currentLine++;
					}
				}
			}
		}else{
			fwrite($file, $value . PHP_EOL);
			$currentLine++;
		}
	}
		
}
fclose($file);


echo "Done";

?>