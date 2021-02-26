<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\entity;


use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Agent extends Entity{

	protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(0.1, 0.1); }

	public static function getNetworkTypeId() : string{ return EntityIds::AGENT; }
}