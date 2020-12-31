<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;

use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class GameCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"You can manage run for money game",
			"/game <start|stop> [option...]"
		);
		$this->setPermission("kametan.command.game");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!isset($args[0])){
			return true;
		}

		switch($args[0]){
			case "start":
				GameHandler::getInstance()->startWaiting();
				UserHandler::getInstance()->sendTranslatedMessage($sender, "command.game.startCountdown", MESSAGE);
				break;
			case "stop": // 必ず待機時間に使う
				GameHandler::getInstance()->stopGame();
				UserHandler::getInstance()->sendTranslatedMessage($sender, "command.game.stop", MESSAGE);
				break;
		}
		return true;

	}
}