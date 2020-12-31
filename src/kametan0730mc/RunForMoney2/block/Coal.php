<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\block;

use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\GamerData;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Coal as OriginalCoal;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Coal extends OriginalCoal{

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
		if(!$player instanceof Player){
			return false;
		}
		$userHandler = UserHandler::getInstance();
		$gameHandler = GameHandler::getInstance();

		if($gameHandler->isFinalPhase()){
			$userHandler->sendTranslatedMessage($player, "game.surrender.finalPhase", ERROR);
			return false;
		}
		$gamerData = $gameHandler->getGameInfo()->getGamerData($player);
		if($gamerData->gamerType !== GamerData::GAMER_TYPE_RUNNER){
			$userHandler->sendTranslatedMessage($player, "game.surrender.notRunner", ERROR);
			return false;
		}
		if($gamerData->surrenderCountDown !== -1){
			$userHandler->sendTranslatedMessage($player, "game.surrender.alreadyApplied", ERROR);
			return false;
		}
		$gameHandler->applySurrender($player);

		return false;
	}
}