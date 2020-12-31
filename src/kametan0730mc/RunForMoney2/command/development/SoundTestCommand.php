<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\player\Player;

class SoundTestCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Play a test sound",
			"/soundtest"
		);
		$this->setPermission("kametan.command.soundtest");
	}


	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage("This command is only for player");
			return true;
		}
		if(!isset($args[0])){
			UserHandler::getInstance()->sendTranslatedMessage($sender, "command.soundTest.usage", ERROR);
			return true;
		}
		$id = (int) $args[0];
		$packet = LevelEventPacket::create($id, 0, $sender->getLocation());
		$sender->getNetworkSession()->sendDataPacket($packet);
		$sender->sendMessage(">> Played ".$id." sound");
		return true;
	}
}