<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;


use kametan0730mc\RunForMoney2\item\CellPhone;
use kametan0730mc\RunForMoney2\item\DoorKey;
use kametan0730mc\RunForMoney2\item\Ekurea;
use kametan0730mc\RunForMoney2\item\ExMedicine2;
use kametan0730mc\RunForMoney2\item\ExMedicine5;
use kametan0730mc\RunForMoney2\item\IceGun;
use kametan0730mc\RunForMoney2\item\NetGun;
use kametan0730mc\RunForMoney2\item\Vaccine;
use kametan0730mc\RunForMoney2\lang\Lang;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use stdClass;
use function is_int;

class ItemShopForm extends UserForm{

	public static $items = [
		["item.apple.name", "item.apple.description", "textures/items/apple", ItemIds::APPLE, 1000, 64],
		["item.cooked_beef.name", "item.cooked_beef.description", "textures/items/beef_cooked", ItemIds::COOKED_BEEF, 5000, 64],
		["item.emerald.name", "item.emerald.description", "textures/items/emerald", ItemIds::EMERALD, 10000, 9],
		["item.rabbit_foot.name", "item.rabbit_foot.description", "textures/items/rabbit_foot", ItemIds::RABBIT_FOOT, 10000, 9],
		["item.ekurea.name", "item.ekurea.description", "textures/kametan/ekurea", Ekurea::ITEM_ID, 10000, 9],
		["item.magical_medicine.name", "item.magical_medicine.description", "textures/kametan/ex_medicine_2", ExMedicine2::ITEM_ID, 100000, 9],
		["item.vaccine.name", "item.vaccine.description", "textures/kametan/vaccine", Vaccine::ITEM_ID, 100000, 9],
		["item.net_gun.name", "item.net_gun.description", "textures/kametan/net_gun", NetGun::ITEM_ID, 100000, 9],
		["item.ice_gun.name", "item.ice_gun.description", "textures/kametan/ice_gun", IceGun::ITEM_ID, 100000, 9],
		["item.revival_medicine.name", "item.revival_medicine.description", "textures/kametan/ex_medicine_4", ExMedicine5::ITEM_ID, 100000, 9],
		["item.cell_phone.name", "item.cell_phone.description", "textures/kametan/cell_phone", CellPhone::ITEM_ID, 100000, 9],
		["item.door_key.name", "item.door_key.description", "textures/kametan/door_key", DoorKey::ITEM_ID, 100000, 9],
	];

	public function handleResponse(Player $player, $data): void{
		if(!is_int($data)){
			return;
		}
		if(!isset(self::$items[$data])){
			return;
		}
		$item = self::$items[$data];
		$player->sendForm(new ItemBuyForm($this->lang, $item[0], $item[3], $item[4], $item[5]));
	}

	public function jsonSerialize(){
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§9アイテムショップ";
		$data["content"] = "購入したい商品を選んでね";
		$buttons =[];

		foreach(self::$items as $itemInfo){
			$button = new \stdClass();
			$button->text = TextFormat::GOLD . Lang::translate($this->lang, $itemInfo[0]) . " (. " . $itemInfo[4] . "メタ)\n" . TextFormat::WHITE .Lang::translate($this->lang, $itemInfo[1]);
			$image = new \stdClass();
			$image->type = "path";
			$image->data = $itemInfo[2];
			$button->image = $image;
			$buttons[] = $button;
		}

		$data["buttons"] = $buttons;
		return $data;
	}
}