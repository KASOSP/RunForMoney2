<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\entity\projectile;


use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class FishingHook extends Projectile{
	/* @var float */
	public $width = 0.1;

	/* @var float */
	public $height = 0.1;
	public static function getNetworkTypeId() : string{ return EntityIds::FISHING_HOOK; }

	protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo($this->height, $this->width); }

}