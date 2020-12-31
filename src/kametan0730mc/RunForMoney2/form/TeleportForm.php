<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;

use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\player\Player;
use function is_int;
use const MESSAGE;

class TeleportForm extends UserForm{

	public function handleResponse(Player $player, $data): void{
		if(!is_int($data)){
			return;
		}
		$selected = (int) $data;
		switch($selected){
			case 0:
				UserHandler::getInstance()->respawn($player);
				UserHandler::getInstance()->sendTranslatedMessage($player, "command.spawn.success", MESSAGE);
				break;

		}
	}

	public function jsonSerialize(){
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§9" . Lang::translate($this->lang, "form.teleport.title");
		$data["content"] = Lang::translate($this->lang, "form.teleport.content");
		$buttons =[];

		$button1 = new \stdClass();
		$button1->text = Lang::translate($this->lang, "form.teleport.lobby");
		$buttons[] = $button1;

		$button2 = new \stdClass();
		$button2->text = Lang::translate($this->lang, "form.teleport.lake");
		$buttons[] = $button2;

		$button3 = new \stdClass();
		$button3->text = Lang::translate($this->lang, "form.teleport.jail");
		$buttons[] = $button3;

		$data["buttons"] = $buttons;
		return $data;
	}
}