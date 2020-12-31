<?php


namespace kametan0730mc\RunForMoney2\form\admin\operation;


use kametan0730mc\RunForMoney2\form\UserForm;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AdminEditUserSaveDataForm extends UserForm{

	/** @var Player */
	private $target;

	public function __construct(string $lang, Player $target){
		$this->target = $target;
		parent::__construct($lang);
	}

	public function handleResponse(Player $player, $data): void{
		if(!UserHandler::getInstance()->isAdmin($player)) return;
		if(!is_array($data) or count($data) !== 2){
			// TODO messsage
			return;
		}
		$userHandler = UserHandler::getInstance();
		$saveData = $userHandler->getSaveDataByUser($this->target);
		$lang = $data[0];
		$nametag = $data[1];
		$saveData->setLang($lang);
		$saveData->setNametag($nametag);
		$userHandler->sendTranslatedMessage($player, "system.admin.user.operation.success", MESSAGE);
	}

	public function jsonSerialize(){
		$userHandler = UserHandler::getInstance();
		//if($this->player->isClosed()) return [];
		$saveData = $userHandler->getSaveDataByUser($this->target);
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§9".$this->target->getName()."さんのセーブデータの編集";
		$contents =[];

		$input1 = new \stdClass();
		$input1->type = "input";
		$input1->text = TextFormat::GOLD . "言語";
		$input1->default = $saveData->getLang();
		$contents[] = $input1;

		$input2 = new \stdClass();
		$input2->type = "input";
		$input2->text = TextFormat::GOLD . "ネームタグ";
		$input2->default = $saveData->getNametag();
		$contents[] = $input2;
		$data["content"] = $contents;
		return $data;
	}
}