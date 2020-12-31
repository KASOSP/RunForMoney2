<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\item;

use kametan0730mc\RunForMoney2\block\Door;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class DoorKey extends AdditionalItem{
	const ITEM_ID = 930;

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
		if($blockClicked instanceof Door){
			$userHandler = UserHandler::getInstance();
			if(mt_rand(0, 30) === 7){
				$userHandler->sendTranslatedMessage($player, "item.use.door_key.broken", MESSAGE);
			}else{
				$userHandler->sendTranslatedMessage($player, "item.use.door_key.success", MESSAGE);
			}
			return ItemUseResult::SUCCESS();
		}
		return ItemUseResult::NONE();
	}
}