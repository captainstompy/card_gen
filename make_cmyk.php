<?php
foreach(glob("cards_exp/cards/*.png") as $filename) {
	$img = new Imagick($filename);
	$img->transformImageColorspace(Imagick::COLORSPACE_CMYK);
	preg_match('/\/[\w]*.png/',$filename,$newname);
	$img->writeImage("cards_cmyk".$newname[0]);
}
