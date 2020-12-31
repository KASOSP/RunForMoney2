<?php


namespace kametan0730mc\RunForMoney2\database\type;


class UserWebReceivedItemType{

	/** @var int|null */
	public $id;

	/** @var int */
	public $userId;

	/** @var int */
	public $type;

	/** @var int */
	public $itemId;

	/** @var int */
	public $itemMeta;

	/** @var int */
	public $amount;

	/**
	 * UserWebReceivedItemType constructor.
	 * @param int|null $id
	 * @param int $userId
	 * @param int $type
	 * @param int $itemId
	 * @param int $itemMeta
	 * @param int $amount
	 */
	public function __construct(?int $id, int $userId, int $type, int $itemId, int $itemMeta, int $amount){
		$this->id = $id;
		$this->userId = $userId;
		$this->type = $type;
		$this->itemId = $itemId;
		$this->itemMeta = $itemMeta;
		$this->amount = $amount;
	}

}