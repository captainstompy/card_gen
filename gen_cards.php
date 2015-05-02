<?php

$suits = array('Heart','Diamond','Shield','Cup','Club','Spade','Bottle','Anchor','Crown');//,'club');
$ranks = array('Ace',2,3,4,5,6,7,8,9,10,'Jack','Queen','King','Bishop','Cardinal','Pope','God');

foreach($suits as $suit) {
	foreach($ranks as $rank) {
		gen_card($suit, $rank);
	}
}

// and gen jokers todo

function gen_card($suit, $rank) {
	$base = new Imagick('images/base.png');
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

	$buffer = 20;
	$main_width = 822;
	$main_height = 1122;
	// make the card
	// put rank text in, first top left, then rotated bottom right
	$base->compositeImage($rank_text, Imagick::COMPOSITE_DEFAULT, $buffer + ($sm_width-$rank_width)/2, $buffer);
	$rank_text->rotateImage("none",180);
	$base->compositeImage($rank_text, Imagick::COMPOSITE_DEFAULT, $main_width - $buffer - $rank_width - ($sm_width - $rank_width)/2, $main_height - $buffer - $rank_height);
	// put suit marker in, first top left, then rotated bottom right
	$base->compositeImage($suit_marker, Imagick::COMPOSITE_DEFAULT, $buffer, $rank_height + 2*$buffer);
	$suit_marker->rotateImage("none",180);
	$base->compositeImage($suit_marker, Imagick::COMPOSITE_DEFAULT, $main_width - $buffer - $sm_width, $main_height - $buffer - ($rank_height + $sm_height + $buffer));
	$base->compositeImage($card_main, Imagick::COMPOSITE_DEFAULT, ($main_width - $cm_width)/2, ($main_height - $cm_height)/2);
// TODO - more
	// save
	$base->writeImage("cards/".$suit."_".$rank."_finish.png");
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
	return get_image("Text/".$color."_".$rank."_rank_text.png");
}

function get_suit_marker($suit) {
	$color = getSuitColor($suit);
	return get_image($color."/".$suit."_small.png");
}

function get_card_main($suit, $rank) {
	if (is_numeric($rank)) { 
		$suit_graphic = get_suit_graphic($suit);
		// TODO - assemble
		$card_main = new Imagick();
		$card_main->newImage(600, 1122, "#FFFFFF");//TODO - what size??
		$card_main->setImageFormat("png");

		$card_height = $card_main->getImageHeight();
		$suit_height = $suit_graphic->getImageHeight();

		$card_width = $card_main->getImageWidth();
		$suit_width = $suit_graphic->getImageWidth();
		// TODO - determine actual nums
		$top_buffer = 240;
		$side_buffer = 50;

		switch($rank) {
// TODO - add flippies to the points
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
	return new Imagick("images/".$image_name);
}
