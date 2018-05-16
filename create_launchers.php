<?php

if(!isset( $argv[1]) ) {
	echo "No path specify". "\n";
	exit;
}

$game_path=$argv[1];


$content="";
$files = scandir($game_path);
foreach($files as $file) {

  	//do your work here
	if(is_dir("$game_path/$file")){
		if( file_exists("$game_path/$file/launcher.xml") ) {
			$content.=file_get_contents("$game_path/$file/launcher.xml");
		}
	}
}



$content='<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<launchers>
' . $content .
'</launchers>';

file_put_contents("launchers.xml", $content);