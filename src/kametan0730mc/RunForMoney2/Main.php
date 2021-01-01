<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2;

use InvalidStateException;
use kametan0730mc\RunForMoney2\block\Coal;
use kametan0730mc\RunForMoney2\block\Door;
use kametan0730mc\RunForMoney2\block\Lapis;
use kametan0730mc\RunForMoney2\command\BanCommand;
use kametan0730mc\RunForMoney2\command\CleanEntityCommand;
use kametan0730mc\RunForMoney2\command\development\ChunkInfoCommand;
use kametan0730mc\RunForMoney2\command\development\CrownTestCommand;
use kametan0730mc\RunForMoney2\command\development\LightningTestCommand;
use kametan0730mc\RunForMoney2\command\development\MyXuidCommand;
use kametan0730mc\RunForMoney2\command\development\SaveDataDumpCommand;
use kametan0730mc\RunForMoney2\command\development\ScanWorldSurfaceCommand;
use kametan0730mc\RunForMoney2\command\development\SetBaseBlockCommand;
use kametan0730mc\RunForMoney2\command\development\SoundTestCommand;
use kametan0730mc\RunForMoney2\command\GameCommand;
use kametan0730mc\RunForMoney2\command\JoinCommand;
use kametan0730mc\RunForMoney2\command\MoneyCommand;
use kametan0730mc\RunForMoney2\command\NametagCommand;
use kametan0730mc\RunForMoney2\command\RemoveInventoryCommand;
use kametan0730mc\RunForMoney2\command\SetPhotoStudioCommand;
use kametan0730mc\RunForMoney2\command\SobaCommand;
use kametan0730mc\RunForMoney2\command\SpawnCommand;
use kametan0730mc\RunForMoney2\command\TeleportAllToMeCommand;
use kametan0730mc\RunForMoney2\command\UnbanCommand;
use kametan0730mc\RunForMoney2\crown\Crown;
use kametan0730mc\RunForMoney2\database\Database;
use kametan0730mc\RunForMoney2\database\MySQL;
use kametan0730mc\RunForMoney2\entity\Agent;
use kametan0730mc\RunForMoney2\entity\LightningBolt;
use kametan0730mc\RunForMoney2\entity\projectile\DragonFireball;
use kametan0730mc\RunForMoney2\entity\projectile\FishingHook;
use kametan0730mc\RunForMoney2\game\field\FieldLoader;
use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\game\mission\MissionLoader;
use kametan0730mc\RunForMoney2\handler\BehaviorPacksHandler;
use kametan0730mc\RunForMoney2\item\Amazon;
use kametan0730mc\RunForMoney2\item\CellPhone;
use kametan0730mc\RunForMoney2\item\DoorKey;
use kametan0730mc\RunForMoney2\item\DoorKeyPlus;
use kametan0730mc\RunForMoney2\item\Ekurea;
use kametan0730mc\RunForMoney2\item\Emerald;
use kametan0730mc\RunForMoney2\item\ExMedicine1;
use kametan0730mc\RunForMoney2\item\ExMedicine2;
use kametan0730mc\RunForMoney2\item\ExMedicine3;
use kametan0730mc\RunForMoney2\item\ExMedicine4;
use kametan0730mc\RunForMoney2\item\ExMedicine5;
use kametan0730mc\RunForMoney2\item\ExMedicineS;
use kametan0730mc\RunForMoney2\item\IceGun;
use kametan0730mc\RunForMoney2\item\ItemLoader;
use kametan0730mc\RunForMoney2\item\Lollipop;
use kametan0730mc\RunForMoney2\item\MenuPad;
use kametan0730mc\RunForMoney2\item\NetGun;
use kametan0730mc\RunForMoney2\item\RabbitFoot;
use kametan0730mc\RunForMoney2\item\Soba;
use kametan0730mc\RunForMoney2\item\Tejyou;
use kametan0730mc\RunForMoney2\item\Vaccine;
use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\scheduler\MainTask;
use kametan0730mc\RunForMoney2\user\UserHandler;
use kametan0730mc\RunForMoney2\world\WorldHandler;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

define("MESSAGE", TextFormat::GREEN);
define("INFO", TextFormat::AQUA);
define("WARNING", TextFormat::YELLOW);
define("ERROR", TextFormat::DARK_RED);

class Main extends PluginBase{

	/** @var Database */
	private $database;

	private function initCommands(bool $isDevelopment = false){
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("ban"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("ban-ip"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("banlist"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("pardon"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("pardon-ip"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("enchant"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("me"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("kill"));

		if($isDevelopment){
			$this->getServer()->getCommandMap()->register("kametan", new ChunkInfoCommand("chunkinfo"));
			$this->getServer()->getCommandMap()->register("kametan", new LightningTestCommand("lightningtest"));
			$this->getServer()->getCommandMap()->register("kametan", new MyXuidCommand("myxuid"));
			$this->getServer()->getCommandMap()->register("kametan", new SaveDataDumpCommand("savedatadump"));
			$this->getServer()->getCommandMap()->register("kametan", new ScanWorldSurfaceCommand("scanworldsurface"));
			$this->getServer()->getCommandMap()->register("kametan", new SetBaseBlockCommand("setbaseblock"));
			$this->getServer()->getCommandMap()->register("kametan", new SoundTestCommand("soundtest"));
		}

		$this->getServer()->getCommandMap()->register("kametan", new BanCommand("ban"));
		$this->getServer()->getCommandMap()->register("kametan", new CleanEntityCommand("cleanentity"));
		$this->getServer()->getCommandMap()->register("kametan", new GameCommand("game"));
		$this->getServer()->getCommandMap()->register("kametan", new UnbanCommand("unban"));
		$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand("ban"));
		$this->getServer()->getCommandMap()->register("kametan", new NametagCommand("nametag"));
		$this->getServer()->getCommandMap()->register("kametan", new RemoveInventoryCommand("removeinventory"));
		$this->getServer()->getCommandMap()->register("kametan", new SobaCommand("soba"));
		$this->getServer()->getCommandMap()->register("kametan", new MoneyCommand("money"));
		$this->getServer()->getCommandMap()->register("kametan", new SpawnCommand("spawn"));
		$this->getServer()->getCommandMap()->register("kametan", new JoinCommand("join"));
		$this->getServer()->getCommandMap()->register("kametan", new TeleportAllToMeCommand("tpa"));
		$this->getServer()->getCommandMap()->register("kametan", new SetPhotoStudioCommand("setphotostudio"));
	}

	private function initEntities(){
		/** @var EntityFactory $factory */
		$factory = EntityFactory::getInstance();
		$factory->register(Agent::class, function(World $world, CompoundTag $nbt) : Agent{
			return new Agent(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['Agent', EntityIds::AGENT], EntityLegacyIds::AGENT);

		$factory->register(LightningBolt::class, function(World $world, CompoundTag $nbt) : LightningBolt{
			return new LightningBolt(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['Lightning Bolt', EntityIds::LIGHTNING_BOLT], EntityLegacyIds::LIGHTNING_BOLT);

		$factory->register(DragonFireball::class, function(World $world, CompoundTag $nbt) : DragonFireball{
			return new DragonFireball(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
		}, ['Dragon Fireball', EntityIds::DRAGON_FIREBALL], EntityLegacyIds::DRAGON_FIREBALL);

		$factory->register(FishingHook::class, function(World $world, CompoundTag $nbt) : FishingHook{
			return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
		}, ['Fishing Hook', EntityIds::FISHING_HOOK], EntityLegacyIds::FISHING_HOOK);

		/*
		$factory->register(Husk::class, function(World $world, CompoundTag $nbt) : Husk{
			return new Husk(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['Husk', EntityIds::HUSK], EntityLegacyIds::HUSK);*/
	}

	private function initBlocks(){
		$factory = BlockFactory::getInstance();
		$factory->register(new Door(new BID(Ids::IRON_DOOR_BLOCK, 0, ItemIds::IRON_DOOR), "Iron Door", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 25.0)), true);
		$factory->register(new Coal(new BID(Ids::COAL_BLOCK), "Coal Block"), true);
		$factory->register(new Lapis(new BID(Ids::LAPIS_BLOCK), "Lapis Block"), true);

	}

	private function initItems(){
		$factory = ItemFactory::getInstance();
		$factory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::AGENT), "Agent Spawn Egg") extends SpawnEgg{
			protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
				return new Agent(Location::fromObject($pos, $world, $yaw, $pitch));
			}
		});

		$factory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::LIGHTNING_BOLT), "Lightning Bolt Spawn Egg") extends SpawnEgg{
			protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
				return new LightningBolt(Location::fromObject($pos, $world, $yaw, $pitch));
			}
		});

		/*
		$factory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::HUSK), "Husk Spawn Egg") extends SpawnEgg{
			protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
				return new Husk(Location::fromObject($pos, $world, $yaw, $pitch));
			}
		});*/
		$factory->register(new Emerald(new ItemIdentifier(ItemIds::EMERALD, 0), "Emerald"), true);
		$factory->register(new RabbitFoot(new ItemIdentifier(ItemIds::RABBIT_FOOT, 0), "Rabbit Foot"), true);

		$factory->register(new Amazon(new ItemIdentifier(900, 0), "Amazon"));
		$factory->register(new CellPhone(new ItemIdentifier(910, 0), "Cell Phone"));
		$factory->register(new DoorKeyPlus(new ItemIdentifier(920, 0), "Door Key Plus"));
		$factory->register(new DoorKey(new ItemIdentifier(930, 0), "Door Key"));
		$factory->register(new Ekurea(new ItemIdentifier(940, 0), "Ekurea"));
		$factory->register(new ExMedicine1(new ItemIdentifier(950, 0), "Ex Medicine 1"));
		$factory->register(new ExMedicine2(new ItemIdentifier(960, 0), "Ex Medicine 2"));
		$factory->register(new ExMedicine3(new ItemIdentifier(970, 0), "Ex Medicine 3"));
		$factory->register(new ExMedicine4(new ItemIdentifier(980, 0), "Ex Medicine 4"));
		$factory->register(new ExMedicine5(new ItemIdentifier(990, 0), "Ex Medicine 5"));
		$factory->register(new ExMedicineS(new ItemIdentifier(1000, 0), "Ex Medicine S"));
		$factory->register(new IceGun(new ItemIdentifier(1010, 0), "Ice Gun"));
		$factory->register(new Lollipop(new ItemIdentifier(1020, 0), "Lollipop"));
		$factory->register(new NetGun(new ItemIdentifier(1030, 0), "Net Gun"));
		$factory->register(new Soba(new ItemIdentifier(1040, 0), "Soba"));
		$factory->register(new Tejyou(new ItemIdentifier(1050, 0), "Tejyou"));
		$factory->register(new Vaccine(new ItemIdentifier(1060, 0), "Vaccine"));
		$factory->register(new MenuPad(new ItemIdentifier(1070, 0), "Menu Pad"));
	}

	private function initEnchantment(){
		EnchantmentIdMap::getInstance()->register(EnchantmentIds::SHARPNESS, VanillaEnchantments::THORNS());
	}

	protected function onEnable(){
		date_default_timezone_set("Japan");

		$this->getServer()->getWorldManager()->setAutoSave(false);
		if(!$this->getServer()->getOnlineMode()){
			throw new InvalidStateException("This plugin cannot work without xbox auth");
		}

		$conf = new Config($this->getDataFolder() . "config.yml", Config::YAML, array(
			"development" => false,
			"server" => [
				"name" => "KametanServer01",
				"id" => mt_rand(100000,999999),
			],
			"spawn" => [
				"x" => 147,
				"y" => 71,
				"z" => -257,
			],
			"database" => [
				"type" => "mysql",
				"host" => "host",
				"username" => "username",
				"password" => "password",
				"dbname" => "dbname",
			]
		));

		if($conf->get("server")["id"] === ""){
			throw new InvalidStateException("Server id not found in configuration file");
		}

		$isDevelopment = (bool) $conf->get("development");
		if($isDevelopment){
			$this->getLogger()->info("This server is running in development mode!!!!");
		}

		Lang::init();
		FieldLoader::init();
		MissionLoader::init();
		ItemLoader::init($this->getDataFolder());

		$this->initEntities();
		$this->initItems();
		$this->initBlocks();
		$this->initEnchantment();
		$this->initCommands($isDevelopment);

		$dbConf = $conf->get("database");
		$databaseType = $dbConf["type"];
		if($databaseType === "mysql"){
			if($dbConf["host"] === "host"){
				throw new InvalidStateException("Please configure mysql authentication information in configuration file");
			}
			$this->database = new MySQL();
			if(!$this->database->initDatabase($dbConf["host"], $dbConf["username"], $dbConf["password"], $dbConf["dbname"])){
				throw new InvalidStateException("Cannot connect to mysql server");
			}
		}else{
			throw new InvalidStateException("Unknown database type (".$databaseType.")");
		}

		$spawnConf = $conf->get("spawn");
		$spawnPoint = new Vector3($spawnConf["x"], $spawnConf["y"], $spawnConf["z"]);

		UserHandler::getInstance()->init($this->database, $spawnPoint);
		GameHandler::getInstance()->init($this->database);
		WorldHandler::getInstance()->init($this->database);

		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new BehaviorPacksHandler(), $this);
		$this->getScheduler()->scheduleRepeatingTask(new MainTask($this), 2); // 0.25s
	}

	/** @var int  */
	private $count = 0;

	/**
	 * 0.1秒毎に呼び出す
	 */
	public function timer(){

		if($this->count % 300 === 0){
			$this->database->test();
		}

		UserHandler::getInstance()->timer();

		if($this->count % 10 === 0){
			GameHandler::getInstance()->second();
			WorldHandler::getInstance()->second();
		}

		$this->count++;
	}
}
