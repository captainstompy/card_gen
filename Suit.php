<?php

class Suit {

	private $suit_name;
	private $colour;

	private $graphic;
	private $inverted_graphic;
	private $marker;
	private $inverted_marker;

	private $offset_dir;
	private $big_suit_size; // an array of w and h

	function Suit($name, $colour) {
		$this->suit_name = $name;
		$this->colour = $colour;

		$this->offset_dir= "none";
		$this->big_suit_size = array();
	}

	public function getColour() {
		return $this->colour;
	}

	public function getName() {
		return $this->suit_name;
	}

	public function getMarker() {
		if (!isset($this->marker)) {
			$this->marker = CardImage::getImage($this->colour."/".$this->suit_name."_small.png");
		}
		return $this->marker;
	}

	public function getGraphic() {
		if (!isset($this->graphic)) {
			$this->graphic = CardImage::getImage($this->colour."/".$this->suit_name.".png");
		}
		return $this->graphic;
	}

	public function getInvertedMarker() {
		if (!isset($this->inverted_marker)) {
			$this->inverted_marker = clone $this->getMarker();
			$this->inverted_marker->rotateImage("none", 180);
		}
		return $this->inverted_marker;
	}

	public function getInvertedGraphic() {
		if (!isset($this->inverted_graphic)) {
			$this->inverted_graphic = clone $this->getGraphic();
			$this->inverted_graphic->rotateImage("none", 180);
		}
		return $this->inverted_graphic;
	}

	public function getFacecardImg($rank_name) {
		return CardImage::getImage($suit->colour."/".$suit->suit_name."_".$rank_name.".png");
	}

	public function getOffset() {
		return array('direction'=>$this->offset_dir, 'dimensions'=>$this->big_suit_size);
	}

	public function setOffset($direction, $width, $height) {
		$this->offset_dir = $direction;
		$this->big_suit_size = array('w'=>$width, 'h'=>$height);
	}
}
