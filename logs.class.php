<?php

if(!defined('allow')) {
	header("HTTP/1.0 404 Not Found");
}

if(!defined('test')) {
	die('Bratan slowaris love hentai.');
}

class Logs {

	public function LogsDataAll() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `action_logs`");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function LogsActionAll() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `action_logs`");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function LoginsToday() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `action_logs` WHERE `timestamp` > :time AND `action` = 'Logged in'");
		$DataBase->Bind(":time", time() - 86400);
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function LogsOnlineUser() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `users` WHERE `Activity` + 30 > UNIX_TIMESTAMP()");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function LogsDataID($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `action_logs` WHERE `userID` = :ID");
		$DataBase->Bind(':ID', $id);
		$DataBase->Execute();

		if($num == 0) {
			$Return = array(
				'Count' => $DataBase->RowCount(),
				'Response' => $DataBase->ResultSet()
			);

			return $Return;
		} else {
			return $DataBase->Single();
		}
	}

	public function CreateLog($uID, $action) {
		global $DataBase;
		global $User;


		$DataBase->Query("INSERT INTO `action_logs` (`id`, `uID`, `action`, `timestamp`) VALUES (NULL, :uID, :action, :timestamp);");
		$DataBase->Bind(':uID', $uID);
		$DataBase->Bind(':action', $action);
		$DataBase->Bind(':timestamp', time());

		return $DataBase->Execute();
	
	}

}

?>