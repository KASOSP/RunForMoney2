<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\mission;

abstract class Mission{

	abstract public static function getMissionId(): int;

	abstract public static function getExecutableFieldsId() : array;

	abstract public function start() : void;

	abstract public function clear() : void;

	abstract public function timeout() : void;

}