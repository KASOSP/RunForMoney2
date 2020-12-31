<?php


namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\item\Soba;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\Server;

class SetPhotoStudioCommand extends Command{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Set photo studio position",
			"/setphotostudio"
		);
		$this->setPermission("kametan.command.setphotostudio");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(ERROR . "This command is only for player");
			return true;
		}
		UserHandler::getInstance()->setPhotoStudioPos($sender->getLocation());
		$sender->sendMessage(MESSAGE . ">> 撮影台の場所を設定しました");
		return true;
	}
}