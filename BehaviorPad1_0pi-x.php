<?php 
ini_set('display_errors', 1);
error_reporting(R_ALL & ~E_NOTICE);

$user_id = htmlspecialchars($_GET['user_id']);
$host_name = htmlspecialchars($_GET['hName']);
$request_type = htmlspecialchars($_GET['req']);
$tv = htmlspecialchars($_GET['tv']);

if ($host_name == "") {
	$host_name = "MAEVE";
}

if ($tv == "") {
	$tv = 50;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?= $host_name ?> Host Behavior Pad Control Interface</title>
<!-- https://youtu.be/L6EatojX3SI -->
<!-- ****************** TO DO LIST **************************

Set slider to report 0-100 in small white text, with 0-20 steps displaying in large text, and attrib number in white small above that

Set system to save a profile

-->

<link rel="stylesheet" type="text/css" href="styles.css">

</head>

<body bgcolor="#000000" onload="draw(); buildMap(); loadHost(); setAttrib(0);">

<script>
<!-- //
// https://stackoverflow.com/questions/18592679/xmlhttprequest-to-post-html-form
//alert(location.pathname);  // /BP/BehaviorPad1_0x.php
//alert(location.search);    // ?req=LH&hName=CLEMENTINE#
	var winWidth = (window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth)*.9;
	var winHeight = (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight)*.9;
	//alert(winWidth + " " + winHeight)
    document.write('<a href="#" onClick="showHide()"><img src="image/DelosLogo.jpg" style="position:absolute;top:0px;left:0px;width:' + winHeight*.1738 + 'px;height:' + winHeight*.0545 + 'px;border:0px;"></a>')    // Based on an 863.1 pixel winHeight, 150px x 47px  // 1.1586 should yielod a 1000px image on the 863.1 px high window height initially designed on.
	var displayPath = location.pathname;
    document.write('<span style="position:absolute;top:0px;left:250px;margin-left:50px;color:#555555;font-family:Arial;font-size:' + winHeight*.02 + 'px;font-weight:bold;text-align:center;line-height:' + winHeight*.1 + 'px;">' + displayPath + '</span>')   // 1.1586 should yield a 1000px image on the 863.1 px high window height initially designed on.
    document.write('<div id="BPMatrix" style="position:absolute;top:' + winHeight*.08 + 'px;left:' + winHeight*.07 + 'px;display:none;">') 

function showHide() {
	var x = document.getElementById("BPMatrix");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}

function findHost(str) {
    if (str.length == 0) { 
        document.getElementById("hostList").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("hostList").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "scandir.php?q=" + str, true);
        xmlhttp.send();
    }
}

ts=0;
if ('ontouchstart' in window) {
	alert("TouchScreen");
	ts=1;
}

window.onresize = function(){ location.reload(); }

function getCurrentFile() {
  var filename = document.location.href;
  //alert(filename);
  var tail = (filename.indexOf(".", (filename.indexOf(".com")+1)) == -1) ? filename.length : filename.lastIndexOf(".");
  return (filename.lastIndexOf("/") >= (filename.length - 1)) ? (filename.substring( filename.substring(0, filename.length - 2).lastIndexOf("/")+1, filename.lastIndexOf("/"))).toLowerCase() : (filename.substring(filename.lastIndexOf("/")+1, tail));
}

function cancelMods() {                           // Cancel Button in upper left.
	var whichHost = "<?= $host_name ?>"
	var thisFile = getCurrentFile();
	//alert(thisFile);
	if (confirm("Discard all changes to host profile and revert to original?") == true) {
		window.location.href = thisFile + ".php?req=LH&hName="+whichHost
	} else {
    	return false;
  }
}

function buildMap() {
	mapBuild = "<map name='grammap'>\n" 
	for (IMV = 0;IMV < L;IMV++) {
		IMVV = "IMC" + IMV
		thisV = document.getElementById(IMVV).value;
		//alert(thisV);
		thisLine = '<area shape="rect" coords="' + thisV + '" onclick="setAttrib(' + IMV + ')" />\n'
		mapBuild = mapBuild + thisLine;
	}
	mapBuild = mapBuild + "</map>";
	document.getElementById("mapbox").innerHTML = mapBuild;
	return false;
}
		
function echoHostXY() {
	hostVals = "";
	for (i=0;i<L;i++) {
		inX = "p" + i + "x"
		inY = "p" + i + "y"
		thisX = document.getElementById(inX).value;
		thisY = document.getElementById(inY).value;
		hostVals = hostVals + thisX + " " + thisY + "\n"
	}
	alert(hostVals);
}

function echoHost() {
	if (confirm("Overwrite existing host profile with current hostgram settings?") == true) {
		hostVals = "";
		for (i=0;i<L;i++) {
			thisI = "activeAttVal_" + i
			thisV = document.getElementById(thisI).value;
			hostVals = hostVals + i + ": " + thisV + "\n"
		}
		var form = document.getElementById("datastore");
		submitForm(form);
	} else {
    	return false;
  }
}

function submitForm(oFormElement)
{
  var xhr = new XMLHttpRequest();
  xhr.onload = function(){ alert (xhr.responseText); }
  xhr.open (oFormElement.method, oFormElement.action, true);
  xhr.send (new FormData (oFormElement));
  return false;
}

// Set up attribute Array - Note they start at 6:00 and build counterclockwise.
var attribute = [<?php 
	$data = file("HostBuilds/" . $host_name . ".txt");     // Reads each line of file into an addressable array $data(0), $data(1)...
	$n = count($data);                                     // Gets line count in $data
	// Maeve Millay|F|36|Madam at Mariposa|AC50000487105
	$thisHost = $data[0];
	$hostParts = explode("|", $thisHost);
	$hFname = $hostParts[0];
	$hSex = $hostParts[1];
	$hAge = $hostParts[2];
	$hCurrJob = $hostParts[3];
	$hIDno = trim($hostParts[4]);
	for ($v = 3; $v <= $n; $v++) {                         // First two lines are other data, not attribs.
		print '"'.trim($data[$v-1]).'",';                  // "ATTRIB_NAME|VAL", "ATTRIB_NAME|VAL", ... "ATTRIB_NAME|VAL"
	}?>];	

L = attribute.length;                                      // Number of Attributes = Number of Spokes.

// Set up display parameters      // <<<<<<<<<<<<<<<<<<<<<<<< PRIME CANDIDATE FOR PHP-IFYING
var canvW = winHeight  // Will be set to screen Height: winHeight, when I make this fully scalable and elastic
var canvH = winHeight
var icX=Math.round(canvW/2) ///Image Center X
var icY=Math.round(canvH/2) ///Image Center Y
var ccD=Math.round(canvH*.09765625)  // Was 100 //Center Circle Diameter, pixels, 1024 * .09765625 = 100
var ocD=Math.round(canvH*.68359375)  // Was 700 //Outer Circle Diameter, pixels, 1024 * 68359375 = 700
var numSteps = 19 //# parameter steps (0-19... is 20

var ccR=ccD/2
var ocR=ocD/2
//Step Angle:
var sa=360/L
var slop = Math.round((ocR-ccR)%numSteps)
var paramStep = Math.round(((ocR-ccR)/numSteps))

var polyBuild = ""
var polyBuild2 = "M"

var mapPoints = [];
		
function drawGram(graphColor,strokeColor,ctxfont,ctxfillStyle,canvas) {
	if (canvas.getContext) {
		for (var R = ccR; R <= ocR; R += paramStep) {
			var ctx = canvas.getContext('2d'); 
			var X = canvas.width / 2;
			var Y = canvas.height / 2;
			ctx.beginPath();
			ctx.arc(X, Y, R, 0, 2 * Math.PI, false);
			ctx.lineWidth = 1.5;
			ctx.strokeStyle = strokeColor;
			ctx.stroke();
		}

		// 512, 512 is center of circle; 462 is short side length to inner circle, 562 is long length to inner circle (100px radius inner circle)
		
		attrib_cnt = 0
		for (i=0;i<360;i+=sa) {
			lineX=(Math.round(1000*(Math.sin(i*Math.PI/180.0))))/1000
			lineY=(Math.round(1000*(Math.cos(i*Math.PI/180.0))))/1000
			drwXs=Math.round((lineX*ccR)+icX)
			drwYs=Math.round((lineY*ccR)+icY)
			drwXe=Math.round((lineX*ocR)+icX)
			drwYe=Math.round((lineY*ocR)+icY)
			ctx.beginPath(); 
			ctx.strokeStyle = graphColor; //start top vert
			ctx.beginPath(); 
			ctx.strokeStyle = graphColor; //start top vert
			ctx.moveTo(drwYs,drwXs); //(in from R, down from top): end
			ctx.lineTo(drwYe,drwXe); //(in from R, down from top): fill
			//document.write(i + "; drwXs = " + drwXs + "; drwYs = " + drwYs + "; drwXe = " + drwXe + "; drwYe = " + drwXe + "<br />")
			ctx.stroke(); //show
			ctx.font = ctxfont;
			ctx.fillStyle = ctxfillStyle;
			drwXea = drwXe
			drwYea = drwYe

			inX = "p" + attrib_cnt + "x"
			inY = "p" + attrib_cnt + "y"
			document.getElementById(inX).value = drwXe;     // Read in initial point values that determine label positions, mask edges, etc.  p0x, p0y, p1x, p1y, etc.
			document.getElementById(inY).value = drwYe;

			attParts = attribute[attrib_cnt];
			attParts = attParts.split("|");
			txt = " " + attParts[0] + " [" + attParts[1] + "]";

			ctxMTW = Math.round(ctx.measureText(txt).width)
			
			if (drwXe == icX && drwYe < icY) {                        //12:00
				drwXea = drwXe - (ctxMTW/2)
				drwYea = drwYe - (ccD*.04)
			}

			if (drwXe == icX && drwYe > icY) {                        //6:00
				drwXea = drwXe - (ctxMTW/2)
				drwYea = drwYe + (ccD*.18)
			}

			if (drwXe > icX) {
				drwXea = drwXe + (ccD*.04)
				drwYea = drwYe + Math.round((drwXe/(ocR+icX))*8)
				//drwYea = drwYe + 10 //Half pixel height of text?
			}

			if (drwXe < icX && drwYe > icY) {
				drwXea = drwXe - (ctxMTW) - (ccD*.06)
				//drwYea = drwYe //+ Math.round((drwXe/(ocR+icX))*8)
				drwYea = drwYe + (ccD*.1) //Half pixel height of text?
			}

			if (drwXe < icX && drwYe < icY) {
				drwXea = drwXe - (ctxMTW) - (ccD*.06)
				drwYea = drwYe //+ Math.round((drwXe/(ocR+icX))*8)
				//drwYea = drwYe + 10 //Half pixel height of text?
			}

			if (drwXe < icX && drwYe == icY) {
				drwXea = drwXe - (ctxMTW) - (ccD*.06)
				drwYea = drwYe + (ccD*.1) //Half pixel height of text?
			}

			if (drwXe > icX && drwYe == icY) {
				drwXea = drwXe + (ccD*.06)
				drwYea = drwYe + (ccD*.05) //Half pixel height of text?
			}

			startIMX = Math.round(drwXea)  //Start Image Map X
			startIMY = Math.round(drwYea)
			endIMX = Math.round(drwXea + ctxMTW) // Measured Text Width
			endIMY = Math.round(drwYea - ccD*.2) // Calculated Text Height

			thesePoints = startIMX + "," + startIMY + "," + endIMX + "," + endIMY  // Image Map Coordinates (IMCx)
			document.getElementById("IMC" + attrib_cnt).value = thesePoints        // Set hiddeen input vals to hold Image Map Coordinates
			
			ctx.fillText(txt,drwXea,drwYea);

			attrib_cnt = attrib_cnt + 1
		}
	}
}

function draw() {
	var graphColor = "#446670";
	var strokeColor = "#446670"
	ctxfont = ccD*.18 + "px Arial";
	ctxfillStyle = "#70a8c9"; //"#668891";
	var canvas = document.getElementById('hostgram');
	//Draw concentric circles of hostgram, spokes, etc
	drawGram(graphColor,strokeColor,ctxfont,ctxfillStyle,canvas);

	var graphColor = "#003333";
	var strokeColor = "#114450"
	ctxfont = ccD*.18 + "px Arial";
	ctxfillStyle = "#70a8c9"; //"#668891";
	var canvas = document.getElementById('bggram');
	//Draw concentric circles of bggram, spokes, etc
	drawGram(graphColor,strokeColor,ctxfont,ctxfillStyle,canvas);

	var rotated = false;
    var div = document.getElementById('selAtt'),
    deg = rotated ? -90 : -90;
    div.style.webkitTransform = 'rotate('+deg+'deg)'; 
    div.style.mozTransform    = 'rotate('+deg+'deg)'; 
    div.style.msTransform     = 'rotate('+deg+'deg)'; 
    div.style.oTransform      = 'rotate('+deg+'deg)'; 
    div.style.transform       = 'rotate('+deg+'deg)'; 
}

function setAttrib(n) {
	attrName = attribute[n].split("|");
	document.getElementById("selAtt").innerHTML = attrName[0];   // Display selected attribute name in rotated box by slider bars
	document.getElementById("activeAttrib").value = n;           // Set hidden input activeAttrib.value to be the selected attribute number
	document.getElementById("testRange").value = attrName[1];    // Set slider value to be the selected attribute number's value
	setVal(attrName[1]);
}

function getCoords(attribNo,val) {
	lineX=(Math.round(1000*(Math.cos((attribNo*(360/L))*Math.PI/180.0))))/1000
	lineY=(Math.round(1000*(Math.sin((attribNo*(360/L))*Math.PI/180.0))))/1000
	drwXs=Math.round((lineX*ccR)+icX)
	drwYs=Math.round((lineY*ccR)+icY)
	drwXe=Math.round((lineX*(((ocR)*(val/100)*(ocR-ccR)/ocR)+ccR))+icX)
	drwYe=Math.round((lineY*(((ocR)*(val/100)*(ocR-ccR)/ocR)+ccR))+icY)
	return drwXe, drwYe
}

function loadHost() {
	for (hv=0;hv<L;hv++) {
	    thisName = attribute[hv].split("|");
	    document.getElementById("activeAttrib").value = hv;           // Set hidden input activeAttrib.value to be the selected attribute number
	    setVal(thisName[1]);
	}
}

function setVal(attVal) {  //attVal = 0 to 100; reads selected attrib (activeAttrib.value), sets the 0-100 value into the hidden input for the selected value, then converts 0 ro 100 value to pixel coords to clip the polygon
	whichAttrib = document.getElementById("activeAttrib").value   // Determine which attribute is active
	if (whichAttrib == "" || whichAttrib == null) {               // If no attrib selected (initial opening) then set 0 as default selection
		document.getElementById("activeAttrib").value = 0
		document.getElementById("selAtt").innerHTML = attribute[0];
		whichAttrib = 0
	}
	attrName = attribute[whichAttrib].split("|");
	thisVal = "activeAttVal_" + whichAttrib                       // Var to hold selected attribute hidden-input name
	document.getElementById(thisVal).value = attVal               // Set that attribute's hidden-input to the adjusted value
	attribute[whichAttrib] = attrName[0] + "|" + attVal
			  
	getCoords(whichAttrib,attVal);

	storeValX = "p" + whichAttrib + "x"
	storeValY = "p" + whichAttrib + "y"
	document.getElementById(storeValX).value = drwYe               // test
	document.getElementById(storeValY).value = drwXe               // test

	var at = [];
	for (i=0;i<L;i++) {
		inX = "p" + i + "x"
		inY = "p" + i + "y"
		thisX = document.getElementById(inX).value + "px ";
		thisY = document.getElementById(inY).value + "px";
		thisATline = thisX + thisY
		Llim = L-1
		if (i < Llim) {
			at[i] = thisATline + ", "
		} else {
			at[i] = thisATline
		} 
	}

	at[whichAttrib] = drwYe + "px " + drwXe + "px"
	Llim = L-1
	if (whichAttrib < Llim) {
		at[whichAttrib] = at[whichAttrib] + ", "
	}
	
	cpLine = ""
	for (nxtCPLine=0;nxtCPLine<L;nxtCPLine++) {
		cpLine = cpLine + at[nxtCPLine] ;
    }
	cpLine = "polygon(" + cpLine + ")"; 
	//alert(cpLine);
	//document.getElementById("hostgram").style.clipPath = "polygon(" + at[0] + at[1] + at[2] + at[3] + at[4] + at[5] + at[6] + at[7] + at[8] + at[9] + at[10] + at[11] + at[12] + at[13] + at[14] + at[15] + ")";  
	document.getElementById("hostgram").style.clipPath = cpLine;  
	document.getElementById("hostgram").style.filter = "drop-shadow(2px 2px 0px gray) drop-shadow(2px -2px 0px gray) drop-shadow(-2px 2px 0px gray) drop-shadow(-1px -1px 0px gray)"; 
 	document.getElementById("midl").innerHTML = attVal;

	barVal = Math.round((attVal/5))+1
	for (bv = 1;bv < 21;bv++) {
		if (bv < barVal) {
			bw=bv+1
			document.getElementById("mask"+bv).style.visibility = "hidden";
		} else {
			bw=bv+1
			document.getElementById("mask"+bv).style.visibility = "visible";
		}
		if (attVal > 0) {
			document.getElementById("mask1").style.visibility = "hidden";
		}
	}
	return false;
}

</script>

    <!-- https://stackoverflow.com/questions/18592679/xmlhttprequest-to-post-html-form -->
    <div id="datalayer" style="border:0px;width:100px;height:100px;position:absolute;top:0px;left:02px;visibility:hidden;">
        <form name="datastore" id="datastore" action="updateHost.php" method="post" onsubmit="return submitForm(this);">  <!-- All point data is written in here from the slider or other controls.  This serves as a RAM function of sorts. -->
            <input type="hidden" name="activeHost" id="activeHost" value="<?= $host_name ?>" />          <!--  Which Attribute is currently selected -->
            <input type="hidden" name="activeAttrib" id="activeAttrib" />          <!--  Which Attribute is currently selected -->
            <script language="JavaScript">
            for (iav=0;iav<L;iav++) {
                document.write('<input type="hidden" name="activeAttVal_' + iav + '" id="activeAttVal_' + iav + '" value="100" />');  // Currently Active Attribute Values, default is 100
                document.write('<input type="hidden" name="p' + iav + 'x" id="p' + iav + 'x" /><input type="hidden" name="p' + iav + 'y" id="p' + iav + 'y" />');  //Current X/Y Vals
                document.write('<input type="hidden" name="IMC' + iav + '" id="IMC' + iav + '" />');  // Image Map Coordinates
            }
            </script>
            <input type="submit" value="submit" style="position:absolute;top:0px;left:1000px;">
        </form>
    </div>
    
    <script language="JavaScript">
      //alert(canvH + " " + ccD + " " + icY)    // 'placer' sets position of center orange value
      placer = icY-(ccD/2)     
      hostID = "ID#<?=$hIDno?>"
      document.write('<canvas id="bggram" width="' + canvH + '" height="' + canvH + '" style="background-color:#000000;position:absolute;top:0px;left:0px;"></canvas>')
      document.write('<canvas id="hostgram" width="' + canvH + '" height="' + canvH + '" style="background-color:#002225;position:absolute;top:0px;left:0px;"></canvas>')
      document.write('<div id="midl" style="border:0px;width:' + ccD + 'px;height:' + ccD + 'px;position:absolute;top:' + placer + 'px;left:' + placer + 'px;z-axis:0;font-family:Arial;font-size:' + ccD*.4 + 'px;font-weight:bold;color:#FF7700;text-align:center;line-height:' + ccD + 'px;"></div>')
      document.write('<div id="midl2" style="border:1px solid #444455;width:' + ocD*1.142857 + 'px;height:' + ccD*.32 + 'px;padding:2px;position:absolute;top:' + ccD*.85 + 'px;left:' + ccD*.01 + 'px;font-family:Arial;font-size:' + ccD*.2 + 'px;font-weight:bold;color:#3d95ba;text-align:left;">')
      document.write('<img src="image/hostIcon16x30.jpg" style="width:' + ccD*.16 + 'px;height:' + ccD*.3 + 'px;margin-right:' + ccD*.1 + 'px;margin-left:4px;margin-bottom:0px;line-height:' + ccD + 'px;" />')
      document.write('<span style="position:absolute;top:' + ccD*.07 + 'px;left:' + ccD*.3 + 'px;color:#70a8c9;">ATTRIBUTE MATRIX:  </span>')
      document.write('<span style="position:absolute;top:' + ccD*.07 + 'px;left:' + ccD*2.4 + 'px;color:#7799aa;">ATRBT GROUP 01 </span>')
      document.write('<span style="position:absolute;top:' + ccD*.07 + 'px;left:' + ccD*4.2 + 'px;color:#778899;">- ' + hostID + '</span>')
    </script>
        
    </div>
    
    <!-- Sets hotspots on transparent gif overlay, use for selecting attribute number -->
    <!-- Contents are written dynamically by JavaScript after page is drawn -->
    <div id="mapbox"><map name="grammap"></map></div>
    
    <script language="JavaScript">
      document.write('<img src="image/xp.gif" style="width:' + canvH + 'px;height:' + canvH + 'px;position:absolute;top:0px;left:0px;" usemap="#grammap" />')
      document.write('<a href="#" onClick="cancelMods();"><img src="image/button-cancel.jpg" style="width:' + ccD + 'px;height:' + ccD*.27 + 'px;position:absolute;top:' + ccD*1.35 + 'px;left:' + ccD*.1 + 'px;" /></a>')
      document.write('<a href="#" onClick="echoHost();"><img src="image/button-modify.jpg" style="width:' + ccD + 'px;height:' + ccD*.27 + 'px;position:absolute;top:' + ccD*1.35 + 'px;left:' + ccD*1.2 + 'px;" /></a>')
      document.write('</div>')  // End of Canvas Div holding attribute matrix
	  document.write('<div id="side" style="background-color:#000000;position:absolute;top:0px;left:' + (canvH+1) + 'px;">')
      document.write('<form id="polyform" name="polyform">')
      document.write('	<input type="range" orient="vertical" min="0" max="100" value="50" id="testRange" onmousemove="setVal(this.value)" style="height:' + ocD + 'px;width:20px;position:absolute;top:' + ccD*3 + 'px;left:' + ccD*1.87 + 'px;" /><br />')
      document.write('</form>')
      
      document.write('<div id="barContainer" style="background-color:#000000;border:0px;width:' + ccD*.44 + 'px;height:' + (ocD+50) + 'px;position:absolute;top:' + ccD*3 + 'px;left:' + ccD*1.5 + 'px;">')   // Was: 'px;left:' + ccD*.68 + 'px;">')
    
                function byte2Hex(n)
                  {
                    var nybHexString = "0123456789ABCDEF";
                    return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);
                  }
        
                var startBarsT=15  // Was 8; Moves blocks AND numbers down with higher number
                var startBarsL=0   // Was 3; Higher = righter forblocks and numbers
                var bgColorStartR=79  // 4fd8de to 356782    D26, D113, D92  decr. 1, 6, 5; determined from photoshop derived color differences divided by numSteps
                var bgColorStartG=170  //
                var bgColorStartB=200  //
    
                for (attBars=numSteps+1;attBars>0;attBars-=1) {
                    var output = '#' + byte2Hex(bgColorStartR) + byte2Hex(bgColorStartG) + byte2Hex(bgColorStartB);
                    //alert(output)
                    dashOffset = 5   //4 + Math.round(ccD/20)
                    document.write("<div id='barsON_" + attBars + "' style='background-color:" + output + ";border:1px solid #888888;width:" + ccD*.22 + "px;height:" + ccD*.22 + "px;position:absolute;top:" + startBarsT + "px;left:" + startBarsL + "px;'><b style='position:relative;top:-" + dashOffset + "px;color:#cccccc;margin-left:" + ccD*.26 + "px;'>-</b><b style='position:relative;color:#888888;margin-left:" + ccD*.30 + "px;top:-5px;font-family:sans-serif;font-size:" + ccD*.22 + "px;'>"+ attBars +"</b></div>")
                    document.write("<div id='mask" + attBars + "' style='background-color:#000000;border:1px solid #888888;width:" + ccD*.22 + "px;height:" + ccD*.22 + "px;position:absolute;top:" + startBarsT + "px;left:" + startBarsL + "px;'><b style='position:relative;top:-" + dashOffset + "px;color:#cccccc;margin-left:" + ccD*.26 + "px;'>-</b><b style='position:relative;color:#888888;margin-left:" + ccD*.30 + "px;top:-5px;font-family:sans-serif;font-size:" + ccD*.22 + "px;'>"+ attBars +"</b></div>")
                    bgColorStartR = Math.max((bgColorStartR - 4),0)
                    bgColorStartG = Math.max((bgColorStartG - 7),0)
                    bgColorStartB = Math.max((bgColorStartB - 6),0)
                    startBarsT = startBarsT + ccD*.35
                }
            </script>
        </div>
        
    <script language="JavaScript">  // This is the menu box at the top right.
      document.write('<div id="menus" name="menus" style="position:absolute;top:55px;left:10px;width:' + ccD*4 + 'px;height:' + ccD*1 + 'px;border:0px solid #066;padding:' + 0 + 'px;">')
    
      document.write('<img class="hostImage" src="image/Hosts/<?= $host_name ?>.jpg" style="clip-path:circle(51%);position:absolute;top:0px;left:-' + ccD*1.1 + 'px;width:' + ccD*1 + 'px;height:' + ccD*1 + 'px;">')
    
      document.write('<a href="index.php" style="color:#7799aa;font-family:Arial, Helvetica, sans-serif;font-size:' + ccD*.12 + 'px;">MAIN MENU</a><br />')
      document.write('<span style="color:#7799aa;font-family:Arial, Helvetica, sans-serif;font-size:' + ccD*.12 + 'px;">Load Host: <input type="text" onkeyup="findHost(this.value)" size="12" style="color:#7799aa;border: 1px solid #555555;background:#223344;font-family:Arial, Helvetica, sans-serif;font-size:' + ccD*.12 + 'px;"></span><br /><span id="hostList" style="color:#7799aa;font-family:Arial, Helvetica, sans-serif;font-size:' + ccD*.12 + 'px;"></span> <br />')
      document.write('<span style="color:#7799aa;font-family:Arial, Helvetica, sans-serif;font-size:' + ccD*.12 + 'px;">Current Host: <?= $hFname ?>, <?= $hSex ?>, <?= $hAge ?>,  <?= $hCurrJob ?></span><br />')
      //document.write('<p><a href="#" onClick="showHide()">Show Matrix</a> or <a href="#" onClick="getCurrentFile()">Show File</a></p>')
	  document.write('</div>')
    </script>
    
    <script language="JavaScript">
        document.write('<div id="selAtt" style="color:#667777;position:absolute;border:0px;top:' + ccD*8 + 'px;left:-' + ccD*.77 + 'px;width:' + ccD*4 + 'px;height:' + ccD*.2 + 'px;font-family:arial;font-size:' + ccD*.2 + 'px;"></div>') // This is where the rotated attribute label goes
    </script>
    
    </div>
</div>

</body>
</html>