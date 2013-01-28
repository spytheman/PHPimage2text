<?php
require("image2text.php");
///////////////////////////////////////////

$chardescriptions = array(
   "cuts/0.png"     => array("0", ),
   "cuts/1.png"     => array("1", ),
   "cuts/2.png"     => array("2", ),
   "cuts/3.png"     => array("3", ),
   "cuts/4.png"     => array("4", ),
   "cuts/5.png"     => array("5", ),
   "cuts/6.png"     => array("6", ),
   "cuts/7.png"     => array("7", ),
   "cuts/8.png"     => array("8", ),
   "cuts/9.png"     => array("9", ),
   "cuts/comma.png" => array(",", ),
   "cuts/dot.png"   => array(".", ),
   );

$iname = $argv[1];
$dimensions = image2text_loadchars($chardescriptions);
$img = imagecreatefrompng($iname);
$text = image2text_recognize($img, $dimensions);
echo "=====================\n{$text}\n=====================\n";
