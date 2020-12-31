<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SpawnCommand extends Command{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Teleport to world spawn",
			"/spawn",
			["hub"]
		);
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
		$userHandler->respawn($sender);
		$userHandler->sendTranslatedMessage($sender, "command.spawn.success", MESSAGE);
		return true;
	}
}