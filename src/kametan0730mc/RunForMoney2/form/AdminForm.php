<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;

use kametan0730mc\RunForMoney2\form\admin\AdminBanForm;
use kametan0730mc\RunForMoney2\form\admin\AdminTeleportForm;
use kametan0730mc\RunForMoney2\form\admin\AdminUserListForm;
use kametan0730mc\RunForMoney2\form\ItemShopForm;
use kametan0730mc\RunForMoney2\form\PasswordRegisterForm;
use kametan0730mc\RunForMoney2\form\SettingForm;
use kametan0730mc\RunForMoney2\form\TeleportForm;
use kametan0730mc\RunForMoney2\form\UserForm;
use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;

class AdminForm extends UserForm{

	public static $menus = [
		["form.admin.ban", "textures/ui/realms_red_x"],
		["form.admin.teleport", "textures/ui/portalBg"],
		["form.admin.user_list", "textures/ui/store_sort_icon"],
	];

	/**
	 * @inheritDoc
	 */
	public function handleResponse(Player $player, $data): void{
		if(!UserHandler::getInstance()->isAdmin($player)) return;
		if(!is_int($data)){
			return;
		}
		switch($data){
			case 0:
				$player->sendForm(new AdminBanForm($this->lang));
				break;
			case 1:
				$player->sendForm(new AdminTeleportForm($this->lang));
				break;
			case 2:
				$player->sendForm(new AdminUserListForm($this->lang));
				break;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§9" . Lang::translate($this->lang, "form.admin.title");
		$data["content"] = Lang::translate($this->lang, "form.admin.content");
		$buttons =[];

		foreach(self::$menus as $menu){
			$button = new \stdClass();
			$button->text = Lang::translate($this->lang, $menu[0]);
			$image = new \stdClass();
			$image->type = "path";
			$image->data = $menu[1];
			$button->image = $image;
			$buttons[] = $button;
		}

		$data["buttons"] = $buttons;
		return $data;
	}
}