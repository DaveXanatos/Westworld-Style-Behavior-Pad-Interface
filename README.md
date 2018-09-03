# Westworld-Style-Behavior-Pad-Interface
This is a working version of the iconic Behavior Pad "Rose Graph" Interface that the Westworld Techs use to set Host Behavioral Attributes.  It is written in HTML5 using Canvas, CSS, JavaScript and PHP.  It has only been tested in Chrome.  I have a copy running on an Apache server "in the wild" as well as a copy running on Raspberry Pi SBCs that serve as "Host Brains" in my robots.  Those Raspberries are running NGINX server and VSFTPC.  This project allows you to set and save host parameters (reads and writes text files to the server if you have R/W perms).  The parameters file (ATTRIBS.txt) allows you to fully customize the Attribute Names.  The system auto-scales to fit whatever screen size you are using, I've tested on my Android phone (LG V20) up to my 32" monitor.  Feel free to add, customize as you like, just please provide credit to me for the hard work I did to get it to this point, and will continue to do as I add more features.  The eventual goal is reproducing working functionality of all the Behavior Pad Screens we've seen in the awesome HBO series Westworld.

WHAT'S WHAT:

BehaviorPad1_0ax.php:  The main file to use for a standard web-server deployed copy, to be viewed in Chrome.
BehaviorPad1_0pi-ax.php:  The main file to use for a Raspberry Pi deployed copy (IE., embedded controller "Mid-Brain" in the host body), with the Pi running VSFTP and the NGINX server.
index.php:  Just a "home Page" to select between the attribute editor listed above, and some other options for viewing host parameters
phpEnv.php:  This is just an informational screen of server parameters, operating system info, etc.
scandir.php:  Used to list all available host profiles in the HostBuilds/ directory
updateHost.php:  This file is called when you use the MODIFY button in the attribute editor.  This stores teh new host values in the host profile.
uploadHost.php:  This file is what makes the currently displayed host become live in the host body.  It does so by overwriting HostBuilds/ACTIVEHOST.txt
styles.css:  Critical for proper display rendering.

EVERYTHING IS A WORK IN PROGRESS.  I'm always adding something when time (which is hard to come by) permits.  This doesn't work properly in Microsoft Edge, and I don't have time to figure out why.  It works nearly perfectly in Chrome on Windows, and that's good enough for me.  If you find the issue with Edge and want to generate a fix - just let me know.

Issues with Raspberry Pi version:  I do leading edge development on the standard Windows version because it's easier in my coding environment.  The Pi version will always lag a revision or two.  That said, the only real issue with the Pi version currently is the slider to the right of the graph - it's difficult and has taken a number of tricks to get the slider to display vertically and operate correctly in the Pi, and it is the only thing of all of the functions that doesn't scale as nicely as everything else.  Again - if you want to find a fix and advise, just let me know and we'll do the Issue/Pull thing.
