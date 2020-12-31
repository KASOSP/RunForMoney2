<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\crown;


use pocketmine\utils\TextFormat;

class Crown{

	/** @var string[] */
	private static $crowns = [ "♔", "♕", "♖", "♗", "♘", "♙", "♚", "♛", "♜", "♝", "♞", "♟"];

	public static function toText(int $type, $color){
		return "§" . $color . self::$crowns[$type];
	}
}