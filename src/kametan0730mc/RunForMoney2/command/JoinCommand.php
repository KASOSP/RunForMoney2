<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command;


use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\GamerData;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class JoinCommand extends Command{
	public function __construct(string $name){
		parent::__construct(
			$name,
			"Join to started game",
			"/join",
		);
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
		$gameHandler = GameHandler::getInstance();
		if(!$gameHandler->isGameRunning() or $gameHandler->isFinalPhase()){
			$userHandler->sendTranslatedMessage($sender, "command.join.fail", ERROR);
			return true;
		}

		if($gameHandler->getGameInfo()->hasGamerData($sender) and $gameHandler->getGameInfo()->getGamerData($sender)->gamerType !== GamerData::GAMER_TYPE_WAITING){
			$userHandler->sendTranslatedMessage($sender, "command.join.fail", ERROR);
			return true;
		}

		if($gameHandler->getGameInfo()->getGamerData($sender)->surrender !== 0){
			$userHandler->sendTranslatedMessage($sender, "command.join.fail", ERROR);
			return true;
		}
		$gameHandler->getGameInfo()->initGamerData($sender);
		$gameHandler->getGameInfo()->getGamerData($sender)->gamerType = GamerData::GAMER_TYPE_CAUGHT;
		$sender->teleport($gameHandler->getGameInfo()->field->getJailPoint());
		$userHandler->sendTranslatedMessage($sender, "command.join.success", MESSAGE);
		$userHandler->updateState($sender);
		return true;
	}
}