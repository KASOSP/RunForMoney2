<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\command\development;


use kametan0730mc\RunForMoney2\crown\Crown;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CrownTestCommand extends Command{

	public function __construct(string $name){
		parent::__construct(
			$name,
			"Display list of crown",
			"/crowntest"
		);
		$this->setPermission("kametan.command.crowntest");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if(!$this->testPermission($sender)){
			return true;
		}
		$crowns = Crown::getAll();
		$message = "";
		foreach($crowns as $key => $crown){
			$message .=  TextFormat::RESET . $key . ":" . $crown . " ";
			if(($key+1) % 6 === 0){
				$sender->sendMessage($message);
				$message = "";
			}
		}
		if($message!== ""){
			$sender->sendMessage($message);
		}
		return true;
	}
}