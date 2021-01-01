<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\item;

use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\GamerData;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Ekurea extends AdditionalItem{
	const ITEM_ID = 940;

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult{
		$userHandler = UserHandler::getInstance();
		$gameHandler = GameHandler::getInstance();
		if(!$gameHandler->isGameRunning()){
			$userHandler->sendTranslatedMessage($player, "item.use.fail.outOfGame", ERROR);
			return ItemUseResult::FAIL();
		}
		if(!$gameHandler->getGameInfo()->hasGamerData($player) or $gameHandler->getGameInfo()->getGamerData($player)->gamerType !== GamerData::GAMER_TYPE_RUNNER){
			$userHandler->sendTranslatedMessage($player, "item.use.fail.onlyForRunner", ERROR);
			return ItemUseResult::FAIL();
		}

		switch(mt_rand(0, 3)){
			case 0:
				$player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 10 * 20, 1, false));
				$userHandler->sendTranslatedMessage($player, "item.use.success.ekurea.1", MESSAGE);
				break;
			case 1:
				$player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 15 * 20, 1, false));
				$userHandler->sendTranslatedMessage($player, "item.use.success.ekurea.2", MESSAGE);
				break;
			case 2:
				$player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 30 * 20, 1, false));
				$userHandler->sendTranslatedMessage($player, "item.use.success.ekurea.3", MESSAGE);
				break;
			case 3:
				$player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 5 * 20, 1, false));
				$userHandler->sendTranslatedMessage($player, "item.use.success.ekurea.4", MESSAGE);
				break;
		}
		$this->pop();
		return ItemUseResult::SUCCESS();
	}
}