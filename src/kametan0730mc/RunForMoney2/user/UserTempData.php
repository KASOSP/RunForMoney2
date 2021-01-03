<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\user;

use kametan0730mc\RunForMoney2\database\type\UserCrownType;
use pocketmine\player\Player;

/**
 * Class UserTempData
 * このクラスのプロパティはデータベースに保存されず、ログアウト毎に削除される一時データ
 */
class UserTempData{

	/** @var int */
	public $userId;

	/** @var string */
	public $loggedInAddress;

	/** @var bool  */
	public $isFirstLogin = false;

	/** @var bool */
	public $isAutoJoinGame = true;

	/** @var int  */
	public $chatRestrictionSeconds = 0;

	/** @var int  */
	public $interactRestrictionCount = 0;

	/** @var bool */
	public $cleanInventoryConfirm = false;

	/** @var int  */
	public $onlineTime = 0;

	/** @var UserCrownType[]  */
	public $crowns = [];

	/** @var int */
	public $userStatusDisplayEntityId;

}