<?php
$dir = 'HostBuilds/'; 
$activeHost = htmlspecialchars($_REQUEST["activeHost"]);
$my_file = $activeHost . ".txt";
$filename = $dir . $my_file;
$newFile = "";

foreach ($_REQUEST as $key => $value)
	if (strpos($key, 'activeAttVal_') !== false) {
		$myDump = $myDump . htmlspecialchars($value) . "\r\n";
	}
//echo $myDump."\r\n";

$separator = "\r\n";
$dline = explode($separator, $myDump);

if (file_exists($filename)) {
    //echo "The file $filename exists\r\n";
	$lines = file($filename, FILE_IGNORE_NEW_LINES);    // $a = "activeAttVal_";     if (strpos($a, 'are') !== false) {     do whatever    }
	$n = count($lines);
	$newFile = $lines[0]."\r\n".$lines[1]."\r\n";
	for ($i = 2; $i < $n; $i++) {
		$thisAtt = explode("|",$lines[$i]);
		$newFile = $newFile.$thisAtt[0]."|".$dline[$i-2]."\r\n";
	}
} else {
    echo "The file $filename does not exist";
}

echo $newFile;

//$my_file = $activeHost . ".txt";
$handle = fopen($filename, 'w') or die('Cannot open file:  '.$filename);
fwrite($handle, $newFile);
fclose($handle);




/*
PseudoCode:

File Exists?
if found:
    open and read in all lines, replace attribute name/value pairs with new value pairs to be saved.
if notFound:
    Check for actionFlag = "CH" (Create Host)
	if CH:
		Create file, enter data, save.
	if notCH:
	    Return fail code, exit.

NOTES:  For create, will need to add fields for new Host ID#, name, age, sex, occupation and OCV factors.

*/

?>