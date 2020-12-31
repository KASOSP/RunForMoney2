<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\mission;

class MissionLoader{

	/** @var Mission[] */
	public static $missions = [];

	public static function init(){
		self::registerMission(StopBombingBridge::getMissionId(), new StopBombingBridge());
	}

	/**
	 * @param int $missionId
	 * @param Mission $mission
	 * @param bool $override
	 */
	public static function registerMission(int $missionId, Mission $mission, bool $override=false){
		if(isset(self::$missions[$missionId]) and !$override) return;
		self::$missions[$missionId] = $mission;
	}

	/**
	 * @param int $missionId
	 * @return bool
	 */
	public static function isRegistered(int $missionId) : bool{
		return isset(self::$missions[$missionId]);
	}

	/**
	 * @param int $missionId
	 * @return Mission
	 */
	public static function get(int $missionId) : Mission {
		return self::$missions[$missionId];
	}
}