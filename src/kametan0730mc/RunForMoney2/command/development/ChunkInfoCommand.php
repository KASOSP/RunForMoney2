<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\player\Player;

class ChunkInfoCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"You can look up your chunk info",
			"/chunkinfo"
		);
		$this->setPermission("kametan.command.chunkinfo");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage("This command is only for player");
			return true;
		}
		$x = (int) round($sender->getLocation()->x);
		$z = (int) round($sender->getLocation()->z);
		$chunk = $sender->getWorld()->getChunk($x >> 4, $z >> 4);
		$chunkX = $chunk->getX();
		$chunkZ = $chunk->getZ();
		$sender->sendMessage(MESSAGE . ">> Here is a chunk (X, Z) = ($chunkX, $chunkZ)");
		return true;
	}
}