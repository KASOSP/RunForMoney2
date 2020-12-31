<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;

use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class StatusCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Check your game status",
			"/status"
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
		$userId = $userHandler->getUserIdByUser($sender);
		$status = $userHandler->calcUserGameStatus($userId);
		$clear = $status[0];
		$death = $status[1];
		$catch = $status[2];
		$surrender = $status[3];
		$revival = $status[4];

		$sender->sendMessage("§a|==============================");
		$sender->sendMessage("§a|・".$sender->getName()."様のステータス");
		$sender->sendMessage("§a|・逃走成功した回数	: ".$clear."");
		$sender->sendMessage("§a|・確保された回数		: ".$death."");
		$sender->sendMessage("§a|・確保した回数		: ".$catch."");
		$sender->sendMessage("§a|・自首回数			: ".$surrender."");
		$sender->sendMessage("§a|・復活回数			: ".$revival."");
		$sender->sendMessage("§a|==============================");
		return true;
	}
}