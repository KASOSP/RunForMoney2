<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\block;

use kametan0730mc\RunForMoney2\item\DoorKey;
use pocketmine\block\Door as OriginalDoor;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\DoorSound;

class Door extends OriginalDoor{

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{

		if(!$item instanceof DoorKey){
			return false;
		}

		$this->open = !$this->open;

		$other = $this->getSide($this->top ? Facing::DOWN : Facing::UP);
		if($other instanceof Door and $other->isSameType($this)){
			$other->open = $this->open;
			$this->pos->getWorld()->setBlock($other->pos, $other);
		}

		$this->pos->getWorld()->setBlock($this->pos, $this);
		$this->pos->getWorld()->addSound($this->pos, new DoorSound());

		return false;
	}
}