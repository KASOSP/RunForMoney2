<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\entity\projectile;


use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\projectile\Projectile;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class DragonFireball extends Projectile{

	protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(0.1, 0.1); }

	public static function getNetworkTypeId() : string{ return EntityIds::DRAGON_FIREBALL; }
}