<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\item;


use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\GamerData;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class RabbitFoot extends AdditionalItem{
	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult{
		$userHandler = UserHandler::getInstance();
		$gameHandler = GameHandler::getInstance();
		if(!$gameHandler->isGameRunning()){
			$userHandler->sendTranslatedMessage($player, "item.use.fail.outOfGame", ERROR);
			return ItemUseResult::FAIL();
		}
		$gamerType = $gameHandler->getGameInfo()->getGamerData($player)->gamerType;
		if($gamerType !== GamerData::GAMER_TYPE_RUNNER and $gamerType !== GamerData::GAMER_TYPE_HUNTER and $gamerType !== GamerData::GAMER_TYPE_BETRAYAL){
			$userHandler->sendTranslatedMessage($player, "item.use.fail.onlyForGamer", ERROR);
			return ItemUseResult::FAIL();
		}

		$player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 10 * 20, 1, false));
		$userHandler->sendTranslatedMessage($player, "item.use.rabbitFoot", MESSAGE);
		$this->pop();
		return ItemUseResult::SUCCESS();
	}
}