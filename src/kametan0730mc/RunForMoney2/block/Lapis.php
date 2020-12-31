<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\block;

use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\GamerData;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Lapis extends Block{
	public function __construct(BlockIdentifier $idInfo, string $name, ?BlockBreakInfo $breakInfo = null){
		parent::__construct($idInfo, $name, $breakInfo ?? new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 30.0));
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
		$userHandler = UserHandler::getInstance();
		$gameHandler = GameHandler::getInstance();
		if(!$gameHandler->isGameRunning() or $gameHandler->isFinalPhase()){
			return false;
		}
		$gamerData = $gameHandler->getGameInfo()->getGamerData($player);
		if($gamerData->gamerType !== GamerData::GAMER_TYPE_CAUGHT){
			$userHandler->sendTranslatedMessage($player, "game.revival.notCaught", ERROR);
			return false;
		}
		$gameHandler->revival($player);
		return false;
	}
}