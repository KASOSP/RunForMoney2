<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\field;

use pocketmine\math\Vector3;

interface Field{

	public static function getFieldId() : int;
	public function getNameLangCode() : string;

	public function getRunnerRandomSpawnPoint() : Vector3;
	public function getHunterSpawnPoint() : Vector3;
	public function getBetrayerSpawnPoint() : Vector3;
	public function getJailPoint() : Vector3;

	public function getGameTime() : int;
}