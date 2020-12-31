<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\user;

/**
 * Class UserSaveData
 * このクラスのプロパティはデータベースに保存されるデータ
 */
class UserSaveData implements \JsonSerializable{

	/** @var bool  */
	private $isSaveDataModified = false;

	/** @var string */
	private $lang = "jpn";

	/** @var string */
	private $nametag = "";

	/**
	 * @return string
	 */
	public function getLang(): string{
		return $this->lang;
	}

	/**
	 * @param string $lang
	 */
	public function setLang(string $lang): void{
		if($this->lang !== $lang){
			$this->lang = $lang;
			$this->isSaveDataModified = true;
		}
	}

	/**
	 * @return string
	 */
	public function getNametag(): string{
		return $this->nametag;
	}

	/**
	 * @param string $nametag
	 */
	public function setNametag(string $nametag): void{
		if($this->nametag !== $nametag){
			$this->nametag = $nametag;
			$this->isSaveDataModified = true;
		}
	}

	/**
	 * @return bool
	 */
	public function isSaveDataModified() : bool{
		return $this->isSaveDataModified;
	}

	public function jsonSerialize(){
		$data = [];
		$data["lang"] = $this->lang;
		$data["nametag"] = $this->nametag;
		return json_encode($data);
	}

	public static function jsonDeserialize(string $json) : self{
		$json = json_decode($json, true);
		$saveData = new UserSaveData();
		if(isset($json["lang"])){
			$saveData->lang = $json["lang"];
		}
		if(isset($json["nametag"])){
			$saveData->nametag = $json["nametag"];
		}
		return $saveData;
	}
}