<?php

function image2text_showshape($s, $k=0){
    list($x1,$y1,$x2,$y2,$sw,$sh) = $s;
    echo "SHAPE [$k]: [$x1,$y1] , [$x2,$y2] : {$sw}x{$sh}  | ";
}

function image2text_showshapes($shapes){
    foreach($shapes as $sk=>$sv){
        image2text_showshape($sv,$sk);
        echo "\n";
    }
}

function image2text_loadchars($chardescriptions=array()){
    $chars = array();
    $dimension2chars = array();
    foreach($chardescriptions as $cname => $cv){
        $cimg = imagecreatefrompng($cname);
        $isize=getimagesize($cname);
        $iw=$isize[0];
        $ih=$isize[1];
        $wh = "{$iw}x{$ih}";
        if(!isset($dimension2chars[ $wh ])){            $dimension2chars[$wh] = array();        }    $dimension2chars[$wh][] = array($cimg, $cv[0]);
    }
    return $dimension2chars;
}

function image2text_line2shapes($img, $osx, $osy, $oex, $oey){
    $maxx = imagesx($img);
    $maxy = imagesy($img);
    $sx = max($osx,0);
    $sy = max($osy,0);
    $ex = min($oex,$maxx);
    $ey = min($oey,$maxy);
    $oldchangex=0;
    $old_is_clearverticalline=false;
    $shapes = array();
    for($x=$sx; $x<$ex; $x++){
        $is_clearverticalline = true;
        for($y=$sy; $y<$ey; $y++){
            $p = imagecolorat($img, $x, $y);
            if($p){
                $is_clearverticalline = false;
                break;
            }
        }
        if($is_clearverticalline != $old_is_clearverticalline){
            if($is_clearverticalline){
                $shapes[]=array(
                                /*x1,y1 */ $oldchangex, 0,
                                /*x2,y2 */ $x, 0,
                                /*w,h   */ 0, 0,
                                );
            }
            $oldchangex = $x;
        }
        $old_is_clearverticalline = $is_clearverticalline;
    }

    foreach($shapes as $sk=>&$sv){
        //    if($sk==1)break;
        $xstart = $sv[0];
        $xend = $sv[2];
        $oldchangey=0;
        $old_is_clearline=false;
        for($y=$sy; $y<$ey; $y++){
            $is_clearline=true;
            for($x=$xstart; $x<$xend; $x++){
                $p = imagecolorat($img, $x, $y);
                //            echo "$sk: [$x,$y] : $p\n";
                if($p){
                    $is_clearline = false;
                    break;
                }
            }
            if($is_clearline != $old_is_clearline){
                if($is_clearline){
                    $sv[1]=$oldchangey;
                    $sv[3]=$y;
                    $sv[4] = $sv[2] - $sv[0]; /*w*/
                    $sv[5] = $sv[3] - $sv[1]; /*h*/
                }
                $oldchangey = $y;
            }
            $old_is_clearline = $is_clearline;
        }

    }

    return $shapes;
}

function image2text_recognizeshape($s, $img, $dimensions){
    list($x1,$y1,$x2,$y2,$sw,$sh) = $s;
    $swsh = "{$sw}x{$sh}";
    if(!isset($dimensions[$swsh])){
        return array("", 1);
    }
    $optimalchar = "";
    $optimalsdd = 999999999;
    $allchars = $dimensions[$swsh];
    foreach($allchars as &$c){
        $timg = $c[0];
        $char = $c[1];
        $sddr = 0;
        $sddg = 0;
        $sddb = 0;
        for($sx=$x1, $tx=0; $sx<$x2; $sx++, $tx++){
            for($sy=$y1, $ty=0; $sy<$y2; $sy++, $ty++){
                $sp = imagecolorat($img, $sx, $sy);
                $sr = ($sp >> 16) & 0xFF;
                $sg = ($sp >> 8) & 0xFF;
                $sb = $sp & 0xFF;

                $tp = imagecolorat($timg, $tx, $ty);
                $tr = ($tp >> 16) & 0xFF;
                $tg = ($tp >> 8) & 0xFF;
                $tb = $tp & 0xFF;

                $dr = ($sr - $tr);
                $ddr = $dr*$dr;
                $sddr += $ddr;

                $dg = ($sg - $tg);
                $ddg = $dg*$dg;
                $sddg += $ddg;

                $db = ($sb - $tb);
                $ddb = $db*$db;
                $sddb += $ddb;
            }
        }
        $sdd = $sddr + $sddg + $sddb;
        if($sdd <= $optimalsdd){
            $optimalsdd = $sdd;
            $optimalchar = $char;
        }
    }
    return array($optimalchar, 0);
}

function image2text_recognizeline($line_of_shapes, $img, $dimensions){
    $res = "";
    foreach($line_of_shapes as $s){
        list($c, $e) = image2text_recognizeshape($s, $img, $dimensions);
        if(!$e){
            $res .= $c;
        }
    }
    return $res;
}

function image2text_recognize($img, $dimensions){
    list($w,$h) = array(imagesx($img), imagesy($img));

    $linespacing = 7;
    $lineheight = 13;
    $fullheight = $linespacing + $lineheight;

    $nlines=0;
    while($nlines * $fullheight < $h)$nlines++;
    $lines = array();
    for($i=0;$i<$nlines;$i++){
        $sx = 0;
        $sy = ($i*$fullheight);
        $ex = $w;
        $ey = (($i+1)*$fullheight);
        $shapes = image2text_line2shapes($img, $sx, $sy, $ex, $ey);
        $line = image2text_recognizeline($shapes, $img, $dimensions);
        $lines[]=$line;
    }
    $res = join("\n", $lines);
    return $res;
}

