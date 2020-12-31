<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game;

use kametan0730mc\RunForMoney2\game\field\Field;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\player\Player;

class GameInfo{

	const GAME_STATUS_WAITING = 0;
	const GAME_STATUS_RUNNING = 1;
	const GAME_STATUS_FINISHED = 2;

	const FINAL_PHASE_REST_TIME = 40;

	/** @var int */
	public $gameId;

	/** @var int */
	public $status = self::GAME_STATUS_WAITING;

	/** @var int */
	public $time;

	/** @var int  */
	public $maxTime = 300;

	/** @var Field */
	public $field;

	/** @var GamerData[]  */
	public $gamerDataRecords = [];

	/** @var int  */
	public $moneyPerSecond = 200;

	/**
	 * @param Player|string $user
	 */
	public function initGamerData($user){
		if($user instanceof Player){ // $userがPlayerオブジェクトの場合
			$user = $user->getName();
		}
		$gamerData = new GamerData();
		$gamerData->userId = UserHandler::getInstance()->getUserIdByUser($user);
		$this->gamerDataRecords[strtolower($user)] = $gamerData;
	}

	/**
	 * @param Player|string $user
	 * @return GamerData
	 */
	public function getGamerData($user) : GamerData{
		if($user instanceof Player){ // $userがPlayerオブジェクトの場合
			$user = $user->getName();
		}
		return $this->gamerDataRecords[strtolower($user)];
	}

	public function hasGamerData($user) : bool{
		if($user instanceof Player){ // $userがPlayerオブジェクトの場合
			$user = $user->getName();
		}
		return isset($this->gamerDataRecords[strtolower($user)]);
	}
}