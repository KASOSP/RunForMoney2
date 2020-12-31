<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\field;

use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Vector3;
use pocketmine\Server;

class Edo extends FlatField implements Field{

	const FIELD_ID = 1;

	public static function getFieldId(): int {
		return self::FIELD_ID;
	}

	public function __construct(){
		$this->searchSpawnPointFromMapData("edo.json");
	}
	public function getNameLangCode(): string {
		return "game.field.edo.name";
	}

	public function getRunnerRandomSpawnPoint(): Vector3 {
		return $this->runnerSpawnPoints[array_rand($this->runnerSpawnPoints)];
	}

	public function getJailPoint(): Vector3{
		return new Vector3(1120, 46, -894);
	}

	public function getGameTime() : int {
		return 60 * 5 + mt_rand(-60, 60);
	}

	public function getRandomMission(){

	}

	public function getHunterSpawnPoint(): Vector3{
		return new Vector3(933, 8, -719);
	}

	public function getBetrayerSpawnPoint(): Vector3{
		return new Vector3(1107, 76, -881);
	}
}