<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\field;

use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Vector3;
use pocketmine\Server;

class Arashiyama extends FlatField implements Field{

	const FIELD_ID = 2;

	public static function getFieldId(): int {
		return self::FIELD_ID;
	}

	public function __construct(){
		$this->searchSpawnPointFromMapData("arashiyama.json");
	}

	public function getNameLangCode() : string {
		return "game.field.arashiyama.name";
	}

	public function getRunnerRandomSpawnPoint() : Vector3 {
		return $this->runnerSpawnPoints[array_rand($this->runnerSpawnPoints)];
	}

	public function getJailPoint(): Vector3{
		return new Vector3(1145, 6, -360);
	}

	public function getGameTime() : int{
		return 60 * 5 + mt_rand(-60, 60);
	}

	public function getHunterSpawnPoint(): Vector3{
		return new Vector3(mt_rand(964, 968), 1, -393 + mt_rand(0, 1));
	}

	public function getBetrayerSpawnPoint(): Vector3{
		return $this->runnerSpawnPoints[array_rand($this->runnerSpawnPoints)];
	}
}