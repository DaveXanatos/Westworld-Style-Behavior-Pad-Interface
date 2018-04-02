<!--
Bernard Lowe|M|52|Head of Behavior|A0A0000031484|
OCV|scaleFactor=1.1|minNeighbors=5|minSize=(30,30)|
IMAGINATION|68
DECISIVENESS|62
PATIENCE|72
SELF-PRESERVATION|50
CRUELTY|2
HUMILITY|40
MEEKNESS|12
COORDINATION|50
VIVACITY|42
CANDOR|75
BULK APPERCEPTION|92
HUMOR|51
CHARM|78
SENSUALITY|61
COURAGE|77
TENACITY|89
EMPATHY|84
LOYALTY|92
AGGRESSION|16
CURIOSITY|79

Form name/value pairs are:
name="activeHost"
name="activeAttrib"
name="activeAttVal_ 0 to L"  L for most is 19 (20 attribs starting at 0, so 0 - 19)
name="p0 to Lx"
name="p0 to Ly"
name="IMC0 to L

-->

<?php
$dir = 'HostBuilds/'; 
$files = scandir($dir); 
foreach($files as $ind_file){ 
	$a[] = $ind_file;
} 

$activeHost = htmlspecialchars($_REQUEST["activeHost"]);
$thisFileName = $activeHost.".txt"
$activeAttVal_X = htmlspecialchars($_REQUEST["activeAttVal_X"]);
$actionFlag = htmlspecialchars($_REQUEST["actionFlag"]);

PseudoCode:

Compare $a[] array with $thisFileName
if found:
    open and read in all lines, replace attribute name/value pairs with new value pairs to be saved.
if notFound:
    Check for actionFlag = "CH" (Create Host)
	if CH:
		Create file, enter data, save.
	if notCH:
	    Return fail code, exit.

NOTES:  For create, will need to add fields for new Host ID#, name, age, sex, occupation and OCV factors.


		





