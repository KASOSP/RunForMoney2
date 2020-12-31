<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game;

use kametan0730mc\RunForMoney2\database\Database;
use kametan0730mc\RunForMoney2\database\type\RunForMoneyEntryType;
use kametan0730mc\RunForMoney2\database\type\UserCrownType;
use kametan0730mc\RunForMoney2\entity\LightningBolt;
use kametan0730mc\RunForMoney2\game\field\FieldLoader;
use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\sound\PortalSound;
use kametan0730mc\RunForMoney2\database\type\UserMoneyTransactionType;
use kametan0730mc\RunForMoney2\database\type\UserRunForMoneyResultType;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\ClickSound;
use pocketmine\world\sound\XpLevelUpSound;

class GameHandler{
	use SingletonTrait;

	/** @var Database */
	private $database;

	/** @var GameInfo|null */
	private $gameInfo;

	public function init(Database $database){
		$this->database = $database;
	}

	/**
	 * ゲームの待機を開始します
	 */
	public function startWaiting(){
		$info = new GameInfo();
		$info->status = GameInfo::GAME_STATUS_WAITING;
		$info->time = 0;
		$info->maxTime = 10;
		$this->gameInfo = $info;
	}

	/**
	 * ゲームを開始します
	 */
	public function startGame(){
		if(!$this->gameInfo instanceof GameInfo){
			$this->gameInfo = new GameInfo();
		}
		$gameId = $this->database->putRunForMoneyEntry(new RunForMoneyEntryType(null, "s"));
		if($gameId === -1){
			return;
		}
		$this->gameInfo->gameId = $gameId;
		$field = FieldLoader::getRandomField();
		$this->gameInfo->field = $field;
		$this->gameInfo->maxTime = $field->getGameTime();
		$this->gameInfo->status = GameInfo::GAME_STATUS_RUNNING;

		/** @var UserHandler $userHandler */
		$userHandler = UserHandler::getInstance();
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			if($userHandler->getTempData($player)->isAutoJoinGame){
				$this->gameInfo->initGamerData($player);
				$this->gameInfo->getGamerData($player)->gamerType = GamerData::GAMER_TYPE_RUNNER;
			}
		}

		$gamers = $this->getAllGamerPlayers();
		$hunterCount = 0;
		$betrayerCount = 0;
		while(($hunterCount+$betrayerCount) < (int) ceil(count($gamers)/10)){
			foreach($gamers as $gamer){
				if($this->gameInfo->getGamerData($gamer)->gamerType === GamerData::GAMER_TYPE_RUNNER){
					if(mt_rand(0, 30) === 20){
						$this->gameInfo->getGamerData($gamer)->gamerType = GamerData::GAMER_TYPE_HUNTER;
						$hunterCount++;
					}elseif(mt_rand(0, 1000) === 300){
						$this->gameInfo->getGamerData($gamer)->gamerType = GamerData::GAMER_TYPE_BETRAYAL;
						$betrayerCount++;
					}
				}
			}
		}

		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$userHandler->sendTranslatedMessage($player, "game.start1", INFO, [$this->gameInfo->maxTime]);
			$fieldName = Lang::translate($userHandler->getSaveDataByUser($player)->getLang(), $field->getNameLangCode());
			$userHandler->sendTranslatedMessage($player, "game.start2", INFO, [$fieldName, $hunterCount, 0]);
		}

		foreach($gamers as $gamer){
			switch($this->gameInfo->getGamerData($gamer)->gamerType){
				case GamerData::GAMER_TYPE_RUNNER:
					$gamer->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 40 * 20, 1, false));
					$gamer->teleport($field->getRunnerRandomSpawnPoint());
					$userHandler->sendTranslatedMessage($gamer, "game.start.runner.message", MESSAGE);
					$userHandler->sendTranslatedTitle($gamer, "game.start.runner.title", null, TextFormat::RED, null);
					break;
				case GamerData::GAMER_TYPE_HUNTER:
					//$gamer->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 40 * 20, 1, false));
					$gamer->teleport($field->getHunterSpawnPoint());
					$userHandler->sendTranslatedMessage($gamer, "game.start.hunter.message", MESSAGE);
					$userHandler->sendTranslatedTitle($gamer, "game.start.hunter.title", null, TextFormat::RED, null);
					break;
				case GamerData::GAMER_TYPE_BETRAYAL:
					//$gamer->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 40 * 20, 1, false));
					$gamer->teleport($field->getBetrayerSpawnPoint());
					$userHandler->sendTranslatedMessage($gamer, "game.start.betrayer.1", MESSAGE);
					$userHandler->sendTranslatedMessage($gamer, "game.start.betrayer.2", MESSAGE);
					$userHandler->sendTranslatedMessage($gamer, "game.start.betrayer.3", MESSAGE);
					$userHandler->sendTranslatedMessage($gamer, "game.start.betrayer.4", MESSAGE);
					$userHandler->sendTranslatedTitle($gamer, "game.start.betrayer.title", null, TextFormat::RED, null);
					break;
			}
			$gamer->getWorld()->addSound($gamer->getLocation(), new PortalSound(), [$gamer]);
			$userHandler->updateState($gamer);
		}

	}

	/**
	 * @return Player[]
	 */
	public function getAllGamerPlayers() : array{
		$result = [];
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			if($this->gameInfo->hasGamerData($player)){
				$result[] = $player;
			}
		}
		return $result;
	}

	public function finishGame(){
		/** @var UserHandler $userHandler */
		$userHandler = UserHandler::getInstance();
		$clearUsernameList = "";
		foreach($this->getAllGamerPlayers() as $gamer){
			$gamerData = $this->gameInfo->getGamerData($gamer);
			switch($gamerData->gamerType){
				case GamerData::GAMER_TYPE_BETRAYAL:
				case GamerData::GAMER_TYPE_RUNNER:
					$gamerData->clear++;
					$clearUsernameList .= $gamer->getName() ." ";
					$this->database->putUserMoneyTransaction(
						new UserMoneyTransactionType(
							null,
							$gamerData->userId,
							UserMoneyTransactionType::TYPE_GAME_CLEAR,
							$gamerData->money,
							"GameId:" . $this->gameInfo->gameId
						)
					);
					break;
				case GamerData::GAMER_TYPE_HUNTER:
					break;
			}
		}

		$xpLevel = mt_rand(1,100);
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$userHandler->sendTranslatedMessage($player, "game.finish1", INFO);
			$userHandler->sendTranslatedMessage($player, "game.finish2", INFO);
			$player->sendMessage(INFO . ">> " . $clearUsernameList);
			$player->getWorld()->addSound($player->getLocation(), new XpLevelUpSound($xpLevel), [$player]);
		}

		foreach($this->gameInfo->gamerDataRecords as $gamerDataRecord){
			$this->database->putUserRunForMoneyResult(
				new UserRunForMoneyResultType(
					null,
					$gamerDataRecord->userId,
					$this->gameInfo->gameId,
					$gamerDataRecord->clear,
					$gamerDataRecord->death,
					$gamerDataRecord->catch,
					$gamerDataRecord->surrender,
					$gamerDataRecord->revival,
				)
			);
		}

		foreach($this->getAllGamerPlayers() as $player){
			$userHandler->respawn($player);
		}

		$this->gameInfo = null;
	}

	/**
	 * 1秒毎に呼び出される
	 */
	public function second(){
		$userHandler = UserHandler::getInstance();
		if($this->gameInfo instanceof GameInfo){
			switch($this->gameInfo->status){
				case GameInfo::GAME_STATUS_WAITING:
					$this->gameInfo->time++;
					$restTime = $this->gameInfo->maxTime - $this->gameInfo->time;
					switch($restTime){
						case 0:
							$this->startGame();
							break;
						case 1:
						case 2:
						case 3:
							foreach(Server::getInstance()->getOnlinePlayers() as $player){
								$player->getWorld()->addSound($player->getLocation(), new ClickSound(1.5), [$player]);
							}
							break;
					}
					foreach(Server::getInstance()->getOnlinePlayers() as $player){
						$userHandler->sendTranslatedTip($player, "game.waiting.countdown", INFO, [$this->gameInfo->maxTime - $this->gameInfo->time]);
					}
					break;
				case GameInfo::GAME_STATUS_RUNNING:
					foreach($this->getAllGamerPlayers() as $gamer){
						$gamerData = $this->gameInfo->getGamerData($gamer);
						switch($gamerData->gamerType){
							case GamerData::GAMER_TYPE_RUNNER:
								$gamerData->money += $this->gameInfo->moneyPerSecond;
								if($gamerData->surrenderCountDown > 0){
									$gamerData->surrenderCountDown--;
									if($gamerData->surrenderCountDown === 0){
										$this->successSurrender($gamer);
									}else{
										$userHandler->sendTranslatedMessage($gamer, 'game.surrender.countdown', MESSAGE, [$gamerData->surrenderCountDown]);
									}
								}
								break;
							case GamerData::GAMER_TYPE_HUNTER:
								break;
						}
					}
					$this->gameInfo->time++;
					if($this->gameInfo->maxTime === $this->gameInfo->time){
						$this->finishGame();
						return;
					}
					foreach(Server::getInstance()->getOnlinePlayers() as $player){
						$userHandler->sendTranslatedTip($player, "game.running.countdown", INFO, [$this->gameInfo->maxTime - $this->gameInfo->time]);
					}
					break;
			}
		}
	}

	public function applySurrender(Player $player){
		$userHandler = UserHandler::getInstance();
		$gamerData = $this->gameInfo->getGamerData($player);
		$gamerData->surrenderCountDown = 10;
		$userHandler->sendTranslatedMessage($player, 'game.surrender.apply', MESSAGE);
	}

	public function successSurrender(Player $player){
		$userHandler = UserHandler::getInstance();
		$gamerData = $this->gameInfo->getGamerData($player);
		$userHandler->sendTranslatedMessage($player, 'game.surrender.success', MESSAGE, [$gamerData->surrenderCountDown]);
		$this->database->putUserMoneyTransaction(
			new UserMoneyTransactionType(
				null,
				$userHandler->getUserIdByUser($player),
				UserMoneyTransactionType::TYPE_GAME_SURRENDER,
				$gamerData->money,
				"GameId:" . $this->gameInfo->gameId
			)
		);
		$gamerData->surrender++;
		$gamerData->money = 0;
		$userHandler->respawn($player);
	}

	public function revival(Player $player){
		$userHandler = UserHandler::getInstance();
		$gamerData = $this->gameInfo->getGamerData($player);
		$userHandler->sendTranslatedMessage($player, 'game.revival.success', MESSAGE);
		$gamerData->clear++;
		$gamerData->gamerType = GamerData::GAMER_TYPE_RUNNER;
		$player->teleport($this->gameInfo->field->getRunnerRandomSpawnPoint());
		$userHandler->updateState($player);
	}

	public function touchPlayer(Player $player, Player $attacker){
		if(!$this->gameInfo instanceof GameInfo or !$this->gameInfo->hasGamerData($player) or !$this->gameInfo->hasGamerData($attacker)){
			return;
		}
		/** @var UserHandler $userHandler */
		$userHandler = UserHandler::getInstance();
		$playerGamerType = $this->gameInfo->getGamerData($player)->gamerType;
		$attackerGamerType = $this->gameInfo->getGamerData($attacker)->gamerType;
		$runnerAndHunter = ($playerGamerType === GamerData::GAMER_TYPE_RUNNER and $attackerGamerType === GamerData::GAMER_TYPE_HUNTER);
		$runnerAndBetrayer = ($playerGamerType === GamerData::GAMER_TYPE_RUNNER and $attackerGamerType === GamerData::GAMER_TYPE_BETRAYAL);
		$betrayerAndHunter = ($playerGamerType === GamerData::GAMER_TYPE_BETRAYAL and $attackerGamerType === GamerData::GAMER_TYPE_HUNTER);

		if($runnerAndHunter or $runnerAndBetrayer or $betrayerAndHunter){
			$this->gameInfo->getGamerData($player)->death++;
			$this->gameInfo->getGamerData($attacker)->catch++;
			$this->database->putUserMoneyTransaction(
				new UserMoneyTransactionType(
					null,
					$userHandler->getUserIdByUser($attacker),
					UserMoneyTransactionType::TYPE_GAME_CATCH,
					30000,
					"GameId:" . $this->gameInfo->gameId
				)
			);
			$entity = new LightningBolt($player->getLocation());
			$entity->spawnToAll();
			switch(true){
				case $runnerAndHunter;
					$userHandler->sendTranslatedMessage($attacker, "game.catch.message", MESSAGE, [$player->getName()]);
					$userHandler->sendTranslatedMessage($player, "game.death.byHunter", MESSAGE);
					foreach(Server::getInstance()->getOnlinePlayers() as $online){
						$userHandler->sendTranslatedMessage($online, "game.catch.runnerAndHunter", ERROR, [$player->getName(), $attacker->getName()]);
					}
					break;
				case $runnerAndBetrayer;
					$userHandler->sendTranslatedMessage($attacker, "game.catch.message", MESSAGE, []);
					$userHandler->sendTranslatedMessage($player, "game.death.byBetrayer", MESSAGE);
					foreach(Server::getInstance()->getOnlinePlayers() as $online){
						$userHandler->sendTranslatedMessage($online, "game.catch.runnerAndBetrayer", ERROR, [$player->getName()]);
					}
					break;
				case $betrayerAndHunter;
					$userHandler->sendTranslatedMessage($attacker, "game.catch.message", MESSAGE, [$player->getName()]);
					$userHandler->sendTranslatedMessage($player, "game.death.byHunter", MESSAGE);
					foreach(Server::getInstance()->getOnlinePlayers() as $online){
						$userHandler->sendTranslatedMessage($online, "game.catch.betrayerAndHunter", ERROR, [$player->getName(), $attacker->getName()]);
					}
					break;
			}
			if($this->isFinalPhase()){
				$userHandler->respawn($player);
			}else{
				$this->gameInfo->getGamerData($player)->gamerType = GamerData::GAMER_TYPE_CAUGHT;
				$player->teleport($this->getGameInfo()->field->getJailPoint());
				$userHandler->updateState($player);
			}
		}
	}

	/**
	 * @return GameInfo|null
	 */
	public function getGameInfo() : GameInfo{
		return $this->gameInfo;
	}

	/**
	 * @return bool
	 */
	public function isGameRunning() : bool{
		return $this->gameInfo instanceof GameInfo && $this->gameInfo->status === GameInfo::GAME_STATUS_RUNNING;
	}

	/**
	 * ゲームの残り時間がn秒以下(ファイナルフェーズ)かどうかを判定する
	 * @return bool
	 */
	public function isFinalPhase() : bool{
		return $this->isGameRunning() and ($this->gameInfo->maxTime - $this->gameInfo->time) <= GameInfo::FINAL_PHASE_REST_TIME;
	}
}