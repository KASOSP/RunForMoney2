<?php


namespace kametan0730mc\RunForMoney2\database\type;


class UserRunForMoneyCacheType{

	/** @var int */
	public $userId;

	/** @var int */
	public $clear;

	/** @var int */
	public $death;

	/** @var int */
	public $catch;

	/** @var int */
	public $surrender;

	/** @var int */
	public $revival;

	/** @var int */
	public $maxResultId;

	/** @var string */
	public $updatedAt;

	/**
	 * UserRunForMoneyCacheType constructor.
	 * @param int $userId
	 * @param int $clear
	 * @param int $death
	 * @param int $catch
	 * @param int $surrender
	 * @param int $revival
	 * @param int $maxResultId
	 * @param string $updatedAt
	 */
	public function __construct(int $userId, int $clear, int $death, int $catch, int $surrender, int $revival, int $maxResultId, string $updatedAt){
		$this->userId = $userId;
		$this->clear = $clear;
		$this->death = $death;
		$this->catch = $catch;
		$this->surrender = $surrender;
		$this->revival = $revival;
		$this->maxResultId = $maxResultId;
		$this->updatedAt = $updatedAt;
	}
}