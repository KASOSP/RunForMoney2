<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;

use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MoneyCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Check your money balance",
			"/money",
			["mymoney"]
		);
		// $this->setPermission("kametan.command.mymoney");
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
		$userId = $userHandler->getUserIdByUser($sender);
		$money = UserHandler::getInstance()->getUserMoney($userId);
		$sender->sendMessage(MESSAGE . ">> You have " . $money . " meta");
		return true;
	}
}