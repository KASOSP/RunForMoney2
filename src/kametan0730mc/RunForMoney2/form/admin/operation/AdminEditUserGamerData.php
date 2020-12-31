<?php


namespace kametan0730mc\RunForMoney2\form\admin\operation;


use kametan0730mc\RunForMoney2\form\UserForm;
use kametan0730mc\RunForMoney2\game\GameHandler;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AdminEditUserGamerData extends UserForm{

	/** @var Player */
	private $target;

	/** @var int */
	private $gameId;

	public function __construct(string $lang, Player $target){
		$this->target = $target;
		$gameHandler = GameHandler::getInstance();
		$this->gameId = $gameHandler->getGameInfo()->gameId;
		parent::__construct($lang);
	}

	public function handleResponse(Player $player, $data): void{
		if(!UserHandler::getInstance()->isAdmin($player)) return;
		if(!is_array($data) or count($data) !== 2){
			// TODO messsage
			return;
		}
		$userHandler = UserHandler::getInstance();
		$gameHandler = GameHandler::getInstance();
		$gameInfo = $gameHandler->getGameInfo();
		if($gameInfo === null){
			return;
		}
		$gamerData = $gameInfo->getGamerData($this->target);
		$gamerType = (int) $data[0];
		$surrenderCount = (int) $data[1];
		$gamerData->gamerType = $gamerType;
		$gamerData->surrenderCountDown = $surrenderCount;
		$userHandler->sendTranslatedMessage($player, "system.admin.user.operation.success", MESSAGE);
	}

	public function jsonSerialize(){
		$gameHandler = GameHandler::getInstance();
		//if($this->player->isClosed()) return [];
		if(!$gameHandler->isGameRunning()) return [];
		if(!$gameHandler->getGameInfo()->hasGamerData($this->target)) return [];
		$gamerData = $gameHandler->getGameInfo()->getGamerData($this->target);
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§9".$this->target->getName()."さんのゲームデータの編集";
		$contents =[];

		$input1 = new \stdClass();
		$input1->type = "input";
		$input1->text = TextFormat::GOLD . "ゲーマータイプ";
		$input1->default = (string) $gamerData->gamerType;
		$contents[] = $input1;

		$input2 = new \stdClass();
		$input2->type = "input";
		$input2->text = TextFormat::GOLD . "自首カウントダウン";
		$input2->default = (string) $gamerData->surrenderCountDown;
		$contents[] = $input2;
		$data["content"] = $contents;
		return $data;
	}
}