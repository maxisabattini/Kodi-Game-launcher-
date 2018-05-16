<?php

if(!isset( $argv[1]) ) {
	echo "No path specify". "\n";
	exit;
}

$game_path=$argv[1];

$pi= pathinfo($game_path);

$name = $pi["filename"];

echo "Searching for: " . $name . "\n";

$xml=file_get_contents("http://thegamesdb.net/api/GetGamesList.php?name=".urlencode($name)."&platform=pc");

$games=simplexml_load_string($xml);

$index=0;
foreach($games->Game as $i => $node){
	echo $index;
	echo "\t: ";
	echo $node->GameTitle;
	echo "\n";
	$index++;
}

if($index===0){
	echo "Nothing found". "\n";
	exit;
}

$number = readline("Enter a number: ");

$xml=file_get_contents("http://thegamesdb.net/api/GetGame.php?id=".$games->Game[(int)$number]->id);

$the_game=simplexml_load_string($xml);

echo "Saving info for: " . $name . "\n";

save_launcher($the_game->Game);

function str_to_hex($string) {
	$hexstr = unpack('H*', $string);
	return array_shift($hexstr);
}

function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}

function save_launcher( $node ) {

	global $game_path;

	//Saving cover
	file_put_contents("$game_path/cover.jpg", file_get_contents("http://thegamesdb.net/banners/_gameviewcache/".$node->Images->boxart ));
	$thumb="$game_path/cover.jpg";

	//Saving fanart
	$fanart="";
	if(isset($node->Images->fanart[0])) {
		file_put_contents("$game_path/fanart.jpg", file_get_contents("http://thegamesdb.net/banners/".$node->Images->fanart[0]->original ));
		$fanart="$game_path/fanart.jpg";
	}

	$str="
		<launcher>
			<id>".
			str_to_hex($node->GameTitle)
			."</id>
			<name>".
			$node->GameTitle		
			."</name>
			<application>".
			$game_path . "\launcher.lnk"
			."</application>
			<args></args>
			<rompath></rompath>

			<thumbpath>".
			$game_path 
			."</thumbpath>
			<fanartpath>".
			$game_path 
			."</fanartpath>

			<trailerpath></trailerpath>
			<custompath></custompath>
			<romext></romext>
			<platform>IBM PC Compatible</platform>

			<thumb>".
			$thumb
			."</thumb>
			<fanart>".
			$fanart
			."</fanart>

			<genre>Action</genre>
			<release>".
			$node->ReleaseDate
			."</release>
			<publisher>".
			$node->Publisher
			."</publisher>
			<launcherplot> ".
			$node->Overview
			."</launcherplot>
			<finished>false</finished>
			<minimize>false</minimize>
			<lnk>true</lnk>
			<roms>
			</roms>
		</launcher>
	";

	file_put_contents($game_path ."/launcher.xml", $str);
}