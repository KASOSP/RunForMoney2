<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\scheduler;

use kametan0730mc\RunForMoney2\Main;
use pocketmine\scheduler\Task;

class MainTask extends Task{

	/**
	 * @var Main
	 */
	private $main;

	/**
	 * MainTask constructor.
	 * @param Main $main
	 */
	public function __construct(Main $main){
		$this->main = $main;
	}

	public function onRun(): void{
		$this->main->timer();
	}
}