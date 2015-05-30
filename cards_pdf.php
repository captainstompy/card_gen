<?php
$images = array();
foreach(glob("cards_cmyk/*.png") as $filename) {
	$images[] = "card_back.png";
	$images[] = $filename;
}
//$images = array('card_back.png',"cards/Spade_A_finish.png");
$images = array(
	'card_back.png',
	"cards_cmyk/Anchor_A_finish.png",
	'card_back.png',
	"cards_cmyk/Shield_G_finish.png",
    "card_back.png",
    "cards_cmyk/Spade_10_finish.png"
);

$img = new Imagick();
$img->setResolution(300,300); // TODO - is correct??
$img->readImages($images);
$img->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
$img->setImageFormat("pdf");


$img->writeImages('card_test.pdf',true);
