<?php


namespace kametan0730mc\RunForMoney2\database\type;


class UserCrownType{

	/** @var int|null */
	public $id;

	/** @var int */
	public $userId;

	/** @var int */
	public $type;

	/** @var string */
	public $color;

	/** @var string */
	public $data;

	/** @var string */
	public $expiresAt;

	/**
	 * UserCrownType constructor.
	 * @param int|null $id
	 * @param int $userId
	 * @param int $type
	 * @param string $color
	 * @param string $data
	 * @param string $expiresAt
	 */
	public function __construct(?int $id, int $userId, int $type, string $color, string $data, string $expiresAt){
		$this->id = $id;
		$this->userId = $userId;
		$this->type = $type;
		$this->color = $color;
		$this->data = $data;
		$this->expiresAt = $expiresAt;
	}

}