<?php
// Create array with names from host profiles directory
$dir = 'HostBuilds/'; 
$files = scandir($dir); 
foreach($files as $ind_file){ 
	$a[] = $ind_file;
} 

$q = $_REQUEST["q"];
$x = "";
$predict = "";

if ($q !== "") {
    $q = strtolower($q);
    $len=strlen($q);
    foreach($a as $name) {
        if (stristr($q, substr($name, 0, $len))) {
            if ($predict === "") {
                $predict = $name;
				$x = explode(".", $predict);
				$predict = $x[0];
            } else {
                $predict .= ", $name";
				$x = explode(".", $predict);
				$predict = $x[0];
            }
        }
    }
}

$items = explode(",",$predict);
$c = count($items);
$temp = "";
for ($i=0;$i<=$c;$i++) {
	$thisOne = trim($items[$i]);
	if (strlen($thisOne) > 3) {
		$temp .= "<a href='?req=LH&hName=".$thisOne."' style='color:#7799cc;'>".$thisOne."</a> ";
	}
}

$predict = trim($temp);

$sl = strlen($predict);

echo $predict === "" ? "no suggestion, <a href='#' style='color:red;'>create</a>?" : $predict;
?>
