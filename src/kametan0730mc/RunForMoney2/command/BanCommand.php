<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;

use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class BanCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Add player to blacklist",
			"/ban <player> <reason>"
		);
		$this->setPermission("kametan.command.ban");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!isset($args[0]) or !isset($args[1])){
			if($sender instanceof Player){
				UserHandler::getInstance()->sendTranslatedMessage($sender, "command.ban.usage", ERROR);
			}else{
				$sender->sendMessage("Usage : /ban <player> <reason>");
			}
			return true;
		}

		$name = $args[0];
		$reason = $args[1];
		$player = Server::getInstance()->getPlayerByPrefix($name);
		if(!$player instanceof Player){
			if($sender instanceof Player){
				UserHandler::getInstance()->sendTranslatedMessage($sender, "command.ban.targetNotFound", ERROR, [$name]);
			}else{
				$sender->sendMessage("Target " . $name . " not found");
			}
			return true;
		}
		UserHandler::getInstance()->ban($player, $reason);
		if(strtolower($player->getName()) === strtolower($sender->getName())){
			return true; // 文字自分をBanしたら、メッセージを送るときにエラーになる
		}
		UserHandler::getInstance()->sendTranslatedMessage($sender, "command.ban.success", MESSAGE, [$name, $reason]);
		return true;
	}
}