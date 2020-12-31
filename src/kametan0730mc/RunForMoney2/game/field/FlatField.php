<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\field;


use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Vector3;
use pocketmine\Server;

abstract class FlatField implements Field{

	/** @var array|null */
	private static $mapData = null;

	/** @var Vector3[] */
	protected $runnerSpawnPoints = [];

	/**
	 * @param string $fileName
	 * @return array
	 */
	public function loadMapData(string $fileName) : array{
		$path = __DIR__ . DIRECTORY_SEPARATOR . "data" .DIRECTORY_SEPARATOR . $fileName;
		return json_decode(file_get_contents($path), true);
	}

	/**
	 * @param string $fileName
	 */
	public function searchSpawnPointFromMapData(string $fileName){
		$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
		$mapData = $this->loadMapData($fileName);
		$xOffset = $mapData["xOffset"];
		$zOffset = $mapData["zOffset"];
		foreach($mapData["data"] as $x => $zList){
			foreach($zList as $z => $block){
				$y = $block[0];
				$id = $block[1];
				$meta = $block[2];
				switch($id){
					case BlockLegacyIds::GRASS_PATH:
					case BlockLegacyIds::GRASS:
					case BlockLegacyIds::GRAVEL:
					case BlockLegacyIds::SAND:
						$this->runnerSpawnPoints[] = new Vector3($x+$xOffset, $y + 2, $z+$zOffset);
						break;
				}
			}
		}
	}
}