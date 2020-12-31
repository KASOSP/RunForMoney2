<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form\admin;

use kametan0730mc\RunForMoney2\form\admin\operation\AdminEditUserGamerData;
use kametan0730mc\RunForMoney2\form\admin\operation\AdminEditUserSaveDataForm;
use kametan0730mc\RunForMoney2\form\UserForm;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class AdminUserOperationForm extends UserForm{

	/** @var Player */
	private $target;

	public function __construct(string $lang, Player $target){
		$this->target = $target;
		parent::__construct($lang);
	}


	public function handleResponse(Player $player, $data): void{
		if(!UserHandler::getInstance()->isAdmin($player)) return;
		if(!is_int($data)){
			return;
		}
		switch($data){
			case 0:
				$player->sendForm(new AdminEditUserSaveDataForm($this->lang, $this->target));
				break;
			case 1:
				//$player->sendForm(new AdminEditUserGamerData($this->lang, $this->target));
				break;
			case 2:
				$player->sendForm(new AdminEditUserGamerData($this->lang, $this->target));
				break;
		}
	}

	public function jsonSerialize(){
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§9ユーザー操作";
		$data["content"] = $this->target->getName() . "さんの操作";
		$buttons =[];

		$button1 = new \stdClass();
		$button1->text = "セーブデータの編集";
		$buttons[] = $button1;

		$button2 = new \stdClass();
		$button2->text = "一時データの編集";
		$buttons[] = $button2;


		$button3 = new \stdClass();
		$button3->text = "ゲームデータの編集";
		$buttons[] = $button3;

		$button2 = new \stdClass();
		$button2->text = "Ban";
		$buttons[] = $button2;

		$data["buttons"] = $buttons;
		return $data;
	}
}