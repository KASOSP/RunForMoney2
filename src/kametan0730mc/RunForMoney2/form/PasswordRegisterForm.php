<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;


use kametan0730mc\RunForMoney2\scheduler\PasswordRegisterTask;
use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PasswordRegisterForm extends UserForm{

	public function handleResponse(Player $player, $data): void{
		if(!is_array($data) or count($data) !== 3 or $data[1] === ""){
			UserHandler::getInstance()->sendTranslatedMessage($player, "system.password.notRegister", MESSAGE);
			UserHandler::getInstance()->sendTranslatedMessage($player, "system.password.always", MESSAGE);

			return;
		}
		$password = $data[1];
		$passwordConfirm = $data[2];

		// TODO strlen

		if($password !== $passwordConfirm){
			UserHandler::getInstance()->sendTranslatedMessage($player, "system.password.notMatch", ERROR);
			UserHandler::getInstance()->sendTranslatedMessage($player, "system.password.notRegister", MESSAGE);
			UserHandler::getInstance()->sendTranslatedMessage($player, "system.password.always", MESSAGE);

			return;
		}
		$userId = UserHandler::getInstance()->getUserIdByUser($player);
		$task = new PasswordRegisterTask($userId, $password);

		Server::getInstance()->getAsyncPool()->submitTask($task);
		UserHandler::getInstance()->sendTranslatedMessage($player, "system.password.hashing", MESSAGE);
	}

	public function jsonSerialize(){
		$data = [];
		$data["type"] = "custom_form";
		$data["title"] = "§9パスワードを登録";
		$contents =[];

		$label = new \stdClass();
		$label->type = "label";
		$label->text = TextFormat::DARK_GREEN .
			"パスワードを登録する場合、以下のフォームを埋めて送信してください。" .
			TextFormat::RED . "登録しない場合はこのフォームは何もせずに閉じてください。" .
			TextFormat::DARK_GREEN ."パスワードを登録するとかめたんサーバーのWebサイトにログインできるようになります。" .
			"また、パスワードはいつでも登録/変更できます。";
		$contents[] = $label;

		$input1 = new \stdClass();
		$input1->type = "input";
		$input1->text = TextFormat::GOLD . "パスワード";
		$input1->placeholder = "パスワード";
		$contents[] = $input1;

		$input2 = new \stdClass();
		$input2->type = "input";
		$input2->text = TextFormat::GOLD . "パスワード(確認)";
		$input2->placeholder = "パスワード";
		$contents[] = $input2;
		$data["content"] = $contents;
		return $data;
	}
}