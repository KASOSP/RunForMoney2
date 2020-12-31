<?php


namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\item\Soba;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\Server;

class TeleportAllToMeCommand extends Command{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Teleport all players to sender",
			"/tpa"
		);
		$this->setPermission("kametan.command.tpa");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(ERROR . "This command is only for player");
			return true;
		}
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->teleport($sender->getLocation());
		}
		return true;
	}
}