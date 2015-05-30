<?php

class Card {

	private $suit; // of type Suit
	private $rank; // of type Rank

	private $height;
	private $width;

	private $bleed;
	private $spacing;
	private $center_offset;
	private $image_size;

	private $debug;

	// TODO - not sure if this is the best place for this, or how we want to set it.
	private static $side_buffer = 161;

	function Card($suit, $rank) {
		// TODO - properly would req checking these are valid suits and ranks!
		$this->suit = $suit;
		$this->rank = $rank;

		/* other variables need default setting */
		// TODO - gotta figure out size vs pixels with like dpi n shit
		$this->height = 1122;
		$this->width = 822;
		$this->bleed = 36;
		$this->spacing = 20;
		$this->center_offset = 'none';
		$this->image_size = array('w'=>600, 'h'=>800);

		$this->debug = false;
	}

	public function genCard() {
		// get the component imgs
		$rank_text = $this->rank->getText($this->suit->getColour());
		$suit_marker = $this->suit->getMarker();
		$main_img = $this->rank->getMain($this->suit);
		$card_main_points = $this->rank->getPoints($this->width, $this->height, $main_img, $this->suit->getOffset());
		$card_main = CardImage::create($main_img, $this->rank->getValue(), $card_main_points, array('w'=>$this->width, 'h'=>$this->height));

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

		$buffer = $this->bleed;
		$spacing = $this->spacing;
		$main_width = $this->width;
		$main_height = $this->height;

		$base = CardImage::getBase($main_width, $main_height);
		// make the card; start with putting on the middle image
		//$base->add($card_main, ($main_width - $cim_width)/2 - $w_offset, ($main_height - $cim_height)/2 - $h_offset);
		$base->add($card_main, 0,0);
		// put rank text in, first top left, then rotated bottom right
		$base->add($rank_text, $buffer + ($sm_width-$rank_width)/2, $buffer);
		$base->add($this->rank->getInvertedText($this->suit->getColour()), $main_width - $buffer - $rank_width - ($sm_width - $rank_width)/2, $main_height - $buffer - $rank_height);
		// put suit marker in, first top left, then rotated bottom right
		$base->add($suit_marker, $buffer, $rank_height + $buffer + $spacing);
		$base->add($this->suit->getInvertedMarker(), $main_width - $buffer - $sm_width, $main_height - $buffer - ($rank_height + $sm_height + $spacing));

		if ($this->debug) {
			$base->addDebug($main_width, $main_height);
		}

		// save
		$base->writeImage($this->suit->getName()."_".$this->rank->getValue()."_finish.png");
	}

	public static function getSideBuffer() {
		return Card::$side_buffer;
	}
}
