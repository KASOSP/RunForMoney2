<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;


use kametan0730mc\RunForMoney2\entity\LightningBolt;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\CommandException;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\Server;

class LightningTestCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"You can confirm your xuid",
			"/lightningtest"
		);
		$this->setPermission("kametan.command.lightningtest");
	}


	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(ERROR . "This command is only for player");
			return true;
		}

		//$sender->getWorld()->addEntity(new LightningBolt($sender->getLocation()));
		//new LightningBolt(Location::fromObject($sender->getPosition(), $sender->getWorld(), 0, 0));


		$entity = new LightningBolt($sender->getLocation());
		$entity->spawnToAll();

		/**
		$pk = new AddActorPacket();
		$pk->entityRuntimeId = 1000000000;
		$pk->type = EntityIds::LIGHTNING_BOLT;
		$pk->position = $sender->getPosition();
		$pk->motion = new Vector3(0, 0, 0);
		$sender->getNetworkSession()->sendDataPacket($pk);
		return true;
		 * */
		return true;
	}
}