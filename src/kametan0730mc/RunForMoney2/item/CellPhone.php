<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\item;


use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\GamerData;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class CellPhone extends AdditionalItem{
	const ITEM_ID = 910;

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
		$userHandler = UserHandler::getInstance();
		$gameHandler = GameHandler::getInstance();
		if(!$gameHandler->isGameRunning()){
			$userHandler->sendTranslatedMessage($player, "item.use.fail.outOfGame", ERROR);
			return ItemUseResult::FAIL();
		}
		$gamerData = $gameHandler->getGameInfo()->getGamerData($player);
		$gamerType = $gamerData->gamerType;
		if($gamerType !== GamerData::GAMER_TYPE_RUNNER and $gamerType !== GamerData::GAMER_TYPE_BETRAYAL){
			$userHandler->sendTranslatedMessage($player, "item.use.fail.onlyForRunner", ERROR);
			return ItemUseResult::FAIL();
		}

		if($gamerData->surrenderCountDown !== -1){
			$userHandler->sendTranslatedMessage($player, "game.surrender.alreadyApplied", ERROR);
			return ItemUseResult::FAIL();
		}
		$gameHandler->applySurrender($player);
		$this->pop();
		return ItemUseResult::SUCCESS();
	}
}