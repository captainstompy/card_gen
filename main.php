<?php
// TODO - aren't they auto fine since in same dir???
require_once('Rank.php');
require_once('Suit.php');
require_once('Card.php');
require_once('CardImage.php');

// where we run!
$ace = new Rank('A', "Ace");
$two = new Rank(2, null);
$three = new Rank(3, null);
$four = new Rank(4, null);
$five = new Rank(5, null);
$six = new Rank(6, null);
$seven = new Rank(7, null);
$eight = new Rank(8, null);
$nine = new Rank(9, null);
$ten = new Rank(10, null);
$jack = new Rank('J', 'Jack');
$queen = new Rank('Q', 'Queen');
$king = new Rank('K', 'King');
$bishop = new Rank('B', 'Bishop');
$cardinal = new Rank('C', 'Cardinal');
$pope = new Rank('P', 'Pope');
$god = new Rank('G', 'God');
$ranks = array(
	$ace,
	$two,
	$three,
	$four,
	$five,
	$six,
	$seven,
	$eight,
	$nine,
	$ten,
	$jack,
	$queen,
	$king,
	$bishop,
	$cardinal,
	$pope,
	$god
);

$spade = new Suit('Spade','Black');
$anchor = new Suit('Anchor','Blue');
$shield = new Suit('Shield','Red');
$heart = new Suit('Heart','Red');
$diamond = new Suit('Diamond','Red');
$bottle = new Suit('Bottle','Blue');
$crown = new Suit('Crown','Blue');
$club = new Suit('Club','Black');
$cup = new Suit('Cup','Black');

$bottle->setOffset("left", 600, 800); // TODO - unsure about that 800
$crown->setOffset("bottom", 600, 462); // TODO - unsure about that 600
$heart->setOffset("rightbottom", 600, 482);
$shield->setOffset("rightbottom", 600, 641);
$club->setOffset("lefttop", 600, 560);

$suits = array(
	$spade,
	$anchor,
	$shield,
	$heart,
	$diamond,
	$bottle,
	$crown,
	$club
);

CardImage::setImageDir("images/");
CardImage::setWriteDir("cards/");

foreach($ranks as $rank) {
	foreach($suits as $suit) {
		$card = new Card($suit, $rank);

		$card->genCard();
	}
}

// cups have to be separate because there is too much variability
$cup_ranks = array(
	array(
		'ranks' => array($ace, $two, $three, $four, $five, $six, $seven, $eight, $nine, $ten),
		'offset' => array('dir'=>'none', 'size'=>array('w'=>600,'h'=>800))
	),
	array(
		'ranks' => array($jack),
		'offset' => array('dir'=>'leftbottom', 'size'=>array('w'=>500, 'h'=>630))
	),
	array(
		'ranks' => array($queen),
		'offset' => array('dir'=>'leftbottom', 'size'=>array('w'=>500, 'h'=>640))
	),
	array(
		'ranks' => array($king, $god),
		'offset' => array('dir'=>'leftbottom', 'size'=>array('w'=>500, 'h'=>650))
	),
	array(
		'ranks' => array($bishop, $cardinal, $pope),
		'offset' => array('dir'=>'leftbottom', 'size'=>array('w'=>500, 'h'=>645))
	)
);
foreach($cup_ranks as $cup_rank) {
	$cup->setOffset($cup_rank['offset']['dir'], $cup_rank['offset']['size']['w'], $cup_rank['offset']['size']['h']);
	foreach($cup_rank['ranks'] as $rank) {
		$card = new Card($cup, $rank);
		$card->genCard();
	}
}

// and then jokers
$joker = new Rank('joker', null);
$colours = array(
	new Suit('Black', 'Black'),
	new Suit('Blue', 'Blue'),
	new Suit('Red', 'Red')
);

foreach($colours as $colour) {
	$card = new Card($colour, $joker);
	$card->genCard();
}

?>
