<?php
foreach(glob("cards/*.png") as $filename) {
	$newimg = new Imagick();
	$newimg->newImage(822+72,1122+72,'#FFFFFF');
	$newimg->setImageFormat('png');
	$img = new Imagick($filename);
	$newimg->compositeImage($img, Imagick::COMPOSITE_DEFAULT, 36, 36);
	$newimg->scaleImage(825,1125);
	$newimg->writeImage("cards_exp/".$filename);
}
