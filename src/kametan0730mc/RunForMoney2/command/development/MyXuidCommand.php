<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class MyXuidCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"You can confirm your xuid",
			"/myxuid"
		);
		$this->setPermission("kametan.command.myxuid");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(ERROR . "This command is only for player");
			return true;
		}

		$xuid = $sender->getXuid();
		$sender->sendMessage(MESSAGE . ">> Your xuid is " . $xuid);
		return true;

	}
}