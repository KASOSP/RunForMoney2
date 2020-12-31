<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\user;

use kametan0730mc\RunForMoney2\crown\Crown;
use kametan0730mc\RunForMoney2\database\Database;
use kametan0730mc\RunForMoney2\database\type\BannedUserType;
use kametan0730mc\RunForMoney2\form\PasswordRegisterForm;
use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\GamerData;
use kametan0730mc\RunForMoney2\item\MenuPad;
use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\database\type\UserLogType;
use kametan0730mc\RunForMoney2\database\type\UserMoneyTransactionType;
use kametan0730mc\RunForMoney2\database\type\UserType;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\SkinAdapterSingleton;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\uuid\UUID;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\sound\AnvilUseSound;

class UserHandler{
	use SingletonTrait;

	/** @var Database */
	private $database;

	/** @var Vector3 */
	private $spawnPoint;

	/** @var UserSaveData[]  */
	private $saveDataRecords = []; // ユーザーIDがキー

	/** @var UserTempData[] */
	private $tempDataRecords = []; // Usernameがキー

	/**
	 * @param Database $database
	 */
	public function init(Database $database, Vector3 $spawnPoint){
		if(!$this->database instanceof Database){
			$this->database = $database;
		}
		$this->spawnPoint = $spawnPoint;
	}

	public function attemptLogin(PlayerPreLoginEvent $event){
		$info = $event->getPlayerInfo();

		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			if(strtolower($player->getName()) === strtolower($info->getUsername())){
				$event->setKickReason(PlayerPreLoginEvent::KICK_REASON_BANNED, ERROR . "Error(1001) : Your login session is already exists on this server");
				return;
			}
		}

		$banRecords = $this->database->getBannedUsers($info->getXuid(), $info->getUsername(), $event->getIp());
		if(count($banRecords) > 0){
			$event->setKickReason(PlayerPreLoginEvent::KICK_REASON_BANNED, "Error(1002) : You are banned (Reason:" . $banRecords[0]->reason . ")");
			return;
		}
		$tempData = new UserTempData();
		$tempData->loggedInAddress = $info->getExtraData()["ServerAddress"];
		$this->tempDataRecords[strtolower($info->getUsername())] = $tempData;
	}

	public function loginEvent(Player $player){
		$registeredUser = $this->database->getUserByXuid($player->getXuid());
		if(!$registeredUser instanceof UserType){
			$this->database->putUser(new UserType(null, $player->getXuid(), strtolower($player->getName()), $player->getNetworkSession()->getIp(), new UserSaveData(), null));
			$registeredUser = $this->database->getUserByXuid($player->getXuid());
			if(!$registeredUser instanceof UserType){
				$player->kick("ERROR(1003) : Failed to create your account");
				return;
			}
			$this->database->putUserMoneyTransaction(new UserMoneyTransactionType(null, $registeredUser->id, UserMoneyTransactionType::TYPE_ACCOUNT_INIT, 10000, ""));
			$this->tempDataRecords[strtolower($player->getName())]->isFirstLogin = true;
		}

		$userId = $registeredUser->id;
		$this->saveDataRecords[$userId] = $registeredUser->saveData;
		$loginHistory = new UserLogType(null, $userId, UserLogType::TYPE_SERVER_LOGIN, $player->getNetworkSession()->getIp());
		$this->database->putUserLog($loginHistory);

		$this->tempDataRecords[strtolower($player->getName())]->userId = $userId;

		$this->tempDataRecords[strtolower($player->getName())]->crowns = $this->database->getUserCrowns($userId);

		if(1==2){
			$human = new Human(new Location(131, 72, -236, 180, 0, $player->getWorld()), $player->getSkin());
			$human->lookAt($player->getLocation());
			$human->spawnTo($player);

			$status = $this->getUserRunForMoneyStatus($userId);
			$clear = $status[0];
			$death = $status[1];
			$catch = $status[2];
			$surrender = $status[3];
			$revival = $status[4];
			$exp = $clear * 20 + $death * 5 + $surrender * 12 + $catch * 8 + $revival + 100;
			$lev = floor($exp / 99);
			$text = "§a" . $player->getName() . "さんのステータス\n§eレベル : " . $lev . "\n§dExp : " . $exp . "\n" . "§b逃走成功回数 : " . $clear . " \n§c自首回数  : " . $surrender . "\n" . "§2復活回数  : " . $revival . "\n" . "§d確保された回数 : " . $death . "\n§e所持金 : " . (0) . "\n§1確保した回数 : " . $catch . "\n§aログイン時間(分) : " . (999) . "\n§0ここの表示はログイン中は更新されません";
			$particle = new FloatingTextParticle($text);
			$player->getWorld()->addParticle(new Vector3(135, 72, -236), $particle);
		}


	}

	public function joinEvent(Player $player){
		$this->respawn($player);
		$player->sendMessage("§b>> Welcome to §a§lKametan Server§r§b !");
		$player->sendTitle("§l§6かめたんサーバー§l§b2");
		if($this->tempDataRecords[strtolower($player->getName())]->isFirstLogin){
			$player->sendForm(new PasswordRegisterForm($this->getSaveDataByUser($player)->getLang()));
		}
		$menuPad = ItemFactory::getInstance()->get(MenuPad::ITEM_ID, 0, 1);
		if(!$player->getInventory()->contains($menuPad)){
			$player->getInventory()->addItem($menuPad);
		}

		$sound = new AnvilUseSound();
		$player->getWorld()->addSound($player->getLocation(), $sound);

		$status = $this->getUserRunForMoneyStatus($this->getUserIdByUser($player));
		$clear = $status[0];
		$death = $status[1];
		$catch = $status[2];
		$surrender = $status[3];
		$revival = $status[4];
		$text = "§a" . $player->getName() . "さんのステータス\n§b逃走成功回数 : " . $clear . " \n§c自首回数  : " . $surrender . "\n" . "§2復活回数  : " . $revival . "\n" . "§d確保された回数 : " . $death . "\n§e所持金 : " . (0) . "\n§1確保した回数 : " . $catch . "\n§aログイン時間(分) : " . (999);

		$entityId = Entity::nextRuntimeId();
		$uuid = UUID::fromRandom();
		$name = $text;
		$player->getNetworkSession()->sendDataPacket(PlayerListPacket::add([PlayerListEntry::createAdditionEntry($uuid, $entityId, $name, SkinAdapterSingleton::get()->toSkinData($player->getSkin()))]));
		$pk = new AddPlayerPacket();
		$pk->uuid = $uuid;
		$pk->username = $name;
		$pk->entityRuntimeId = $entityId;
		$pk->position = new Vector3(132.5, 73, -236);
		$pk->item = ItemStack::null();
		$flags = (
			1 << EntityMetadataFlags::IMMOBILE
		);
		$pk->metadata = [
			EntityMetadataProperties::FLAGS => new LongMetadataProperty($flags),
			EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.01) //zero causes problems on debug builds
		];
		$player->getNetworkSession()->sendDataPacket($pk);
		$player->getNetworkSession()->sendDataPacket(PlayerListPacket::remove([PlayerListEntry::createRemovalEntry($uuid)]));

	}

	public function quitEvent(Player $player){
		if($this->getSaveDataByUser($player)->isSaveDataModified()){
			$this->database->updateUserSaveData($this->getUserIdByUser($player), $this->getSaveDataByUser($player));
		}
		$loginHistory = new UserLogType(null, $this->getUserIdByUser($player), UserLogType::TYPE_SERVER_LOGOUT, $player->getNetworkSession()->getIp());
		$this->database->putUserLog($loginHistory);
		unset($this->saveDataRecords[$this->getUserIdByUser($player)]);
		unset($this->tempDataRecords[strtolower($player->getName())]);
	}

	public function chatEvent(PlayerChatEvent $event){
		$player = $event->getPlayer();
		$tempData = $this->getTempData($player);
		if(!Server::getInstance()->isOp($player->getName()) and $tempData->chatRestrictionSeconds !== 0){
			$this->sendTranslatedMessage($player, "system.chat.consecutivePosts", ERROR);
			$event->cancel();
			return;
		}
		$tempData->chatRestrictionSeconds = 5;
	}

	public function interactEvent(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$userTempData = $this->getTempData($player);
		if($userTempData->interactRestrictionCount > 0){
			$event->cancel();
		}else{
			$userTempData->interactRestrictionCount = 4; // 0.4s
		}
	}

	/**
	 * @param int $userId
	 * @return array
	 */
	public function getUserRunForMoneyStatus(int $userId) : array{
		$clear = $death = $catch = $surrender = $revival = 0;
		$result = $this->database->getUserRunForMoneyCache($userId);
		if($result === null){
			return [0, 0, 0, 0, 0];
		}
		return [$result->clear, $result->death, $result->catch, $result->surrender, $result->revival];
	}

	public function getUserMoney(int $userId) : int{
		$cache = $this->database->getUserMoneyCache($userId);
		if(isset($cache)){
			return $cache->amount;
		}
		return 0;
	}

	public function reduceUserMoney(int $userId, int $amount, int $type){
		$this->addUserMoney($userId, -$amount, $type);
	}

	public function addUserMoney(int $userId, int $amount, int $type){
		$this->database->putUserMoneyTransaction(new UserMoneyTransactionType(null, $userId, $amount, $type, null));
	}

	public function updateUserPassword($userId, $hashedPassword){
		$this->database->updateUserPassword($userId, $hashedPassword);
	}

	public function getCrownsText(Player $player){
		$text = "";
		foreach($this->getTempData($player)->crowns as $crown){
			$text .= Crown::toText($crown->type, $crown->color);
		}
		return $text;
	}

	public function resetState(Player $player){
		$player->getHungerManager()->setFood(20);
		$player->setGamemode(GameMode::ADVENTURE());
		$player->getEffects()->clear();

		/** @var GameHandler $gameHandler */
		$gameHandler = GameHandler::getInstance();
		if($gameHandler->isGameRunning() and $gameHandler->getGameInfo()->hasGamerData($player)){
			$gameHandler->getGameInfo()->getGamerData($player)->gamerType = GamerData::GAMER_TYPE_WAITING;
		}
		$this->updateState($player);
	}

	public function respawn(Player $player){
		$this->resetState($player);
		$player->teleport($this->spawnPoint);
	}

	public function updateState(Player $player){
		$gamerTag = "待機中";
		$gameHandler = GameHandler::getInstance();
		$userHandler = UserHandler::getInstance();
		if($gameHandler->isGameRunning() and $gameHandler->getGameInfo()->hasGamerData($player)){
			$gamerType = $gameHandler->getGameInfo()->getGamerData($player)->gamerType;
			switch($gamerType){
				case GamerData::GAMER_TYPE_CAUGHT:
					$gamerTag = "囚人";
					$player->setNameTagAlwaysVisible(false);
					$player->getHungerManager()->setEnabled(false);
					$player->getHungerManager()->setFood(20);
					break;
				case GamerData::GAMER_TYPE_RUNNER:
				case GamerData::GAMER_TYPE_BETRAYAL:
					$gamerTag = "逃走者";
					$player->setNameTagAlwaysVisible(false);
					$player->getHungerManager()->setEnabled(true);
					break;
				case GamerData::GAMER_TYPE_HUNTER:
					$gamerTag = TextFormat::RED . "ハンター" . TextFormat::WHITE;
					$player->setNameTagAlwaysVisible(true);
					$player->getHungerManager()->setEnabled(false);
					$player->getHungerManager()->setFood(20);
					break;
				case GamerData::GAMER_TYPE_WAITING:
					$gamerTag = "待機中";
					$player->setNameTagAlwaysVisible(true);
					$player->getHungerManager()->setEnabled(false);
					$player->getHungerManager()->setFood(20);
					break;
				default:
					$gamerTag = "Unknown";
					$player->setNameTagAlwaysVisible(true);
					$player->getHungerManager()->setEnabled(false);
					$player->getHungerManager()->setFood(20);
					break;
			}
		}else{
			$player->setNameTagAlwaysVisible(true);
			$player->getHungerManager()->setEnabled(false);
			$player->getHungerManager()->setFood(20);
		}

		$nametag = $this->getSaveDataByUser($player)->getNametag();
		$crowns = $this->getCrownsText($player);

		$player->setNameTag("[" . $gamerTag .  "] " . $player->getName());
		$player->setDisplayName("[" . ($nametag !== "" ? $nametag . " "  : "") . ($crowns !== "" ? $crowns : "") . "§r§f" . $gamerTag .  "] " . $player->getName());
	}

	/** @var int  */
	private $count = 0;

	/**
	 * 0.1秒毎に呼び出される
	 */
	public function timer(){
		if($this->count % 10 === 0){ // 1秒毎に処理する
			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				$tempData = $this->getTempData($player);
				if($tempData->chatRestrictionSeconds > 0){
					$tempData->chatRestrictionSeconds--;
				}
			}
		}

		if($this->count % 300 === 0){
			$rand = mt_rand(0, 10);
			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				$this->sendTranslatedMessage($player, "server.regularly." . $rand, INFO);
			}
		}

		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$tempData = $this->getTempData($player);
			if($tempData->interactRestrictionCount > 0){
				$tempData->interactRestrictionCount--;
			}
		}
		$this->count++;
	}

	/**
	 * @param Player $player
	 */
	public function ban(Player $player, $reason){
		$xuid = $player->getXuid();
		$username = strtolower($player->getName());
		$userId = $this->getUserIdByUser($player);
		$ipAddress = $player->getNetworkSession()->getIp();
		$expirationDate = time() + 60 * 60 * 24 * 30; // 30days
		$sign = $player->getName();
		$banRecord = new BannedUserType(null, $userId, $xuid, $username, $ipAddress, $expirationDate, $sign, $reason);
		$this->database->putBannedUser($banRecord);
		$player->kick("You are banned (Reason:" . $banRecord->reason . ")");
	}

	/**
	 * @return Vector3
	 */
	public function getSpawnPoint(): Vector3{
		return $this->spawnPoint;
	}

	/**
	 * @param Player $player
	 * @return bool
	 */
	public function isAdmin(Player $player) : bool{
		return Server::getInstance()->isOp($player->getName());
	}

	/**
	 * @param Player|string $user
	 * @return int
	 */
	public function getUserIdByUser($user){
		if($user instanceof Player){ // $userがPlayerオブジェクトの場合
			$user = $user->getName();
		}
		return $this->tempDataRecords[strtolower($user)]->userId;
	}

	/**
	 * @param int $id
	 * @return UserSaveData
	 */
	public function getSaveData(int $id){
		return $this->saveDataRecords[$id];
	}

	/**
	 * @param Player|string $user
	 * @return UserSaveData
	 */
	public function getSaveDataByUser($user){
		if($user instanceof Player){ // $userがPlayerオブジェクトの場合
			$user = $user->getName();
		}
		return $this->saveDataRecords[$this->tempDataRecords[strtolower($user)]->userId];
	}

	/**
	 * @param Player|string
	 * @return UserTempData
	 */
	public function getTempData($user){
		if($user instanceof Player){ // $userがPlayerオブジェクトの場合
			$user = $user->getName();
		}
		return $this->tempDataRecords[strtolower($user)];
	}

	/**
	 * @param Player $player
	 * @param string $message
	 * @param array $inputs
	 * @return string
	 */
	public function translateText(Player $player, string $message, array $inputs = []){
		$lang = $this->getSaveDataByUser($player)->getLang();
		return Lang::translate($lang, $message, $inputs);
	}

	/**
	 * @param Player|CommandSender $player
	 * @param string $message
	 * @param string $color
	 * @param array $inputs
	 */
	public function sendTranslatedMessage($player, string $message, string $color, array $inputs = []){
		if($player instanceof Player){
			$lang = $this->getSaveDataByUser($player)->getLang();
		}else{
			$lang = "eng";
		}
		$player->sendMessage($color . ">> " .  Lang::translate($lang, $message, $inputs));
	}

	/**
	 * @param Player $player
	 * @param string $message
	 * @param string $color
	 * @param array $inputs
	 */
	public function sendTranslatedTip(Player $player, string $message, string $color, array $inputs = []){
		$lang = $this->getSaveDataByUser($player)->getLang();
		$player->sendTip($color .  Lang::translate($lang, $message, $inputs));
	}

	/**
	 * @param Player $player
	 * @param string $message
	 * @param string $color
	 * @param array $inputs
	 */
	public function sendTranslatedPopup(Player $player, string $message, string $color, array $inputs = []){
		$lang = $this->getSaveDataByUser($player)->getLang();
		$player->sendPopup($color .  Lang::translate($lang, $message, $inputs));
	}

	/**
	 * @param Player $player
	 * @param string $main
	 * @param string|null $sub
	 * @param string $mainColor
	 * @param string|null $subColor
	 * @param array $mainInput
	 * @param array $subInput
	 */
	public function sendTranslatedTitle(Player $player, string $main, ?string $sub, string $mainColor, ?string $subColor, array $mainInput = [], array $subInput = []){
		$lang = $this->getSaveDataByUser($player)->getLang();
		if(isset($sub)){
			$player->sendTitle($mainColor . Lang::translate($lang, $main, $mainInput), $subColor . Lang::translate($lang, $sub, $subInput));
		}else{
			$player->sendTitle($mainColor . Lang::translate($lang, $main, $mainInput));
		}
	}
}