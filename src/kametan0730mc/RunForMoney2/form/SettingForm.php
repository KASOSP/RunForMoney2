<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;


use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\nametag\Nametag;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SettingForm extends UserForm{

	/** @var Player */
	private $player;

	public function __construct(string $lang, Player $player){
		$this->player = $player;
		parent::__construct($lang);
	}

	public function handleResponse(Player $player, $data): void{
		if(!is_array($data) or count($data) !== 2){
			return;
		}
		$userHandler = UserHandler::getInstance();
		$saveData = $userHandler->getSaveDataByUser($this->player);
		$langIndex = (int) $data[0];
		if(isset(Lang::getLangList()[$langIndex])){
			$lang = Lang::getLangList()[$langIndex];
			$saveData->setLang($lang);
		}else{
			$userHandler->sendTranslatedMessage($player, 'system.language.notFound', ERROR);
		}

		$nametag = $data[1];
		if(Nametag::testIsValidNametag($player, $nametag)){
			$saveData->setNametag($nametag);
		}

		$userHandler->sendTranslatedMessage($player, "system.setting.complete", MESSAGE);
	}

	public function jsonSerialize(){
		$userHandler = UserHandler::getInstance();
		$saveData = $userHandler->getSaveDataByUser($this->player);
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§9設定";
		$contents =[];

		$input1 = new \stdClass();
		$input1->type = "dropdown";
		$input1->text = TextFormat::GOLD . "言語";
		$langList = Lang::getLangList();
		$default = array_search($saveData->getLang(), $langList, true);
		$input1->default = ($default === false) ? 0 : $default;
		$input1->options = $langList;
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
