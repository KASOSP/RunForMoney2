<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\database\type;

class BannedUserType{

	/** @var int|null */
	public $banId;

	/** @var int */
	public $userId;

	/** @var string */
	public $xuid;

	/** @var string */
	public $username;

	/** @var string */
	public $ipAddress;

	/** @var int */
	public $expirationDate;

	/** @var string */
	public $enforcerSign;

	/** @var string */
	public $reason;

	/**
	 * BanType constructor.
	 * @param int|null $banId
	 * @param int $userId
	 * @param string $xuid
	 * @param string $username
	 * @param string $ipAddress
	 * @param int $expirationDate
	 * @param string $enforcerSign
	 * @param string $reason
	 */
	public function __construct(?int $banId, int $userId, string $xuid, string $username, string $ipAddress, int $expirationDate, string $enforcerSign, string $reason){
		$this->banId = $banId;
		$this->userId = $userId;
		$this->xuid = $xuid;
		$this->username = $username;
		$this->ipAddress = $ipAddress;
		$this->expirationDate = $expirationDate;
		$this->enforcerSign = $enforcerSign;
		$this->reason = $reason;
	}
}