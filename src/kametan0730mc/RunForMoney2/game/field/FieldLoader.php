<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\game\field;

class FieldLoader{

	/** @var Field[] */
	public static $fields = [];

	public static function init(){
		//self::registerField(SampleField::getFieldId(), new SampleField());
		self::registerField(Arashiyama::getFieldId(), new Arashiyama());
		self::registerField(Edo::getFieldId(), new Edo);
	}

	/**
	 * @param int $fieldId
	 * @param Field $field
	 * @param bool $override
	 */
	public static function registerField(int $fieldId, Field $field, bool $override=false){
		if(isset(self::$fields[$fieldId]) and !$override) return;
		self::$fields[$fieldId] = $field;
	}

	/**
	 * @param int $fieldId
	 * @return bool
	 */
	public static function isRegistered(int $fieldId) : bool{
		return isset(self::$fields[$fieldId]);
	}

	/**
	 * @return Field
	 */
	public static function getRandomField() {
		return self::$fields[array_rand(self::$fields)];
	}

	/**
	 * @param int $fieldId
	 * @return Field
	 */
	public static function get(int $fieldId) : Field {
		return self::$fields[$fieldId];
	}
}