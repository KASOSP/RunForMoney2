<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\item;


use kametan0730mc\RunForMoney2\form\MenuForm;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MenuPad extends AdditionalItem{
	const ITEM_ID = 1070;
	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
		$player->sendForm(new MenuForm(UserHandler::getInstance()->getSaveDataByUser($player)->getLang(), $player));
		return ItemUseResult::SUCCESS();
	}
}