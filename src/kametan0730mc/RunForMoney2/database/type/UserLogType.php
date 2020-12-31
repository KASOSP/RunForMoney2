<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\database\type;

class UserLogType{

	public const TYPE_SERVER_LOGIN = 0;
	public const TYPE_SERVER_LOGOUT = 1;

	public const TYPE_HOMEPAGE_LOGIN = 4;

	/** @var int|null */
	public $id;

	/** @var int */
	public $userId;

	/** @var int */
	public $type;

	/** @var string */
	public $ipAddress;

	/**
	 * UserLogType constructor.
	 * @param int|null $id
	 * @param int $userId
	 * @param int $action
	 * @param string $ipAddress
	 */
	public function __construct(?int $id, int $userId, int $type, string $ipAddress){
		$this->id = $id;
		$this->userId = $userId;
		$this->type = $type;
		$this->ipAddress = $ipAddress;
	}
}