<?php

declare(strict_types=1);

namespace kametan0730mc\RunForMoney2\database;


use kametan0730mc\RunForMoney2\database\type\BannedUserType;
use kametan0730mc\RunForMoney2\database\type\RunForMoneyEntryType;
use kametan0730mc\RunForMoney2\database\type\UserLogType;
use kametan0730mc\RunForMoney2\database\type\UserMoneyCacheType;
use kametan0730mc\RunForMoney2\database\type\UserMoneyTransactionType;
use kametan0730mc\RunForMoney2\database\type\UserRunForMoneyResultType;
use kametan0730mc\RunForMoney2\database\type\UserType;
use kametan0730mc\RunForMoney2\user\UserSaveData;

interface Database{

	public function test() : bool;

	/**
	 * @param BannedUserType $record
	 */
	public function putBannedUser(BannedUserType $record);

	/**
	 * @param string $xuid
	 * @param string $username
	 * @param string $ipAddress
	 * @return BannedUserType[]
	 */
	public function getBannedUsers(string $xuid, string $username, string $ipAddress) : array;

	public function putRunForMoneyEntry(RunForMoneyEntryType $entry) : int;

	public function getUserCrowns($userId) : array;

	public function putUserLog(UserLogType $userLog);

	public function getUserMoneyCache($userId) : ?UserMoneyCacheType;
	/**
	 * @param UserMoneyTransactionType $transaction
	 */
	public function putUserMoneyTransaction(UserMoneyTransactionType $transaction);
	/**
	 * @param $userId
	 * @return UserMoneyTransactionType[]
	 */
	public function getUserMoneyTransactions($userId) : array;
	/**
	 * @param UserType $user
	 */
	public function putUser(UserType $user);
	/**
	 * @param string $xuid
	 * @return UserType|null
	 */
	public function getUserByXuid(string $xuid) : ? UserType;

	public function updateUserSaveData(int $userId, UserSaveData $saveData);

	public function updateUserPassword(int $userId, string $hashedPassword);

	public function getUserRunForMoneyCache(int $userId);
	/**
	 * @param UserRunForMoneyResultType $result
	 */
	public function putUserRunForMoneyResult(UserRunForMoneyResultType $result);

	public function getUserRunForMoneyResults(int $userId): array;
}