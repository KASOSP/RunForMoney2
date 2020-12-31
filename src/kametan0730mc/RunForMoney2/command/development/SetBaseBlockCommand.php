<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\DiamondOre;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetBaseBlockCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Place block base of building",
			"/setbaseblock"
		);
		$this->setPermission("kametan.command.setbaseblock");
	}


	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(ERROR . "This command is only for player");
			return true;
		}
		$world = $sender->getWorld();
		$world->setBlock($sender->getLocation(), BlockFactory::getInstance()->get(1, 0));
		UserHandler::getInstance()->sendTranslatedMessage($sender, "command.setBaseBlock.success", MESSAGE);
		return true;
	}
}