<?php


namespace kametan0730mc\RunForMoney2\database\type;


class RunForMoneyEntryType{

	/** @var int|null */
	public $id;

	/** @var string */
	public $information;

	/**
	 * RunForMoneyEntryType constructor.
	 * @param int|null $id
	 * @param string $information
	 */
	public function __construct(?int $id, string $information){
		$this->id = $id;
		$this->information = $information;
	}
}