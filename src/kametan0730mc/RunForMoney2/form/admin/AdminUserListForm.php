<?php


namespace kametan0730mc\RunForMoney2\form\admin;


use kametan0730mc\RunForMoney2\form\UserForm;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\Server;

class AdminUserListForm extends UserForm{

	/** @var Player[] */
	private $userList = [];

	public function __construct(string $lang){
		$this->userList = array_values(Server::getInstance()->getOnlinePlayers());
		parent::__construct($lang);
	}

	public function handleResponse(Player $player, $data): void{
		if(!UserHandler::getInstance()->isAdmin($player)) return;
		if(!is_int($data)){
			return;
		}
		if(!isset($this->userList[$data])){
			return;
		}
		$user = $this->userList[$data];
		$player->sendForm(new AdminUserOperationForm($this->lang, $user));
	}

	public function jsonSerialize(){
		$data = [];
		$data["type"] = "form";
		$data["title"] = "§9オンラインユーザーリスト";
		$data["content"] = "ユーザーを選択してください";
		$buttons =[];
		$userHandler = UserHandler::getInstance();
		foreach($this->userList as $player){
			$button = new \stdClass();
			$button->text = $player->getDisplayName();
			$buttons[] = $button;
		}

		$data["buttons"] = $buttons;
		return $data;
	}
}