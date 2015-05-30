<?php
$image = new Imagick();
$image->newImage(825,1125,"#FFFFFF");
$image->setImageFormat("png");

// add in the suits in a circle!
$suits = array(
	getSuitImg("Heart"),
	getSuitImg("Club"),
	getSuitImg("Bottle"),
	getSuitImg("Diamond"),
	getSuitImg("Spade"),
	getSuitImg("Anchor"),
	getSuitImg("Shield"),
	getSuitImg("Cup"),
	getSuitImg("Crown")
);

$main_height = $image->getImageHeight();
$main_width = $image->getImageWidth();

$line_width = 8;

$draw = new ImagickDraw();
$draw->setStrokeOpacity(1);
$draw->setStrokeWidth($line_width);
$draw->setFillColor("none");

$border = 10;
$borderplus = 4;
$colours = array("#C01F25", "#000000", "#0B6292");
foreach($colours as $key => $c) {
	$draw->setStrokeColor($c);
	$draw->line(0,($key+1)*$borderplus*$border+36, $main_width,($key+1)*$borderplus*$border+36);
	$draw->line(0, $main_height-($key+1)*$borderplus*$border-36, $main_width, $main_height-($key+1)*$borderplus*$border-36);
}
/* TODO - this is something weird
$start = array('x'=>4*$border+3*$line_width,'y'=>4*$border+3*$line_width);
$points = array('x'=>$start['x'], 'y'=>$start['y']);
foreach($suits as $suit) {
	if ($suit->getImageWidth() + $points['x'] >= $main_width - $start['x']) break;
	compImg($image, $suit, $points);
	$points['x'] += $border+$suit->getImageWidth();
}
$image->writeImage('card_back_test_alt.png');
die();*/
// uncomment these lines for a reference grid for the center
//$draw->line(406,0,406,1122);
//$draw->line(0,561,812,561);

$image->drawImage($draw);

$radius = 200;
// TODO - suits may not all be exactly the same
$suit = $suits[0];
$suit_height = $suit->getImageHeight();
$suit_width = $suit->getImageWidth();

$suit1 = $suits[0];
$suit2 = $suits[1];
$suit3 = $suits[2];
$suit4 = $suits[3];
$suit5 = $suits[4];
$suit6 = $suits[5];
$suit7 = $suits[6];
$suit8 = $suits[7];
$suit9 = $suits[8];

$o = array('x'=>$main_width/2,'y'=>$main_height/2);
$conv = 2*pi()/360;
$sin40 = sin(40*$conv);
$sin80 = sin(80*$conv);
$sin30 = sin(30*$conv);
$sin70 = sin(70*$conv);
$a1 = array('x'=>$main_width/2-$suit_width/2,'y'=>$main_height/2-$radius-$suit_height);
$a2 = transform($o, $a1, 40*$conv);
$a3 = transform($o, $a1, 80*$conv);
$a4 = transform($o, $a1, 120*$conv);
$a5 = transform($o, $a1, 160*$conv);
$a6 = transform($o, $a1, 200*$conv);
$a7 = transform($o, $a1, 240*$conv);
$a8 = transform($o, $a1, 280*$conv);
$a9 = transform($o, $a1, 320*$conv);

$s2 = $suit2->getImageHeight();
$s3 = $suit3->getImageHeight();
$s4 = $suit4->getImageHeight();
$s5 = $suit5->getImageHeight();
$t6 = $suit6->getImageWidth();
$t7 = $suit7->getImageWidth();
$t8 = $suit8->getImageWidth();
$t9 = $suit9->getImageWidth();

$suit2->rotateImage("none",40);
$suit3->rotateImage("none",80);
$suit4->rotateImage("none",120);
$suit5->rotateImage("none",160);
$suit6->rotateImage("none",200);
$suit7->rotateImage("none",240);
$suit8->rotateImage("none",280);
$suit9->rotateImage("none",320);

$s = $suit1->getImageHeight();
$t = $suit1->getImageWidth();

$points = array(
	$a1,
	array('x'=>$a2['x']-$s2*$sin40, 'y'=>$a2['y']),
	array('x'=>$a3['x']-$s3*$sin80, 'y'=>$a3['y']),
	array('x'=>$a4['x']-$suit4->getImageWidth(), 'y'=>$a4['y']-$s4*$sin30),
	array('x'=>$a5['x']-$suit5->getImageWidth(), 'y'=>$a5['y']-$s5*$sin70),
	array('x'=>$a6['x']-$t6*$sin70, 'y'=>$a6['y']-$suit6->getImageHeight()),
	array('x'=>$a7['x']-$t7*$sin30, 'y'=>$a7['y']-$suit7->getImageHeight()),
	array('x'=>$a8['x'], 'y'=>$a8['y']-$t8*$sin80),
	array('x'=>$a9['x'], 'y'=>$a9['y']-$t9*$sin40)
);
foreach($points as $key => $point) {
	compImg($image, $suits[$key], $point); // TODO - make sure the suits are actually turned!
}

$image->transformImageColorspace(Imagick::COLORSPACE_CMYK);

$image->writeImage("card_back.png");

function compImg($image, $suit, $points) {
	$image->compositeImage($suit, Imagick::COMPOSITE_DEFAULT, $points['x'], $points['y']);
}

// pass in $deg as rad
function transform($o, $a1, $deg) {
	$initial = array('x'=>$a1['x']-$o['x'], 'y'=>$a1['y']-$o['y']);
	$transform = array('x'=>$initial['x']*cos($deg)-$initial['y']*sin($deg), 'y'=>$initial['x']*sin($deg)+$initial['y']*cos($deg));
	$complete = array('x'=>$transform['x']+$o['x'],'y'=>$transform['y']+$o['y']);
	return $complete;
}

function getSuitImg($suit) {
	$color = getSuitColor($suit);
	return new Imagick("images/".$color."/".$suit."_small.png");
}
function getSuitColor($suit) {
	switch ($suit) {
		case 'Diamond':
		case 'Heart':
		case 'Shield':
			$color = "Red";
			break;
		case 'Club':
		case 'Spade':
		case 'Cup':
			$color = "Black";
			break;
		case 'Bottle':
		case 'Anchor':
		case 'Crown':
		default:
			$color = "Blue";
			break;
	}
	return $color;
}
?>
