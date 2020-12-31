<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\sound;


use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\world\sound\Sound;

class PortalSound implements Sound{

	public function encode(?Vector3 $pos) : array{
		return [LevelEventPacket::create(LevelEventPacket::EVENT_SOUND_PORTAL, 0, $pos)];
	}
}