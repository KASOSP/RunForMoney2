<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\form;


use pocketmine\form\Form;

abstract class UserForm implements Form{

	/** @var string */
	protected $lang;

	public function __construct(string $lang){
		$this->lang = $lang;
	}
}