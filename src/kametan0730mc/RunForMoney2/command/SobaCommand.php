<?php


namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\item\Soba;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\item\ItemFactory;
use pocketmine\Server;

class SobaCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Give all players toshikoshi soba",
			"/soba"
		);
		$this->setPermission("kametan.command.soba");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->getInventory()->addItem(ItemFactory::getInstance()->get(Soba::ITEM_ID, 0, 1));
			$player->sendMessage("§a>> サーバーから年越しそばが送られました");
		}

	}
}