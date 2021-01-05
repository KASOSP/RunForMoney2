<?php

namespace kametan0730mc\RunForMoney2\user\skin;

use pocketmine\entity\Skin;

class SkinValidator{

	/**
	 * スキンと透明度の閾値(百分率)を与え、閾値より高かった場合falseを返します
	 *
	 * @param Skin $skin
	 * @param int $threshold
	 * @return bool
	 */
	public static function validate(Skin $skin, int $threshold){
		$data = $skin->getSkinData();
		$transparent = 0; // 透明
		$opacity = 0; // 不透明
		$i = 0;
		while($i < strlen($data)){
			$red = ord($data[$i++]);
			$green = ord($data[$i++]);
			$blue = ord($data[$i++]);
			$alpha = ord($data[$i++]);
			if($alpha === 0) $transparent++;
			else $opacity++;
		}

		if((int)(100*$transparent/($transparent+$opacity)) > $threshold){
			return false;
		}
		return true;
	}
}