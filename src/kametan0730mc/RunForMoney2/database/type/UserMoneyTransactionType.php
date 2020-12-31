<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\database\type;


class UserMoneyTransactionType{

	// 0~9はシステム的な
	public const TYPE_ACCOUNT_INIT = 0;

	// 10~29はアイテム関係
	public const TYPE_ITEM_BUY = 10; // アイテムの購入
	public const TYPE_ITEM_USE = 11; // アイテムの使用による消費
	public const TYPE_ITEM_SOBA = 12; // 年越しそばの使用

	// 30~49はゲーム関係
	public const TYPE_GAME_EVENT = 30; // ゲーム内イベント
	public const TYPE_GAME_CLEAR = 31; // ゲームクリア
	public const TYPE_GAME_CATCH = 32; // 捕獲
	public const TYPE_GAME_SURRENDER = 33; // 自首

	// 50~99は適当
	public const TYPE_REFUND = 50;
	public const TYPE_ADMIN_OPERATION = 99;

	/** @var int|null */
	public $id;

	/** @var int */
	public $userId;

	/** @var int */
	public $type;

	/** @var int */
	public $amount;

	/** @var string|null */
	public $data = "";

	/**
	 * UserMoneyTransactionType constructor.
	 * @param int|null $id
	 * @param int $userId
	 * @param int $type
	 * @param int $amount
	 * @param string $data
	 */
	public function __construct(?int $id, int $userId, int $type, int $amount, string $data=""){
		$this->id = $id;
		$this->userId = $userId;
		$this->type = $type;
		$this->amount = $amount;
		$this->data = $data;
	}
}