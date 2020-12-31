<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\entity;


use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Agent extends Entity{
	/* @var float */
	public $width = 0.1;

	/* @var float */
	public $height = 0.1;

	public static function getNetworkTypeId() : string{ return EntityIds::AGENT; }
}