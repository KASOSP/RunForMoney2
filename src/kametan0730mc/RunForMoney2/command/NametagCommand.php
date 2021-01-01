<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class NametagCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Change or set nametag",
			"/nametag <tag>"
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
		if(!isset($args[0])){
			$userHandler->sendTranslatedMessage($sender, "command.nametag.usage", ERROR);
			return true;
		}
		$nametag = $args[0];

		if($nametag === "reset"){
			$userHandler->getSaveDataByUser($sender)->setNametag("");
			$userHandler->updateState($sender);
			$userHandler->sendTranslatedMessage($sender, "command.nametag.reset", MESSAGE);
			return true;
		}

		$len = mb_strlen($nametag);
		if($len-(substr_count($nametag, '§')*2) > 10 or $len > 16){
			$userHandler->sendTranslatedMessage($sender, "command.nametag.tooLong", ERROR);
			return true;
		}

		for($i=0;$i<$len;$i++){
			$char = mb_substr($nametag, $i, 1);
			if($char === '§'){
				// §が最後の文字か最後から2番目文字だったり、次の文字がカラーコードとして無効な文字だったら
				if($i >= $len-2 or array_search(mb_substr($nametag, $i+1, 1), ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f']) === false){
					$userHandler->sendTranslatedMessage($sender, "command.nametag.invalid", ERROR);
					return true;
				}
			}
		}

		$userHandler->getSaveDataByUser($sender)->setNametag($args[0]);
		$userHandler->updateState($sender);
		$userHandler->sendTranslatedMessage($sender, "command.nametag.success", MESSAGE, [$nametag]);
		return true;
	}
}