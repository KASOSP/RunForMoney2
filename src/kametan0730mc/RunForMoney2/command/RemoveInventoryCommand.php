<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class RemoveInventoryCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Remove your inventory",
			"/removeinventory",
			["ri"]
		);
		//$this->setPermission("kametan.command.removeinventory");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(ERROR . "This command is only for player");
			return true;
		}
		$userHandler = UserHandler::getInstance();
		if($userHandler->getTempData($sender)->cleanInventoryConfirm){
			$sender->getInventory()->clearAll();
			$userHandler->getTempData($sender)->cleanInventoryConfirm = false;
			$userHandler->sendTranslatedMessage($sender, "command.removeInventory.success", MESSAGE);
		}else{
			$userHandler->sendTranslatedMessage($sender, "command.removeInventory.confirm", MESSAGE);
			$userHandler->getTempData($sender)->cleanInventoryConfirm = true;
		}
		return true;
	}
}