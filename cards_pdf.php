<?php

function cmp($a, $b) {
  $suit_order = array(
    'Red' => 1,
    'Black' => 2,
    'Blue' => 3,
    'Heart' => 4,
    'Diamond' => 5,
    'Shield' => 6,
    'Spade' => 7,
    'Club' => 8,
    'Cup' => 9,
    'Crown' => 10,
    'Bottle' => 11,
    'Anchor' => 12
  );

  $rank_order = array(
    'joker' => 1,
    'G' => 2,
    'P' => 3,
    'C' => 4,
    'B' => 5,
    'K' => 6,
    'Q' => 7,
    'J' => 8,
    '10' => 9,
    '9' => 10,
    '8' => 11,
    '7' => 12,
    '6' => 13,
    '5' => 14,
    '4' => 15,
    '3' => 16,
    '2' => 17,
    'A' => 18,
  );

  $split_a = explode("_", end(explode("/", $a)));
  $split_b = explode("_", end(explode("/", $b)));

  if ($suit_order[$split_a[0]] < $suit_order[$split_b[0]]) {
    return -1;
  } else if ($suit_order[$split_a[0]] > $suit_order[$split_b[0]]) {
    return 1;
  }

  if ($rank_order[$split_a[1]] < $rank_order[$split_b[1]]) {
    return -1;
  } else {
    return 1;
  }
}

$card_images = array();
foreach(glob("cards_cmyk/*.png") as $filename) {
	$card_images[] = $filename;
}
usort($card_images, "cmp");

$images = array();
foreach($card_images as $card_image) {
	$images[] = "card_back.png";
	$images[] = $card_image;
}
/* for test pdf:
$images = array(
	'card_back.png',
	"cards_cmyk/Anchor_A_finish.png",
	'card_back.png',
	"cards_cmyk/Shield_G_finish.png",
    "card_back.png",
    "cards_cmyk/Spade_10_finish.png"
);
*/

$img = new Imagick();
$img->setResolution(300,300); // TODO - is correct??
$img->readImages($images);
$img->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
$img->setImageFormat("pdf");


$img->writeImages('card_test.pdf',true);
