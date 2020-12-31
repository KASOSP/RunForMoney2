<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;


use kametan0730mc\RunForMoney2\database\type\UserMoneyTransactionType;
use kametan0730mc\RunForMoney2\lang\Lang;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

class ItemBuyForm extends UserForm{

	/** @var string */
	private $itemName;

	/** @var int */
	private $itemId;

	/** @var int */
	private $itemCost;

	/** @var int */
	private $maxCount;


	public function __construct(string $lang, string $itemName, int $itemId, int $itemCost, int $maxCount){
		$this->itemName = $itemName;
		$this->itemId = $itemId;
		$this->itemCost = $itemCost;
		$this->maxCount = $maxCount;
		parent::__construct($lang);
	}

	public function handleResponse(Player $player, $data): void{
		if(!is_array($data) or count($data) !== 2){
			return;
		}
		$count = (int) $data[1];

		/** @var UserHandler $userHandler */
		$userHandler = UserHandler::getInstance();

		if($count <= 0 or $this->maxCount < $count){
			$userHandler->sendTranslatedMessage($player, "form.invalid.message", ERROR);
			return;
		}
		$totalCost = $count*$this->itemCost;

		$userId = $userHandler->getUserIdByUser($player);

		if($userHandler->getUserMoney($userId) < $totalCost){
			$userHandler->sendTranslatedMessage($player, "system.item.buy.no_money", ERROR);
			return;
		}

		$item = ItemFactory::getInstance()->get($this->itemId, 0, $count);

		if(!$player->getInventory()->canAddItem($item)){
			$userHandler->sendTranslatedMessage($player, "system.item.buy.no_space", ERROR);
			return;
		}

		$userHandler->reduceUserMoney($userId, $totalCost, UserMoneyTransactionType::TYPE_ITEM_BUY);
		$userHandler->sendTranslatedMessage($player, "system.item.buy.success", MESSAGE);

		$player->getInventory()->addItem($item);
	}

	public function jsonSerialize(){
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§9" . Lang::translate($this->lang, "form.itemBuy.title");
		$contents =[];

		$label = new \stdClass();
		$label->type = "label";
		$label->text = "アイテム名:" . Lang::translate($this->lang, $this->itemName) . "\n" . "単価:" . $this->itemCost . "メタ";
		$contents[] = $label;

		$slider = new \stdClass();
		$slider->text = Lang::translate($this->lang, "form.itemBuy.count");
		$slider->type = "slider";
		$slider->min = 1;
		$slider->max = $this->maxCount;
		$contents[] = $slider;
		$data["content"] = $contents;
		return $data;
	}
}