<?php
$dir = 'HostBuilds/'; 
$activeHost = htmlspecialchars($_REQUEST["hostName"]);
$loadHost = $dir . $activeHost . ".txt";
$targetHost = $dir . "ACTIVEHOST.txt";

if (file_exists($loadHost)) {
    //echo "The file \"$loadHost\" exists.  ";
	copy($loadHost,$targetHost);
	echo "Host Profile \"$activeHost\" is now active in host body";
} else {
    echo "The file \"$loadHost\" does not exist";
}

?>

