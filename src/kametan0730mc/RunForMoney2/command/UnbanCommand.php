<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class UnbanCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Remove player from blacklist",
			"/unban <username>"
		);
		$this->setPermission("kametan.command.unban");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		$sender->sendMessage(">> This command is not available now");
		return true;
	}
}