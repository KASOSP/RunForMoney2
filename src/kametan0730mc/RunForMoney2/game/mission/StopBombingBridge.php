<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\mission;


use kametan0730mc\RunForMoney2\game\field\Arashiyama;

class StopBombingBridge extends Mission{

	const FIELD_ID = 1;

	public static function getMissionId(): int {
		return self::FIELD_ID;
	}

	public static function getExecutableFieldsId() : array{
		return [Arashiyama::FIELD_ID];
	}

	public function start() : void{

	}

	public function clear() : void{

	}

	public function timeout() : void{

	}
}