<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\lang;


class Lang{

	/**
	 * @var string[][]
	 */
	private static $lang = [];

	public static function init() : void{
		$path = __DIR__ . DIRECTORY_SEPARATOR . "locale" .DIRECTORY_SEPARATOR;
		$files = scandir($path);
		foreach($files as $file){
			if(substr($file, strrpos($file, '.') + 1) === "ini"){
				self::loadLangFile($path . $file);
			}
		}
	}

	public static function loadLangFile(string $path) : void{
		$pairs = [];
		$lines = file($path);
		foreach ($lines as $line){
			$pair = explode('=', $line, 2);
			if(count($pair) !== 2) continue;
			$pairs[$pair[0]] = str_replace(array("\r\n", "\r", "\n"), '', $pair[1]);
		}
		if(!isset($pairs["language.code"])){
			throw new \InvalidStateException("Language code not found in " . $path);
		}
		self::loadLangData($pairs);
	}

	/**
	 * @param array $pairs
	 * @param bool $override
	 */
	public static function loadLangData(array $pairs, bool $override=false) : void{
		$langCode = $pairs["language.code"];
		if(isset(self::$lang[$langCode]) and !$override) return;

		self::$lang[$langCode] = [];

		foreach($pairs as $key => $value){
			self::$lang[$langCode][$key] = $value;
		}
	}

	public static function translate(string $langCode, string $text, array $inputs=[]) : string {
		$translated = @self::$lang[$langCode][$text];
		if($translated === null) {
			return $text . ((count($inputs) !== 0) ? (" (" . implode(",", $inputs) . ")") : "");
		}else{
			$count = 0;
			foreach($inputs as $input){
				$translated = str_replace("{%$count}", (string) $input, $translated);
				$count++;
			}
			return $translated;
		}
	}
}