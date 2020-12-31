<?php


namespace kametan0730mc\RunForMoney2\database\type;


class UserMoneyCacheType{

	/** @var int */
	public $userId;

	/** @var int */
	public $amount;

	/** @var int */
	public $maxTransactionId;

	/** @var string */
	public $updatedAt;

	/**
	 * UserMoneyCacheType constructor.
	 * @param int $userId
	 * @param int $amount
	 * @param int $maxTransactionId
	 * @param string $updatedAt
	 */
	public function __construct(int $userId, int $amount, int $maxTransactionId, string $updatedAt){
		$this->userId = $userId;
		$this->amount = $amount;
		$this->maxTransactionId = $maxTransactionId;
		$this->updatedAt = $updatedAt;
	}
}