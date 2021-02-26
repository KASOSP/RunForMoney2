<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\entity;


use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class LightningBolt extends Entity{
	/** @var int */
	public $age = 0;

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.1, 0.1);
	}

	public static function getNetworkTypeId(): string{
		return EntityIds::LIGHTNING_BOLT;
	}

	protected function entityBaseTick(int $tickDiff = 1): bool{
		if(!$this->closed and ++$this->age > 100){
			$this->close();
			return true;
		}
		return false;
	}
}