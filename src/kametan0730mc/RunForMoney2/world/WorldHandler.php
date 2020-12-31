<?php


namespace kametan0730mc\RunForMoney2\world;


use kametan0730mc\RunForMoney2\database\Database;
use kametan0730mc\RunForMoney2\sound\PortalSound;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\particle\PortalParticle;

class WorldHandler{
	use SingletonTrait;

	/** @var Database */
	private $database;

	/** @var FloatingTextParticle */
	private $moneyRankingParticle;

	/** @var Vector3 */
	private $moneyRankingPos;

	/** @var FloatingTextParticle */
	private $clearRankingParticle;

	/** @var Vector3 */
	private $clearRankingPos;

	/** @var FloatingTextParticle */
	private $catchRankingParticle;

	/** @var Vector3 */
	private $catchRankingPos;

	public function init(Database $database){
		$this->database = $database;
		$this->moneyRankingParticle = new FloatingTextParticle("Money Ranking");
		$this->moneyRankingPos = new Vector3(122, 74, -244);
		$this->clearRankingParticle = new FloatingTextParticle("Clear Ranking");
		$this->clearRankingPos = new Vector3(119, 74, -251);
		$this->catchRankingParticle = new FloatingTextParticle("Catch Ranking");
		$this->catchRankingPos = new Vector3(116, 74, -257);
		$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
		$world->addParticle($this->moneyRankingPos, $this->moneyRankingParticle);
	}

	public function update(){
		$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
		$moneyCaches = $this->database->getUserMoneyCachesOrderByAmountDESC();
		$moneyRanking = "§a-------所持金ランキング-------\n";
		foreach($moneyCaches as $index => $cache){
			$username = $this->database->getUserNameByUserId($cache->userId);
			$moneyRanking .= "§e".($index+1)."位 : §b".$username."様 ".$cache->amount."メタ\n";
		}
		$this->moneyRankingParticle->setText($moneyRanking);
		$world->addParticle($this->moneyRankingPos, $this->moneyRankingParticle);

		$clearCaches = $this->database->getUserRunForMoneyCachesOrderByClearDESC();
		$clearRanking = "§a-------逃走成功回数ランキング-------\n";
		foreach($clearCaches as $index => $cache){
			$username = $this->database->getUserNameByUserId($cache->userId);
			$clearRanking .= "§e".($index+1)."位 : §b".$username."様 ".$cache->clear."回\n";
		}
		$this->clearRankingParticle->setText($clearRanking);
		$world->addParticle($this->clearRankingPos, $this->clearRankingParticle);

		$catchCaches = $this->database->getUserRunForMoneyCachesOrderByClearDESC();
		$catchRanking = "§a-------捕獲回数ランキング-------\n";
		foreach($catchCaches as $index => $cache){
			$username = $this->database->getUserNameByUserId($cache->userId);
			$catchRanking .= "§e".($index+1)."位 : §b".$username."様 ".$cache->catch."回\n";
		}
		$this->catchRankingParticle->setText($catchRanking);
		$world->addParticle($this->catchRankingPos, $this->catchRankingParticle);

		$particle = new PortalParticle();
		for($x=0;$x<10;$x++){
			for($z=0;$z<10;$z++){
				$world->addParticle($this->clearRankingPos->add(rand(-20,20)/10,rand(-20,20)/10,rand(-20,20)/10), $particle);
			}
		}
	}

	private $seconds = 0;
	public function second(){
		$this->seconds++;
		if($this->seconds % 10 === 0){
			$this->update();
		}
	}
}