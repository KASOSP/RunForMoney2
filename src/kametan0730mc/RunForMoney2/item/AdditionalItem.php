<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\item;


use pocketmine\item\Item;

abstract class AdditionalItem extends Item{
	
	public function getMaxStackSize(): int{
		return 1;
	}
}