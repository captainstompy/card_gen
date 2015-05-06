<?php

// Command line arguments:
// suits
// ranks
// buffer
// card size
// image location
// image files

function parseArgs() {
	global $argc;
	global $argv;
	$options = array();
	for($i=1;$i<$argc;$i++) {
		$split = explode('=',$argv[$i]);
		if (count($split) != 2) continue;
		// TODO - THIS IS A HACCCCCK
		if (in_array($split[0],array('suits','ranks'))) {
			$split[1] = json_decode($split[1],true);
		} // end hack :P
		$options[$split[0]] = $split[1];
	}
	return $options;
}

// TODO - i don't want this called by anyone that's not a function
function getArg($key) {
	global $options;
	$defaults = array(
		'buffer' => 30,
		'suits' => array('Heart','Diamond','Shield','Cup','Club','Spade','Bottle','Anchor','Crown'),
		'ranks' => array('Ace',2,3,4,5,6,7,8,9,10,'Jack','Queen','King','Bishop','Cardinal','Pope','God'),
		'main_card_width' => 822,
		'main_card_height' => 1122,
		'image_location' => "images/",
		'finished_cards_location' => "cards/",
		'center_offset' => 'none', // note - left or right or none
		'center_image_width' => 600, // note - this is the size of whatever in the image you want to center
		'center_image_height' => 800, // note - this is the size of whatever in the image you want to center
		'scale_width' => 0,
		'scale_height' => 0,
		'debug' => false, // TODO - DONT CHECK THIS IN AS TRUE YO!
		// TODO - figure out how to do templates or something; might be easier with a class structure
		'text_file_name_format' => "",
		'tiny_suit_name_format' => "",
		'main_suit_graphic_name_format' => "",
		'main_suit_big_name_format' => "", // note - this is for aces and facecards
	);
	if (isset($options[$key])) {
		$value = $options[$key];
	} else if (isset($defaults[$key])) {
		$value = $defaults[$key];
	} else {} // TODO this would be an error
	return $value;
}

// the buffer for the text/suit up in the corner
function getIDBuffer() {
	return getArg('buffer');
}

// which suits/ranks to create
function getSuits() {
	return getArg('suits');
}
function getRanks() {
	return getArg('ranks');
}

// card size
function getCardHeight() {
	return getArg('main_card_height');
}

function getCardWidth() {
	return getArg('main_card_width');
}

// this is for the ace/facecard for centering the suit
function getCenterOffset() {
	return getArg('center_offset');
}
function getCenterImageSize() {
	return array('w'=>getArg('center_image_width'), 'h'=>getArg('center_image_height'));
}

function getScale() {
	$scale = array('w'=>getArg('scale_width'), 'h'=>getArg('scale_height'));
	if ($scale['w'] != 0 || $scale['h'] != 0) return $scale;
	return false;
}

// the main directory where the card source images are kept
function getImageLocation() {
	return getArg('image_location');
}

// the directory in which to save the completed cards
function getSaveLocation() {
	return getArg('finished_cards_location');
}

// good for figuring out placement
function getDebugValue() {
	return getArg('debug');
}

$options = parseArgs();

$suits = getSuits();
$ranks = getRanks();

foreach($suits as $suit) {
	foreach($ranks as $rank) {
		gen_card($suit, $rank);
	}
}

// and gen jokers todo

function gen_card($suit, $rank) {
	$base = new Imagick(getImageLocation().'base.png');
	// get the component imgs
	$rank_text = get_rank_text($suit, $rank);
	$suit_marker = get_suit_marker($suit);
	$card_main = get_card_main($suit, $rank);
	// determine sizes for ease
	$rank_height = $rank_text->getImageHeight();
	$sm_height = $suit_marker->getImageHeight();
	$rank_width = $rank_text->getImageWidth();
	$sm_width = $suit_marker->getImageWidth();

	$cm_height = $card_main->getImageHeight();
	$cm_width = $card_main->getImageWidth();
// these are for center image placement; in most cases, it is the same as the full width/height, but when offsetting, it's the size of the portion of the image we want centered
	$cim_width = $cm_width;
	$cim_height = $cm_height;

	$buffer = getIDBuffer();
	$main_width = getCardWidth();
	$main_height = getCardHeight();

	// do we offset the center image?
	$offset_dir = getCenterOffset();
	$w_offset = 0;
	$h_offset = 0;
	// $offset_dir is the direction we WANT TO MOVE IT!!
	if ($offset_dir != "none") {
		$center_size = getCenterImageSize();
		$centering_width = $center_size['w'];
		$centering_height = $center_size['h'];
		if (strpos($offset_dir, "left") !== false) {
			// means the whole image should be shifted left
			$w_offset = $cm_width - $centering_width;
			$cim_width = $centering_width; // this is when we want it to be not the full width
		} else if (strpos($offset_dir, "right") !== false) {
			// means the whole image should be shifted right
			$cim_width = $centering_width; // this is when we want it to be not the full width
		}

		if (strpos($offset_dir, "top") !== false) {
			$cim_height = $centering_height;
		} else if (strpos($offset_dir, "bottom") !== false) {
			$h_offset = -$cm_height + $centering_height;
			$cim_height = $centering_height;
		}
	}

	// make the card; start with putting on the middle image
	$base->compositeImage($card_main, Imagick::COMPOSITE_DEFAULT, ($main_width - $cim_width)/2 - $w_offset, ($main_height - $cim_height)/2 - $h_offset);
	// put rank text in, first top left, then rotated bottom right
	$base->compositeImage($rank_text, Imagick::COMPOSITE_DEFAULT, $buffer + ($sm_width-$rank_width)/2, $buffer);
	$rank_text->rotateImage("none",180);
	$base->compositeImage($rank_text, Imagick::COMPOSITE_DEFAULT, $main_width - $buffer - $rank_width - ($sm_width - $rank_width)/2, $main_height - $buffer - $rank_height);
	// put suit marker in, first top left, then rotated bottom right
	$base->compositeImage($suit_marker, Imagick::COMPOSITE_DEFAULT, $buffer, $rank_height + 2*$buffer);
	$suit_marker->rotateImage("none",180);
	$base->compositeImage($suit_marker, Imagick::COMPOSITE_DEFAULT, $main_width - $buffer - $sm_width, $main_height - $buffer - ($rank_height + $sm_height + $buffer));

	if (getDebugValue()) {
		// Debugging lines!!
		$draw = new ImagickDraw();
		$draw->setStrokeWidth(1);
		$draw->setStrokeColor("#888888");
		// center
		$draw->line($main_width/2, 0, $main_width/2, $main_height);
		$draw->line(0, $main_height/2, $main_width, $main_height/2);
		// where it is
		$draw->line(($main_width-$cim_width)/2-$w_offset,0,($main_width-$cim_width)/2-$w_offset, $main_height);
		$draw->line(0, ($main_height - $cim_height)/2 - $h_offset, $main_width, ($main_height - $cim_height)/2 - $h_offset);
		// where it should have been
		$draw->setStrokeColor("#00FF00");
		$draw->line(($main_width-$cm_width)/2,0,($main_width-$cm_width)/2, $main_height);
		$draw->line(($main_width-$cm_width)/2+$cm_width,0,($main_width-$cm_width)/2+$cm_width, $main_height);
		$draw->line(0, ($main_height - $cm_height)/2, $main_width, ($main_height - $cm_height)/2);
		$draw->line(0, ($main_height - $cm_height)/2+$cm_height, $main_width, ($main_height - $cm_height)/2+$cm_height);
		$base->drawImage($draw);
	}

	// save
	$base->writeImage(getSaveLocation().$suit."_".$rank."_finish.png");
	// TODO - return error if there is one?
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

function get_rank_text($suit, $rank) {
	$color = getSuitColor($suit);
	$img = get_image("Text/".$color."_".$rank."_rank_text.png");
	$img->scaleImage(0,75);
	return $img;
	//return get_image("Text/".$color."_".$rank."_rank_text.png");
}

function get_suit_marker($suit) {
	$color = getSuitColor($suit);
	$img = get_image($color."/".$suit."_small.png");
	$scale = getScale();
	if (is_array($scale)) {
		$img->scaleImage($scale['w'], $scale['h']);
	}
	return $img;
}

function get_card_main($suit, $rank) {
	if (is_numeric($rank)) { 
		$suit_graphic = get_suit_graphic($suit);
		// assemble
		$card_main = new Imagick();
		$background = "#FFFFFF";
		if (getDebugValue()) $background = "#FFFF99";
		$card_main->newImage(600, 1122, $background);// TODO - these sizes shouldn't be hard coded >.>
		$card_main->setImageFormat("png");

		$card_height = $card_main->getImageHeight();
		$suit_height = $suit_graphic->getImageHeight();

		$card_width = $card_main->getImageWidth();
		$suit_width = $suit_graphic->getImageWidth();
		// TODO - determine actual nums
		$top_buffer = 240;
		$side_buffer = 50;

		switch($rank) {
			case 2:
				$points = array(
					array('x'=>($card_width-$suit_width)/2, 'y'=>$top_buffer),
					array('x'=>($card_width-$suit_width)/2, 'y'=>$card_height-$top_buffer-$suit_height, 'flip'=>true)
				);
				break;
			case 3:
				$points = array(
					array('x'=>($card_width-$suit_width)/2, 'y'=>$top_buffer), //TODO - do we want to do it relatively always??
					array('x'=>($card_width-$suit_width)/2, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>($card_width-$suit_width)/2, 'y'=>$card_height-$top_buffer-$suit_height, 'flip'=>true)
				);
				break;
			case 4:
				$points = array(
					array('x'=>$side_buffer,'y'=>$top_buffer),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$top_buffer),
					array('x'=>$side_buffer,'y'=>$card_height-$top_buffer-$suit_height, 'flip'=>true),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$card_height-$top_buffer-$suit_height)
				);
				break;
			case 5:
				$points = array(
					array('x'=>$side_buffer,'y'=>$top_buffer),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$top_buffer),
					array('x'=>($card_width-$suit_width)/2, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$side_buffer,'y'=>$card_height-$top_buffer-$suit_height, 'flip'=>true),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$card_height-$top_buffer-$suit_height)
				);
				break;
			case 6:
				$points = array(
					array('x'=>$side_buffer,'y'=>$top_buffer),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$top_buffer),
					array('x'=>$side_buffer, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$card_width-$side_buffer-$suit_width, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$side_buffer,'y'=>$card_height-$top_buffer-$suit_height, 'flip'=>true),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$card_height-$top_buffer-$suit_height)
				);
				break;
			case 7:
				$points = array(
					array('x'=>$side_buffer,'y'=>$top_buffer),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$top_buffer),
					array('x'=>($card_width-$suit_width)/2, 'y'=>($top_buffer+($card_height/2 - $suit_height/2))/2),
					array('x'=>$side_buffer, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$card_width-$side_buffer-$suit_width, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$side_buffer,'y'=>$card_height-$top_buffer-$suit_height, 'flip'=>true),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$card_height-$top_buffer-$suit_height)
				);
				break;
			case 8:
				$points = array(
					array('x'=>$side_buffer,'y'=>$top_buffer),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$top_buffer),
					array('x'=>($card_width-$suit_width)/2, 'y'=>($top_buffer+($card_height/2 - $suit_height/2))/2),
					array('x'=>$side_buffer, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$card_width-$side_buffer-$suit_width, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>($card_width-$suit_width)/2, 'y'=>(($card_height-$top_buffer-$suit_height)+($card_height/2 - $suit_height/2))/2, 'flip'=>true),
					array('x'=>$side_buffer,'y'=>$card_height-$top_buffer-$suit_height),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$card_height-$top_buffer-$suit_height)
				);
				break;
			case 9:
				$points = array(
					array('x'=>($card_width-$suit_width)/2, 'y'=>1.5*$top_buffer-$card_height/4+$suit_height/4), // the y is calculated by simplifying D = C-(B-A), where C is the one added in 7, and C = (A+B)/2
					array('x'=>$side_buffer,'y'=>$top_buffer),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$top_buffer),
					array('x'=>($card_width-$suit_width)/2, 'y'=>($top_buffer+($card_height/2 - $suit_height/2))/2),
					array('x'=>$side_buffer, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$card_width-$side_buffer-$suit_width, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>($card_width-$suit_width)/2, 'y'=>(($card_height-$top_buffer-$suit_height)+($card_height/2 - $suit_height/2))/2, 'flip'=>true),
					array('x'=>$side_buffer,'y'=>$card_height-$top_buffer-$suit_height),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$card_height-$top_buffer-$suit_height)
				);
				break;
			case 10:
				$points = array(
					array('x'=>($card_width-$suit_width)/2, 'y'=>1.5*$top_buffer-$card_height/4+$suit_height/4), // the y is calculated by simplifying D = C-(B-A), where C is the one added in 7, and C = (A+B)/2
					array('x'=>$side_buffer,'y'=>$top_buffer),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$top_buffer),
					array('x'=>($card_width-$suit_width)/2, 'y'=>($top_buffer+($card_height/2 - $suit_height/2))/2),
					array('x'=>$side_buffer, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>$card_width-$side_buffer-$suit_width, 'y'=>$card_height/2 - $suit_height/2),
					array('x'=>($card_width-$suit_width)/2, 'y'=>(($card_height-$top_buffer-$suit_height)+($card_height/2 - $suit_height/2))/2, 'flip'=>true),
					array('x'=>$side_buffer,'y'=>$card_height-$top_buffer-$suit_height),
					array('x'=>$card_width-$side_buffer-$suit_width,'y'=>$card_height-$top_buffer-$suit_height),
					array('x'=>($card_width-$suit_width)/2, 'y'=>$card_height - $suit_height - (1.5*$top_buffer-$card_height/4+$suit_height/4))
				);
				break;
			default: $points = array(); break;
		}
		foreach($points as $point) {
			if(isset($point['flip']) && $point['flip']) {
				$suit_graphic->rotateImage("none", 180);
			}
			$card_main->compositeImage($suit_graphic, Imagick::COMPOSITE_DEFAULT, $point['x'], $point['y']);
		}
		$suit_graphic = $card_main;
	} else {
		$suit_graphic = get_face_card($suit, $rank);
	}
	return $suit_graphic;
}

function get_suit_graphic($suit) {
	$color = getSuitColor($suit);
	return get_image($color."/".$suit.".png");
}

function get_face_card($suit, $rank) {
	$color = getSuitColor($suit);
	return get_image($color."/".$suit."_".$rank.".png");
}

function get_image($image_name) {
	return new Imagick(getImageLocation().$image_name);
}
