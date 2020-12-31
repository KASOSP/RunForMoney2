<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2;

use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EventListener implements Listener{

	public function onPlayerPreLogin(PlayerPreLoginEvent $event){
		UserHandler::getInstance()->attemptLogin($event);
	}

	public function onPlayerLogin(PlayerLoginEvent $event){
		UserHandler::getInstance()->loginEvent($event->getPlayer());
	}

	public function onPlayerJoin(PlayerJoinEvent $event){
		$event->setJoinMessage(null);
		UserHandler::getInstance()->joinEvent($event->getPlayer());
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
		$event->setQuitMessage(null);
		UserHandler::getInstance()->quitEvent($event->getPlayer());
	}

	public function onPlayerDrop(PlayerDropItemEvent $event){
		$event->cancel();
	}

	public function onPlayerChat(PlayerChatEvent $event){
		UserHandler::getInstance()->chatEvent($event);
	}

	public function onPlayerInteract(PlayerInteractEvent $event){
		UserHandler::getInstance()->interactEvent($event);
	}

	public function onPlayerDataSave(PlayerDataSaveEvent $event){
		$spawn = UserHandler::getInstance()->getSpawnPoint();
		$nbt = $event->getSaveData();
		$nbt->removeTag("Pos");
		$nbt->setTag("Pos", new ListTag([
			new DoubleTag($spawn->getFloorX()),
			new DoubleTag($spawn->getFloorY()),
			new DoubleTag($spawn->getFloorZ())
		]));
		$event->setSaveData($nbt);
	}

	public function onEntityDamage(EntityDamageEvent $event){
		$event->cancel();
		$entity = $event->getEntity();
		if($entity->getLocation()->getY() < 0){
			if($entity instanceof Player){
				$userHandler = UserHandler::getInstance();
				$userHandler->respawn($entity);
				$userHandler->sendTranslatedMessage($entity, "system.abyss.respawn", MESSAGE);
			}else{
				$entity->kill();
			}
		}
		if($event instanceof EntityDamageByEntityEvent){
			$attacker = $event->getDamager();
			if($entity instanceof Player and $attacker instanceof Player){
				if($entity->getLocation()->distance($attacker->getLocation()) < 4){
					GameHandler::getInstance()->touchPlayer($entity, $attacker);
				}
			}
		}
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event){
		$packet = $event->getPacket();
		if($packet instanceof PlayerActionPacket){
			$action = $packet->action;
			$player = $event->getOrigin()->getPlayer();
			switch($action){
				case PlayerActionPacket::ACTION_START_SWIMMING:
					$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SWIMMING, true);
					break;
				case PlayerActionPacket::ACTION_STOP_SWIMMING:
					$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SWIMMING, false);
					break;
			}
		}
	}

	public function onPluginDisable(PluginDisableEvent $event){
		$userHandler = UserHandler::getInstance();
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->kick(TextFormat::GREEN . $userHandler->translateText($player, "system.server.down"));
		}
	}
}