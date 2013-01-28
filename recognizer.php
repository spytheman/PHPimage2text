<?php
require("image2text.php");
///////////////////////////////////////////

$iname = $argv[1];
list($_chars, $dimensions) = image2text_loadchars();
$img = imagecreatefrompng($iname);
$text = image2text_recognize($img, $dimensions);
echo "=====================\n{$text}\n=====================\n";
