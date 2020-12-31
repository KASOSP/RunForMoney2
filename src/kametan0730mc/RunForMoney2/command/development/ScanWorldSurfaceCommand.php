<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class ScanWorldSurfaceCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Collect world surface data",
			"/scanworldsurface <x1> <z1> <x2> <z2>"
		);
		$this->setPermission("kametan.command.scanworldsurface");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}

		if(!isset($args[0],$args[1],$args[2],$args[3])){
			UserHandler::getInstance()->sendTranslatedMessage($sender, "command.scanWorldSurface.usage", ERROR);

			return true;
		}else{
			$x1 = (int) $args[0];
			$z1 = (int) $args[1];
			$x2 = (int) $args[2];
			$z2 = (int) $args[3];
			$minX = min($x1, $x2);
			$maxX = max($x1, $x2);
			$minZ = min($z1, $z2);
			$maxZ = max($z1, $z2);
			$xOffset = $minX;
			$zOffset = $minZ;
			$xSize = $maxX - $minX + 1;
			$zSize = $maxZ - $minZ + 1;
			$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
			$data = [];
			$data["areaName"] = "Area";
			$data["minX"] = $minX;
			$data["maxX"] = $maxX;
			$data["minZ"] = $minZ;
			$data["maxZ"] = $maxZ;
			$data["xOffset"] = $xOffset;
			$data["zOffset"] = $zOffset;
			$data["xSize"] = $xSize;
			$data["zSize"] = $zSize;
			$data["data"] = [];
			for($x=0;$x<$xSize;$x++){
				for($z=0;$z<$zSize;$z++){
					if(!isset($data["data"][$x])){
						$data["data"][$x] = [];
					}

					$y = $world->getHighestBlockAt($x+$xOffset, $z+$zOffset);
					$block = $world->getBlockAt($x+$xOffset, $y, $z+$zOffset);
					$id = $block->getId();
					$meta = $block->getMeta();
					$data["data"][$x][$z] = [$y, $id, $meta];
				}
			}
			$json = json_encode($data);
			$fileName = "scan_world_surface_" . $sender->getName() . "_" . time() . ".json";
			file_put_contents($fileName, $json);
			$sender->sendMessage(">> Saved to " . $fileName);
			return true;
		}
	}
}