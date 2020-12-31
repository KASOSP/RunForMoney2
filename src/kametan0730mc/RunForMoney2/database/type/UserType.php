<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\database\type;

use kametan0730mc\RunForMoney2\user\UserSaveData;

class UserType{

	/** @var int|null */
	public $id;

	/** @var string */
	public $xuid;

	/** @var string */
	public $username;

	/** @var string */
	public $ipAddress;

	/** @var UserSaveData */
	public $saveData;

	/** @var string|null */
	public $password;

	/**
	 * UserType constructor.
	 * @param int|null $id
	 * @param string $xuid
	 * @param string $username
	 * @param string $ipAddress
	 * @param UserSaveData $saveData
	 * @param string|null $password
	 */
	public function __construct(?int $id, string $xuid, string $username, string $ipAddress, UserSaveData $saveData, ?string $password){
		$this->id = $id;
		$this->xuid = $xuid;
		$this->username = $username;
		$this->ipAddress = $ipAddress;
		$this->saveData = $saveData;
		$this->password = $password;
	}
}