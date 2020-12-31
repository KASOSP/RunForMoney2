<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CleanEntityCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Close your world entities",
			"/cleanentity"
		);
		$this->setPermission("kametan.command.cleanentity");
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
		$count = 0;
		foreach ($world->getEntities() as $entity) {
			if(!$entity instanceof Player){
				$count++;
				$entity->close();
			}
		}
		UserHandler::getInstance()->sendTranslatedMessage($sender, "command.cleanEntity.execute", INFO, [$count]);
		return true;
	}
}