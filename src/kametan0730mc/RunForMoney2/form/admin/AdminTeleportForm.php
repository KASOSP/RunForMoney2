<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form\admin;


use kametan0730mc\RunForMoney2\form\UserForm;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

class AdminTeleportForm extends UserForm{

	public function handleResponse(Player $player, $data): void{
		// TODO: Implement handleResponse() method.
	}

	public function jsonSerialize(){
		// TODO: Implement jsonSerialize() method.
	}
}