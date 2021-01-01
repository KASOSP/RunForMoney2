<?php

namespace kametan0730mc\RunForMoney2\nametag;

use kametan0730mc\RunForMoney2\user\UserHandler;
use pocketmine\player\Player;

class Nametag{

	/**
	 * ネームタグが不正かどうか判定する。不正であればエラーを送り、falseを返す。
	 *
	 * @param Player $player
	 * @param string $nametag
	 * @return bool
	 */
	public static function testIsValidNametag(Player $player, string $nametag){
		$userHandler = UserHandler::getInstance();
		$len = mb_strlen($nametag);
		if($len-(substr_count($nametag, '§')*2) > 10 or $len > 16){
			$userHandler->sendTranslatedMessage($player, "system.nametag.tooLong", ERROR);
			return false;
		}

		for($i=0;$i<$len;$i++){
			$char = mb_substr($nametag, $i, 1);
			if($char === '§'){
				// §が最後の文字か最後から2番目文字だったり、次の文字がカラーコードとして無効な文字だったら
				if($i >= $len-2 or array_search(mb_substr($nametag, $i+1, 1), ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f']) === false){
					$userHandler->sendTranslatedMessage($player, "system.nametag.invalid", ERROR);
					return false;
				}
			}
		}
		return true;
	}
}