﻿<?php 
ini_set('display_errors', 1);
error_reporting(R_ALL & ~E_NOTICE);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Host Behavior Pad Control Interface</title>
<!-- https://youtu.be/L6EatojX3SI -->
<!-- ****************** TO DO LIST **************************

Set slider to report 0-100 in small white text, with 0-20 steps displaying in large text, and attrib number in white small above that

Set system to save a profile

-->

<link rel="stylesheet" type="text/css" href="styles.css">

</head>

<body bgcolor="#000000" onload="draw(); buildMap(); test2(50);">

<script language="JavaScript">
<!-- //
window.onresize = function(){ location.reload(); }

if ('ontouchstart' in window) {
	alert("TouchScreen")
}
	
var winWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
var winHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

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
		
function loadFile(o) {	
	alert(o.target);
	var fr = new FileReader();
	fr.onload = function(e)
		{
			showDataFile(e, o);
		};
	fr.readAsText(o.files[0]);
}

function showDataFile(e, o) {
	//document.getElementById("data").innerText = e.target.result;
	alert(e.target.result);
}

function loadProfile(which) {
	document.getElementById("activeAttrib").value = 0
	var pxy = [<?php 
	$data = file('hostProfile_01.txt');     // Reads each line of file into an addressable array $data(0), $data(1)...
	$n = count($data);                      // Gets line count in $data
	for ($v = 1; $v <= $n; $v++) {
		if ($v < $n) {
			print '	"'.trim($data[$v-1]).'",';  // Add ."\n" to make each show on its own line
		} else {
			print '	"'.trim($data[$v-1]).'"';
		}
	}?>];

	for (i=0;i<16;i++) {
		inX = "p" + i + "x"
		inY = "p" + i + "y"
		var useVal = pxy[i].split(" ")
		//alert(inX + " " + useVal[0] + " " + inY + " " + useVal[1])
		document.getElementById(inX).value = useVal[0];
		document.getElementById(inY).value = useVal[1];
	}
	test2(100)
}

function echoHost() {
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

// Set up attribute Array - Note they start at 6:00 and build counterclockwise.
var attribute = [<?php 
	$data = file('ATTRIBS.txt');     // Reads each line of file into an addressable array $data(0), $data(1)...
	$n = count($data);                      // Gets line count in $data
	for ($v = 1; $v <= $n; $v++) {
		print '"'.trim($data[$v-1]).'",';
	}?>];	

// Set up display parameters      // <<<<<<<<<<<<<<<<<<<<<<<< PRIME CANDIDATE FOR PHP-IFYING
var canvW = winHeight  // Will be set to screen Height: winHeight, when I make this fully scalable and elastic
var canvH = winHeight
var icX=Math.round(canvW/2) ///Image Center X
var icY=Math.round(canvH/2) ///Image Center Y
var ccD=Math.round(canvH*.09765625)  // Was 100 //Center Circle Diameter, pixels, 1024 * .09765625 = 100
var ocD=Math.round(canvH*.68359375)  // Was 700 //Outer Circle Diameter, pixels, 1024 * 68359375 = 700
var L=16  //# spokes
var numSteps = 19 //# parameter steps

var ccR=ccD/2
var ocR=ocD/2
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

			txt = " " + attribute[attrib_cnt] + " [  ]"

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

			//alert(txt + "\n" + ctxMTW + "\n" + startIMX + "," + startIMY + "," + endIMX + "," + endIMY)
			thesePoints = startIMX + "," + startIMY + "," + endIMX + "," + endIMY
			//alert(txt + "\n" + thesePoints)
			document.getElementById("IMC" + attrib_cnt).value = thesePoints
			//mapPoints[attrib_cnt] = '<area shape="rect" coords="' + thesePoints + '" onclick="setAttrib(' + aC + ')" />'
			//alert(txt + "\n" + mapPoints[attrib_cnt])
			
			ctx.fillText(txt,drwXea,drwYea);

			attrib_cnt = attrib_cnt + 1
		}
	}
}

function draw() {
	var graphColor = "#446670";
	var strokeColor = "#446670"
	ctxfont = ccD*.2 + "px Arial";
	ctxfillStyle = "#70a8c9"; //"#668891";
	var canvas = document.getElementById('hostgram');
	//Draw concentric circles of hostgram, spokes, etc
	drawGram(graphColor,strokeColor,ctxfont,ctxfillStyle,canvas);

	var graphColor = "#003333";
	var strokeColor = "#114450"
	ctxfont = ccD*.2 + "px Arial";
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
	//alert(n);
	document.getElementById("selAtt").innerHTML = attribute[n];  // Display selected attribute name
	document.getElementById("activeAttrib").value = n;           // Set hidden input activeAttrib.value to be the selected attribute number
	//test2(0)
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

function test2(attVal) {  //attVal = 0 to 100; reads selected attrib (activeAttrib.value), sets the 0-100 value into the hidden input for the selected value, then converts 0 ro 100 value to pixel coords to clip the polygon
	whichAttrib = document.getElementById("activeAttrib").value   // Determine which attribute is active
	if (whichAttrib == "" || whichAttrib == null) {               // If no attrib selected (initial opening) then set 0 as default selection
		//alert("here")
		document.getElementById("activeAttrib").value = 0
		document.getElementById("selAtt").innerHTML = attribute[0];
		whichAttrib = 0
	}
	thisVal = "activeAttVal_" + whichAttrib                       // Var to hold selected attribute hidden-input name
	document.getElementById(thisVal).value = attVal               // Set that attribute's hidden-input to the adjusted value

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
		if (i < 15) {
			at[i] = thisATline + ", "
		} else {
			at[i] = thisATline
		} 
	}

	at[whichAttrib] = drwYe + "px " + drwXe + "px"
	if (whichAttrib < 15) {
		at[whichAttrib] = at[whichAttrib] + ", "
	}

	document.getElementById("hostgram").style.clipPath = "polygon(" + at[0] + at[1] + at[2] + at[3] + at[4] + at[5] + at[6] + at[7] + at[8] + at[9] + at[10] + at[11] + at[12] + at[13] + at[14] + at[15] + ")";  
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

<div id="datalayer" style="border:0px;width:100px;height:100px;position:absolute;top:0px;left:02px;visibility:hidden;">
	<form name="datastore">  <!-- All point data is written in here from the slider or other controls.  This serves as a RAM function of sorts. -->
		<input type="hidden" name="activeAttrib" id="activeAttrib" />          <!--  Which Attribute is currently selected -->
		<input type="hidden" name="activeAttVal_0" id="activeAttVal_0" />      <!--  Actual 0 - 100 range values for the numbered attribute -->
		<input type="hidden" name="activeAttVal_1" id="activeAttVal_1" />
		<input type="hidden" name="activeAttVal_2" id="activeAttVal_2" /><input type="hidden" name="activeAttVal_3" id="activeAttVal_3" />
		<input type="hidden" name="activeAttVal_4" id="activeAttVal_4" /><input type="hidden" name="activeAttVal_5" id="activeAttVal_5" />
		<input type="hidden" name="activeAttVal_6" id="activeAttVal_6" /><input type="hidden" name="activeAttVal_7" id="activeAttVal_7" />
		<input type="hidden" name="activeAttVal_8" id="activeAttVal_8" /><input type="hidden" name="activeAttVal_9" id="activeAttVal_9" />
		<input type="hidden" name="activeAttVal_10" id="activeAttVal_10" /><input type="hidden" name="activeAttVal_11" id="activeAttVal_11" />
		<input type="hidden" name="activeAttVal_12" id="activeAttVal_12" /><input type="hidden" name="activeAttVal_13" id="activeAttVal_13" />
		<input type="hidden" name="activeAttVal_14" id="activeAttVal_14" /><input type="hidden" name="activeAttVal_15" id="activeAttVal_15" />
		<input type="hidden" name="p0x" id="p0x" /><input type="hidden" name="p0y" id="p0y" />  <!-- X/Y coordinates corresponding to attribute's 0-100 range value -->
		<input type="hidden" name="p1x" id="p1x" /><input type="hidden" name="p1y" id="p1y" />
		<input type="hidden" name="p2x" id="p2x" /><input type="hidden" name="p2y" id="p2y" /><input type="hidden" name="p3x" id="p3x" /><input type="hidden" name="p3y" id="p3y" />
		<input type="hidden" name="p4x" id="p4x" /><input type="hidden" name="p4y" id="p4y" /><input type="hidden" name="p5x" id="p5x" /><input type="hidden" name="p5y" id="p5y" />
		<input type="hidden" name="p6x" id="p6x" /><input type="hidden" name="p6y" id="p6y" /><input type="hidden" name="p7x" id="p7x" /><input type="hidden" name="p7y" id="p7y" />
		<input type="hidden" name="p8x" id="p8x" /><input type="hidden" name="p8y" id="p8y" /><input type="hidden" name="p9x" id="p9x" /><input type="hidden" name="p9y" id="p9y" />
		<input type="hidden" name="p10x" id="p10x" /><input type="hidden" name="p10y" id="p10y" /><input type="hidden" name="p11x" id="p11x" /><input type="hidden" name="p11y" id="p11y" />
		<input type="hidden" name="p12x" id="p12x" /><input type="hidden" name="p12y" id="p12y" /><input type="hidden" name="p13x" id="p13x" /><input type="hidden" name="p13y" id="p13y" />
		<input type="hidden" name="p14x" id="p14x" /><input type="hidden" name="p14y" id="p14y" /><input type="hidden" name="p15x" id="p15x" /><input type="hidden" name="p15y" id="p15y" />
        <input type="text" name="IMC0" id="IMC0" /><input type="text" name="IMC1" id="IMC1" /><input type="text" name="IMC2" id="IMC2" /><input type="text" name="IMC3" id="IMC3" />
        <input type="text" name="IMC4" id="IMC4" /><input type="text" name="IMC5" id="IMC5" /><input type="text" name="IMC6" id="IMC6" /><input type="text" name="IMC7" id="IMC7" />
        <input type="text" name="IMC8" id="IMC8" /><input type="text" name="IMC9" id="IMC9" /><input type="text" name="IMC10" id="IMC10" /><input type="text" name="IMC11" id="IMC11" />
        <input type="text" name="IMC12" id="IMC12" /><input type="text" name="IMC13" id="IMC13" /><input type="text" name="IMC14" id="IMC14" /><input type="text" name="IMC15" id="IMC15" />
	</form>
</div>

<!-- 
<canvas id="bggram" width="1024" height="1024" style="background-color:#000000;position:absolute;top:0px;left:0px;"></canvas>
<canvas id="hostgram" width="1024" height="1024" style="background-color:#002225;position:absolute;top:0px;left:0px;"></canvas>
<div id="midl" style="border:0px;width:100px;height:100px;position:absolute;top:462px;left:462px;z-axis:0;font-family:Arial;font-size:40px;font-weight:bold;color:#FF7700;text-align:center;line-height:100px;"></div>
<div id="midl2" style="border:1px solid #444455;width:800px;height:32px;padding:2px;position:absolute;top:80px;left:50px;font-family:Arial;font-size:20px;font-weight:bold;color:#3d95ba;text-align:left;">
<img src="image/hostIcon16x30.jpg" style="width:16px;height:30px;margin-right:10px;margin-left:4px;margin-bottom:0px;line-height:100px;" />
<span style="position:absolute;top:7px;left:30px;color:#70a8c9;">ATTRIBUTE MATRIX:  </span>
<span style="position:absolute;top:7px;left:240px;color:#7799aa;">ATRBT GROUP 01 </span>
<span style="position:absolute;top:7px;left:420px;color:#778899;">- ID#A0A0000000001</span>
-->

<script language="JavaScript">
  //alert(canvH + " " + ccD + " " + icY)
  placer = icY-(ccD/2)
  document.write('<canvas id="bggram" width="' + canvH + '" height="' + canvH + '" style="background-color:#000000;position:absolute;top:0px;left:0px;"></canvas>')
  document.write('<canvas id="hostgram" width="' + canvH + '" height="' + canvH + '" style="background-color:#002225;position:absolute;top:0px;left:0px;"></canvas>')
  document.write('<div id="midl" style="border:0px;width:' + ccD + 'px;height:' + ccD + 'px;position:absolute;top:' + placer + 'px;left:' + placer + 'px;z-axis:0;font-family:Arial;font-size:' + ccD*.4 + 'px;font-weight:bold;color:#FF7700;text-align:center;line-height:' + ccD + 'px;"></div>')
  document.write('<div id="midl2" style="border:1px solid #444455;width:' + ocD*1.142857 + 'px;height:' + ccD*.32 + 'px;padding:2px;position:absolute;top:' + ccD*.8 + 'px;left:' + ccD*.5 + 'px;font-family:Arial;font-size:' + ccD*.2 + 'px;font-weight:bold;color:#3d95ba;text-align:left;">')
  document.write('<img src="image/hostIcon16x30.jpg" style="width:' + ccD*.16 + 'px;height:' + ccD*.3 + 'px;margin-right:' + ccD*.1 + 'px;margin-left:4px;margin-bottom:0px;line-height:' + ccD + 'px;" />')
  document.write('<span style="position:absolute;top:' + ccD*.07 + 'px;left:' + ccD*.3 + 'px;color:#70a8c9;">ATTRIBUTE MATRIX:  </span>')
  document.write('<span style="position:absolute;top:' + ccD*.07 + 'px;left:' + ccD*2.4 + 'px;color:#7799aa;">ATRBT GROUP 01 </span>')
  document.write('<span style="position:absolute;top:' + ccD*.07 + 'px;left:' + ccD*4.2 + 'px;color:#778899;">- ID#A0A0000000001</span>')
</script>
	
</div>

<!-- Sets hotspots on transparent gif overlay, use for selecting attribute number -->
<!-- Contents are written dynamically by JavaScript after page is drawn -->
<div id="mapbox"><map name="grammap"></map></div>

<script language="JavaScript">
  document.write('<img src="image/xp.gif" style="width:' + canvH + 'px;height:' + canvH + 'px;position:absolute;top:0px;left:0px;" usemap="#grammap" />')
  document.write('<img src="image/button-cancel.jpg" style="width:' + ccD + 'px;height:' + ccD*.27 + 'px;position:absolute;top:' + ccD*1.25 + 'px;left:' + ccD*.6 + 'px;" />')
  document.write('<img src="image/button-modify.jpg" style="width:' + ccD + 'px;height:' + ccD*.27 + 'px;position:absolute;top:' + ccD*1.25 + 'px;left:' + ccD*1.7 + 'px;" />')
</script>

<script language="JavaScript">
  document.write('<div id="side" style="background-color:#000000;position:absolute;top:0px;left:' + (canvH+1) + 'px;">')
  document.write('<form id="polyform" name="polyform">')
  document.write('	<input type="range" orient="vertical" min="0" max="100" value="50" id="testRange" onmousemove="test2(this.value)" style="height:' + ocD + 'px;width:10px;position:absolute;top:' + ccD*1.5 + 'px;left:' + ccD + 'px;" /><br />')
  document.write('</form>')
  document.write('<div id="barContainer" style="background-color:#000000;border:0px;width:' + ccD*.44 + 'px;height:' + (ocD+50) + 'px;position:absolute;top:' + ccD*1.55 + 'px;left:' + ccD*.68 + 'px;">')

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

	<a href="#" style="color:#ffffff;" onClick="loadProfile(1);return false;">Load Profile</a>    
	<!-- <a href="#" style="color:#ffffff;" onClick="readHost('HostBuilds/1.txt');return false;">Read in text file</a> -->
	<a href="#" style="color:#ffffff;" onClick="echoHost();return false;">Display Loaded Values</a>
	<a id='test' href='data:text;charset=utf-8,"+encodeURIComponent("hi")+"' download=hostProfile_01.txt>Save Profile</a><br />  <!--  download attribute allows for change of file extension.  If none given, defaults to .txt -->
	<span style="color:#ffffff;">Select Profile to load:  <input type="file" onchange="loadFile(this)"></span><br />

<!--<div id="selAtt" style="color:#667777;position:absolute;top:743px;left:-50px;width:200px;height:20px;font-family:arial;font-size:20px;"></div>-->
    
	<script language="JavaScript">
		document.write('<div id="selAtt" style="color:#667777;position:absolute;border:0px;top:' + ccD*7.43 + 'px;left:-' + ccD*.5 + 'px;width:' + ccD*2 + 'px;height:' + ccD*.2 + 'px;font-family:arial;font-size:' + ccD*.2 + 'px;"></div>') // This is where the rotated attribute label goes
	</script>

</div>
</body>
</html>