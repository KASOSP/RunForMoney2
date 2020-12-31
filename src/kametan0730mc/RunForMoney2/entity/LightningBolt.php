<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\entity;


use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class LightningBolt extends Entity{
	/* @var float */
	public $width = 0.1;

	/* @var float */
	public $height = 0.1;

	/** @var int */
	public $age = 0;

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