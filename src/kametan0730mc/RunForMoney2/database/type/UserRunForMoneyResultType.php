<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\database\type;


class UserRunForMoneyResultType{

	/** @var int|null */
	public $id;

	/** @var int */
	public $userId;

	/** @var int */
	public $runForMoneyId;

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

	/**
	 * UserGameResultType constructor.
	 * @param int|null $id
	 * @param int $userId
	 * @param int $runForMoneyId
	 * @param int $clear
	 * @param int $death
	 * @param int $catch
	 * @param int $surrender
	 * @param int $revival
	 */
	public function __construct(?int $id, int $userId, int $runForMoneyId, int $clear, int $death, int $catch, int $surrender, int $revival){
		$this->id = $id;
		$this->userId = $userId;
		$this->runForMoneyId = $runForMoneyId;
		$this->clear = $clear;
		$this->death = $death;
		$this->catch = $catch;
		$this->surrender = $surrender;
		$this->revival = $revival;
	}
}