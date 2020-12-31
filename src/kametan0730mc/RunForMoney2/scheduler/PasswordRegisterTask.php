<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\scheduler;


use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class PasswordRegisterTask extends AsyncTask{

	/** @var int */
	private $userId;

	/** @var string */
	private $password;

	/**
	 * PasswordRegisterTask constructor.
	 * @param int $userId
	 * @param string $password
	 */
	public function __construct(int $userId, string $password){
		$this->userId = $userId;
		$this->password = $password;
	}

	public function onRun(): void{
		$hashedPassword = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 10]);
		$this->setResult($hashedPassword);
	}

	public function onCompletion(): void{
		$hashedPassword = $this->getResult();
		$userHandler = UserHandler::getInstance();
		$target = null;
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			if($userHandler->getUserIdByUser($player) === $this->userId){
				$target = $player;
				break;
			}
		}
		if($target === null){
			return;
		}
		UserHandler::getInstance()->updateUserPassword($this->userId, $hashedPassword);
		$userHandler->sendTranslatedMessage($target, "system.password.respawn", MESSAGE, [$this->password]);
		UserHandler::getInstance()->sendTranslatedMessage($player, "system.password.always", MESSAGE);

	}
}