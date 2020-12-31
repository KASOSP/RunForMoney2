<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game;

/**
 * Class GamerData
 * ゲーム毎に削除される一時データ
 */
class GamerData{

	const GAMER_TYPE_RUNNER = 0;
	const GAMER_TYPE_HUNTER = 1;
	const GAMER_TYPE_BETRAYAL = 2;
	const GAMER_TYPE_CAUGHT = 3;
	const GAMER_TYPE_WAITING = 4;

	/** @var int */
	public $userId;

	/** @var int */
	public $gamerType = self::GAMER_TYPE_WAITING;

	/** @var int  */
	public $surrenderCountDown = -1;

	/** @var int */
	public $money = 0;

	/** @var int */
	public $clear = 0;

	/** @var int */
	public $death = 0;

	/** @var int */
	public $catch = 0;

	/** @var int */
	public $surrender = 0;

	/** @var int */
	public $revival = 0;


}