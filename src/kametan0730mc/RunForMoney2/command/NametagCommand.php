<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\nametag\Nametag;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class NametagCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Change or set nametag",
			"/nametag <tag>"
		);
		//$this->setPermission("kametan.command.nametag");
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
		if(!isset($args[0])){
			$userHandler->sendTranslatedMessage($sender, "command.nametag.usage", ERROR);
			return true;
		}
		$nametag = $args[0];

		if($nametag === "reset"){
			$userHandler->getSaveDataByUser($sender)->setNametag("");
			$userHandler->updateState($sender);
			$userHandler->sendTranslatedMessage($sender, "system.nametag.reset", MESSAGE);
			return true;
		}

		if(!Nametag::testIsValidNametag($sender, $nametag)){
			return true;
		}

		$userHandler->getSaveDataByUser($sender)->setNametag($args[0]);
		$userHandler->updateState($sender);
		$userHandler->sendTranslatedMessage($sender, "command.nametag.success", MESSAGE, [$nametag]);
		return true;
	}
}