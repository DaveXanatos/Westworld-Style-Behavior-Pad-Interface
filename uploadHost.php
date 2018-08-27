<?php
$dir = 'HostBuilds/'; 
$activeHost = htmlspecialchars($_REQUEST["hostName"]);
$my_file = $activeHost . ".txt";
$loadHost = $dir . $my_file;
$targetHost = $dir . "ACTIVEHOST.txt"

if (file_exists($loadHost)) {
    echo "The file $loadHost exists\r\n";
	//copy($loadHost,$targetHost);
	}
} else {
    echo "The file $loadHost does not exist";
}

?>