<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;


use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

class SettingForm extends UserForm{

	public function handleResponse(Player $player, $data): void{
		return;
	}

	public function jsonSerialize(){
		return[
		    "type" => "custom_form",
		    "title" => "設定",
		    "contents" => [
			[
			    'type' => 'label',
                            'text' => '作成中です。'
			]	
	            ]
		];
	}
}
