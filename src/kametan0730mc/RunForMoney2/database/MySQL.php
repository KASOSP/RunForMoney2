<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\database;

use kametan0730mc\RunForMoney2\database\type\BannedUserType;
use kametan0730mc\RunForMoney2\database\type\RunForMoneyEntryType;
use kametan0730mc\RunForMoney2\database\type\UserCrownType;
use kametan0730mc\RunForMoney2\database\type\UserLogType;
use kametan0730mc\RunForMoney2\database\type\UserMoneyCacheType;
use kametan0730mc\RunForMoney2\database\type\UserMoneyTransactionType;
use kametan0730mc\RunForMoney2\database\type\UserRunForMoneyCacheType;
use kametan0730mc\RunForMoney2\database\type\UserRunForMoneyResultType;
use kametan0730mc\RunForMoney2\database\type\UserType;
use kametan0730mc\RunForMoney2\database\type\UserWebReceivedItemType;
use kametan0730mc\RunForMoney2\user\UserSaveData;

class MySQL implements Database{

	/** @var \mysqli */
	private $connection;

	/**
	 * @return bool
	 */
	public function test() : bool{
		return $this->connection->ping();
	}

	/**
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param string $dbname
	 * @return bool
	 */
	public function initDatabase(string $host, string $username, string $password, string $dbname) : bool{
		$this->connection = new \mysqli($host, $username, $password, $dbname);
		if($this->connection->connect_error){
			return false;
		}
		return true;
	}

	/**
	 * @param BannedUserType $record
	 */
	public function putBannedUser(BannedUserType $record){
		$stmt = $this->connection->prepare("INSERT INTO `banned_users` (`xuid`, `user_id`, `username`, `ip_address`, `expiration_date`, `enforcer_sign`, `reason`) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("isssiss", $record->userId, $record->xuid, $record->username, $record->ipAddress, $record->expirationDate, $record->enforcerSign, $record->reason);
		$stmt->execute();
	}

	/**
	 * @param string $xuid
	 * @param string $username
	 * @param string $ipAddress
	 * @return BannedUserType[]
	 */
	public function getBannedUsers(string $xuid, string $username, string $ipAddress) : array{
		$now = time();
		$stmt = $this->connection->prepare("SELECT `id`, `user_id`, `xuid`, `username`, `ip_address`, `expiration_date`, `enforcer_sign`, `reason` FROM `banned_users` WHERE (`xuid` = ? OR `username` = ? OR `ip_address` = ?) AND  `is_valid` = 1 AND `expiration_date` > ?");
		$stmt->bind_param("sssi", $xuid, $username, $ipAddress, $now);
		$stmt->execute();
		$stmt->bind_result($id, $userId, $xuid, $username, $ipAddress, $expirationDate, $enforcerSign, $reason);
		$result = [];
		while($stmt->fetch()){
			$result[] = new BannedUserType($id, $userId, $xuid, $username, $ipAddress, $expirationDate, $enforcerSign, $reason);
		}
		return $result;
	}

	public function putRunForMoneyEntry(RunForMoneyEntryType $entry) : int{
		$stmt = $this->connection->prepare("INSERT INTO `run_for_money_entries` (`information`) VALUES (?)");
		$stmt->bind_param("s", $entry->information);
		if($stmt->execute()){
			$result = $this->connection->query("SELECT LAST_INSERT_ID()");
			$row = $result->fetch_assoc();
			if(!isset($row["LAST_INSERT_ID()"])){
				return -1;
			}
			return (int) $row["LAST_INSERT_ID()"];
		}
		return -1;
	}

	/**
	 * @param $userId
	 * @return UserCrownType[]
	 */
	public function getUserCrowns($userId) : array{
		$stmt = $this->connection->prepare("SELECT `id`, `type`, `color`, `data`, `expires_at` FROM `user_crowns` WHERE `user_id` = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($id, $type, $color, $data, $expiresAt);
		$crowns = [];
		while($stmt->fetch()){
			$crowns[] = new UserCrownType($id, $userId, $type, $color, $data, $expiresAt);
		}
		return $crowns;
	}

	public function putUserLog(UserLogType $userLog){
		$stmt = $this->connection->prepare("INSERT INTO `user_logs` (`user_id`, `type`, `ip_address`) VALUES (?, ?, ?)");
		$stmt->bind_param("iis", $userLog->userId,$userLog->type, $userLog->ipAddress);
		$stmt->execute();
	}


	/**
	 * @param $userId
	 * @return UserMoneyCacheType|null
	 */
	public function getUserMoneyCache($userId) : ?UserMoneyCacheType{
		$stmt = $this->connection->prepare("SELECT `amount`, `max_transaction_id`, `updated_at` FROM `user_money_caches` WHERE `user_id` = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($amount, $maxTransactionId, $updatedAt);
		$stmt->fetch();
		if($maxTransactionId === null){
			return null;
		}
		return new UserMoneyCacheType($userId, $amount, $maxTransactionId, $updatedAt);
	}


	/**
	 * @param UserMoneyTransactionType $transaction
	 */
	public function putUserMoneyTransaction(UserMoneyTransactionType $transaction){
		$stmt = $this->connection->prepare("INSERT INTO `user_money_transactions` (`user_id`, `type`, `amount`, `data`) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("iiis", $transaction->userId, $transaction->type, $transaction->amount, $transaction->data);
		$stmt->execute();
	}

	/**
	 * @param $userId
	 * @return UserMoneyTransactionType[]
	 */
	public function getUserMoneyTransactions($userId) : array{
		$stmt = $this->connection->prepare("SELECT `id`, `type`, `amount`, `data` FROM `user_money_transactions` WHERE `user_id` = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($id, $type, $amount, $data);
		$result = [];
		while($stmt->fetch()){
			$result[] = new UserMoneyTransactionType($id, $userId, $type, $amount, $data);
		}
		return $result;
	}

	/**
	 * @param UserType $user
	 */
	public function putUser(UserType $user){
		$stmt = $this->connection->prepare("INSERT INTO `users` (`xuid`, `username`, `ip_address`, `save_data`) VALUES (?, ?, ?, ?)");
		$json = $user->saveData->jsonSerialize();
		$stmt->bind_param("ssss", $user->xuid, $user->username, $user->ipAddress, $json);
		$stmt->execute();
	}

	/**
	 * @param int $id
	 * @return string
	 */
	public function getUserNameByUserId(int $id) : ? string{
		$stmt = $this->connection->prepare("SELECT `username` FROM `users` WHERE `id` = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($username);
		$stmt->fetch();
		if($username === null){
			return "Unknown";
		}
		return $username;
	}

	/**
	 * @param string $xuid
	 * @return UserType|null
	 */
	public function getUserByXuid(string $xuid) : ? UserType{
		$stmt = $this->connection->prepare("SELECT `id`, `xuid`, `username`, `ip_address`, `save_data` FROM `users` WHERE `xuid` = ?");
		$stmt->bind_param("s", $xuid);
		$stmt->execute();
		$stmt->bind_result($id, $xuid, $username, $ipAddress, $saveData);
		$stmt->fetch();
		if($username === null){
			return null;
		}
		return new UserType($id, $xuid, $username, $ipAddress, UserSaveData::jsonDeserialize($saveData), null);
	}

	public function updateUserSaveData(int $userId, UserSaveData $saveData){
		$stmt = $this->connection->prepare("UPDATE `users` SET `save_data` = ?, `updated_at` = ? WHERE `id` = ?");
		$json = $saveData->jsonSerialize();
		$updatedAt = date('Y-m-d H:i:s');
		$stmt->bind_param("ssi", $json, $updatedAt, $userId);
		$stmt->execute();
	}

	public function updateUserPassword(int $userId, string $hashedPassword){
		$stmt = $this->connection->prepare("UPDATE `users` SET `password` = ?, `updated_at` = ? WHERE `id` = ?");
		$updatedAt = date('Y-m-d H:i:s');
		$stmt->bind_param("ssi", $hashedPassword, $updatedAt, $userId);
		$stmt->execute();
	}

	public function getUserRunForMoneyCache(int $userId){
		$stmt = $this->connection->prepare("SELECT `user_id`, `clear`, `death`, `catch`, `surrender`, `revival`, `max_result_id`, `updated_at` FROM `user_run_for_money_caches` WHERE `user_id` = ?");
		$stmt->bind_param("i", $userId);
		$stmt->bind_result($runForMoneyId, $clear, $death, $catch, $surrender, $revival, $maxResultId, $updatedAt);
		$stmt->fetch();
		if($runForMoneyId === null){
			return null;
		}
		return new UserRunForMoneyCacheType($runForMoneyId, $clear, $death, $catch, $surrender, $revival, $maxResultId, $updatedAt);
	}

	/**
	 * @param UserRunForMoneyResultType $result
	 */
	public function putUserRunForMoneyResult(UserRunForMoneyResultType $result){
		$stmt = $this->connection->prepare("INSERT INTO `user_run_for_money_results`(`user_id`, `run_for_money_id`, `clear`, `death`, `catch`, `surrender`, `revival`) VALUES (?,?,?,?,?,?,?)");
		$stmt->bind_param("iiiiiii", $result->userId, $result->runForMoneyId, $result->clear, $result->death, $result->catch, $result->surrender, $result->revival);
		$stmt->execute();
	}

	public function getUserRunForMoneyResults(int $userId): array{
		$stmt = $this->connection->prepare("SELECT `run_for_money_id`, `clear`, `death`, `catch`, `surrender`, `revival` FROM `user_run_for_money_results` WHERE `user_id` = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($runForMoneyId, $clear, $death, $catch, $surrender, $revival);
		$result = [];
		while($stmt->fetch()){
			$result[] = new UserRunForMoneyResultType(null, $runForMoneyId, $userId, $clear, $death, $catch, $surrender, $revival);
		}
		return $result;
	}

	public function getUserWebReceivedItems(int $userId): array{
		$stmt = $this->connection->prepare("SELECT `id`, `type`, `item_id`, `item_meta`, `amount` FROM `user_web_received_items` WHERE `user_id` = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$stmt->bind_result($id, $type, $itemId, $itemMeta, $amount);
		$result = [];
		while($stmt->fetch()){
			$result[] = new UserWebReceivedItemType($id, $userId, $type, $itemId, $itemMeta, $amount);
		}
		return $result;
	}

	public function deleteWebReceivedItem(int $id){
		$stmt = $this->connection->prepare("DELETE FROM `user_web_received_items` WHERE `id` = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
	}

	public function deleteWebReceivedItems(int $userId){
		$stmt = $this->connection->prepare("DELETE FROM `user_web_received_items` WHERE `user_id` = ?");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
	}

	////////-------------Ranking----------////////

	public function getUserMoneyCachesOrderByAmountDESC(){
		$result = $this->connection->query("SELECT `user_id`, `amount`, `max_transaction_id`, `updated_at` FROM `user_money_caches` ORDER BY `amount` DESC LIMIT 10");
		$caches = [];
		while($row = $result->fetch_assoc()){
			$caches[] = new UserMoneyCacheType((int) $row["user_id"], (int) $row["amount"], (int) $row["max_transaction_id"], $row["updated_at"]);
		}
		return $caches;
	}

	public function getUserRunForMoneyCachesOrderByClearDESC(){
		$result = $this->connection->query("SELECT `user_id`, `clear`, `death`, `catch`, `surrender`, `revival`, `max_result_id`, `updated_at` FROM `user_run_for_money_caches` ORDER BY `clear` DESC LIMIT 10");
		$caches = [];
		while($row = $result->fetch_assoc()){
			$caches[] = new UserRunForMoneyCacheType((int) $row["user_id"], (int) $row["clear"], (int) $row["death"], (int) $row["catch"], (int) $row["surrender"], (int) $row["revival"], (int) $row["max_result_id"], $row["updated_at"]);
		}
		return $caches;
	}

}