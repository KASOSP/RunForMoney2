<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SaveDataDumpCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"You can see your save data",
			"/savedatadump"
		);
		$this->setPermission("kametan.command.savedatadump");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(ERROR . "This command is only for player");
			return true;
		}
		$sender->sendMessage(MESSAGE . ">> Your save data:" . UserHandler::getInstance()->getSaveDataByUser($sender)->jsonSerialize());
		return true;
	}
}