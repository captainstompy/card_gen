<?php

class CardImage {

	private static $image_dir;
	private static $write_dir;

	private $imagick;

	function CardImage($img) {
		$this->imagick = $img;
	}

	function __clone() {
		$this->imagick = clone $this->imagick;
	}

// TODO - need to verify these exist before trying to do anything!
	public static function setImageDir($dirname) {
		CardImage::$image_dir = $dirname;
	}

	public static function setWriteDir($dirname) {
		CardImage::$write_dir = $dirname;
	}

// TODO - wrap the Imagick object in a CardImage object or something, so that the other files never touch Imagick to make it easier to change later if necessary.
	public static function getImage($filename) {
		return new CardImage(new Imagick(CardImage::$image_dir.$filename));
	}

	/**
	* if value is numeric (ie 2,3,10, etc), then $image is a Suit graphic.
	* if the value is non-numeric, then $image is a big image and needs to be offset-applied TODO
	*/
	public static function create($image, $value, $points, $card_size) {
		$card_main = new Imagick();
		$background = "#FFFFFF";
		$card_main->newImage($card_size['w'], $card_size['h'], $background);
		$card_main->setImageFormat('png');

		if (is_numeric($value)) {
			$suit_graphic = $image;

			foreach($points as $point) {
				if(isset($point['flip']) && $point['flip']) {
					$suit_graphic = clone $image;
					$suit_graphic->rotateImage('none',180);
				}
				$card_main->compositeImage($suit_graphic->imagick, Imagick::COMPOSITE_DEFAULT, $point['x'], $point['y']);
			}
		} else {
			$card_main->compositeImage($image->imagick, Imagick::COMPOSITE_DEFAULT, $points['x'], $points['y']);
		}
		return new CardImage($card_main);
	}

	public static function getBase($main_width, $main_height) {
		$base = new Imagick();
		$base->newImage($main_width, $main_height, "#FFFFFF");

		return new CardImage($base);
	}

	private function probablyRemoveThisButSaveTheOffsets() {
		// do we offset the center image?
		$offset_dir = $this->center_offset;
		$w_offset = 0;
		$h_offset = 0;
		// $offset_dir is the direction we WANT TO MOVE IT!!
		if ($offset_dir != "none") {
			$center_size = $this->image_size;
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
				$h_offset = $cm_height - $centering_height;
				$cim_height = $centering_height;
			} else if (strpos($offset_dir, "bottom") !== false) {
				$cim_height = $centering_height;
			}
		}
	}

	public function add($image, $x, $y) {
		$this->imagick->compositeImage($image->imagick, Imagick::COMPOSITE_DEFAULT, $x, $y); 
	}

	public function addDebug($main_width, $main_height) {
		// TODO - don't have offsets
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
	}

	public function writeImage($filename) {
		$this->imagick->writeImage(CardImage::$write_dir.$filename);
	}

	public function getImageHeight() {
		return $this->imagick->getImageHeight();
	}

	public function getImageWidth() {
		return $this->imagick->getImageWidth();
	}

	public function rotateImage($bkgd, $deg) {
		return $this->imagick->rotateImage($bkgd, $deg);
	}
}
