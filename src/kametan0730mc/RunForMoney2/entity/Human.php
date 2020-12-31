<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\entity;


use pocketmine\entity\Human as OriginalHuman;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

class Human extends OriginalHuman{

	public function attack(EntityDamageEvent $source): void{

		if($source instanceof EntityDamageByEntityEvent){
			$damager = $source->getDamager();
			$this->lookAt($damager->getLocation());
		}
	}

}