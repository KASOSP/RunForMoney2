<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\item;


use kametan0730mc\RunForMoney2\database\type\UserMoneyTransactionType;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\color\Color;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\DustParticle;

class Soba extends AdditionalItem{
	const ITEM_ID = 1040;

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
		$userHandler = UserHandler::getInstance();
		$userId = $userHandler->getUserIdByUser($player);
		$player->sendMessage(MESSAGE.">> 年越しそばを食べた!!!");
		$amount = mt_rand(20000, 60000) * 10;
		$userHandler->addUserMoney($userId, $amount, UserMoneyTransactionType::TYPE_ITEM_SOBA);
		$player->sendMessage("§6>> " . $amount . "メタお年玉をもらった!!");
		$player->sendMessage("§d§l>> 明けましておめでとうございます!!!");
		$player->sendMessage("§c>> 今年もかめたんサーバーをよろしくお願いします!");
		$this->pop();

		for($x=0;$x<10;$x++){
			for($z=0;$z<10;$z++){
				$particle = new DustParticle(new Color(mt_rand(120,250),mt_rand(120,250),mt_rand(120,250)));
				$player->getWorld()->addParticle($player->getLocation()->add(rand(-20,20)/10,rand(-20,20)/10,rand(-20,20)/10), $particle);
			}
		}
		return ItemUseResult::SUCCESS();
	}
}