<?php

class Rank {

	private $rank_value;
	private $rank_name;

	private $text;
	private $inverted_text;

	function Rank($value, $name) {
		$this->rank_value = $value;
		$this->rank_name = ($name != null ? $name : $value);

		$this->text = array();
		$this->inverted_text = array();
	}

	public function getText($colour) {
		if (!isset($this->text[$colour])) {
			$this->text[$colour] = CardImage::getImage("Text/".$colour."-".$this->rank_value.".png");
		}
		return $this->text[$colour];
	}

	public function getInvertedText($colour) {
		if (!isset($this->inverted_text[$colour])) {
			$this->inverted_text[$colour] = clone $this->getText($colour);
			$this->inverted_text[$colour]->rotateImage('none', 180);
		}
		return $this->inverted_text[$colour];
	}

	public function getValue() {
		return $this->rank_value;
	}

	// TODO - return img if face card, graphic if not
	public function getMain($suit) {
		if (is_numeric($this->rank_value)) {
			$img = $suit->getGraphic();
		} else {
			$img = CardImage::getImage($suit->getColour()."/".$suit->getName()."_".$this->rank_name.".png");
		}
		return $img;
	}

	// TODO - make this cleaner, more readable, yada.
	// TODO TODO - missing a whole whack of variables that make these points actually work
	public function getPoints($card_width, $card_height, $suit_graphic, $offset) {
		$suit_height = $suit_graphic->getImageHeight();
		$suit_width = $suit_graphic->getImageWidth();

		// TODO - determine actual nums
		$top_buffer = 240;
		$side_buffer = Card::getSideBuffer();

		switch($this->rank_value) {
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
			default:
				$cim_width = $suit_width;
				$cim_height = $suit_height;
				// do we offset the center image?
				$offset_dir = $offset['direction'];
				$w_offset = 0;
				$h_offset = 0;
				// $offset_dir is the direction we WANT TO MOVE IT!!
				if ($offset_dir != "none") {
					$centering_width = $offset['dimensions']['w'];
					$centering_height = $offset['dimensions']['h'];
					if (strpos($offset_dir, "left") !== false) {
						// means the whole image should be shifted left
						$w_offset = $cim_width - $centering_width;
						$cim_width = $centering_width; // this is when we want it to be not the full width
					} else if (strpos($offset_dir, "right") !== false) {
						// means the whole image should be shifted right
						$cim_width = $centering_width; // this is when we want it to be not the full width
					}

					if (strpos($offset_dir, "top") !== false) {
						$h_offset = $cim_height - $centering_height;
						$cim_height = $centering_height;
					} else if (strpos($offset_dir, "bottom") !== false) {
						$cim_height = $centering_height;
					}
				}

				$points = array('x'=>($card_width - $cim_width)/2 - $w_offset, 'y'=>($card_height - $cim_height)/2 - $h_offset);
				break;
		}
		return $points;
	}

}
