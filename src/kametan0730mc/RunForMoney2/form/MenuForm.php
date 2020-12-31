<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;

use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\Main;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use function is_int;
use function var_dump;

class MenuForm extends UserForm{

	public static $menus = [
		["form.menu.teleport", "textures/items/compass_item", false],
		["form.menu.shop", "textures/items/emerald", false],
		["form.menu.setting", "textures/kametan_2/setting", false],
		["form.menu.passwordRegister", "textures/kametan_2/lock", false],
		["form.menu.logout", "textures/kametan_2/logout", false],
		["form.menu.admin", "textures/ui/servers", true],
	];

	private $player;

	public function __construct(string $lang, Player $player){
		$this->player = $player;
		parent::__construct($lang);
	}

	/**
	 * @inheritDoc
	 */
	public function handleResponse(Player $player, $data): void{
		if(!is_int($data)){
			return;
		}
		switch($data){
			case 0:
				$player->sendForm(new TeleportForm($this->lang));
				break;
			case 1:
				$player->sendForm(new ItemShopForm($this->lang));
				break;
			case 2:
				$player->sendForm(new SettingForm($this->lang));
				break;
			case 3:
				$player->sendForm(new PasswordRegisterForm($this->lang));
				break;
			case 4:
				$player->kick(MESSAGE . "ログアウトしました");
				break;
			case 5:
				if(UserHandler::getInstance()->isAdmin($player)){
					$player->sendForm(new AdminForm($this->lang));
				}
				break;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§9" . Lang::translate($this->lang, "form.menu.title");
		$data["content"] = Lang::translate($this->lang, "form.menu.content");
		$buttons =[];

		foreach(self::$menus as $menu){
			if(!$menu[2] or UserHandler::getInstance()->isAdmin($this->player)){
				$button = new \stdClass();
				$button->text = Lang::translate($this->lang, $menu[0]);
				$image = new \stdClass();
				$image->type = "path";
				$image->data = $menu[1];
				$button->image = $image;
				$buttons[] = $button;
			}
		}

		$data["buttons"] = $buttons;
		return $data;
	}
}