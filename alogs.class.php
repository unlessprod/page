<?php

if(!defined('allow')) {
	header("HTTP/1.0 404 Not Found");
}

if(!defined('test')) {
	die('Bratan slowaris love hentai');
}

class ALogs {

	public function LogsDataAll() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs`");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}


	public function LogsDataID($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `id` = :ID ORDER BY `date` DESC");
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

	public function LogsDataUserID($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `userID` = :ID ORDER BY `id`");
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

	public function LogsDataStopper($stopper, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `stopper` = :stopper");
		$DataBase->Bind(':stopper', $stopper);
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

	public function LogsDataRunning() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function AbuseCheck($target) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 AND `target` = :target");
		$DataBase->Bind(':target', $target);
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

	public function LogsDataRunningOnAPI($apiID, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 AND `apiID` = :apiID");
		$DataBase->Bind(':apiID', $apiID);
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

	public function MapLogs() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE  `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function LastUserAttack($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `time` + `date`  AND `stopped` = 0 AND `userID` = :userID ORDER BY `id` DESC LIMIT 10");
		$DataBase->Bind(':userID', $id);
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

	public function UserAttacks($uID) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 AND `userID`  = :uID");
		$DataBase->Bind(':uID', $uID);
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

		public function RunningAttacksL4() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 AND `type` = '4'");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function RunningAttacksL7() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 AND `type` = '7'");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return; 
	}

	public function CreateLog($userID, $target, $time, $port, $reqmethod, $postdata, $method, $rate, $hdata, $delay, $httpversion, $precheck, $emulation, $captcha, $stopper, $handler, $type) {
		global $DataBase;

		$DataBase->Query("INSERT INTO `attack_logs` (`id`, `userID`, `target`, `time`, `port`, `reqmethod`, `postdata`, `method`, `rate`, `hdata`, `delay`, `httpversion`, `precheck`, `emulation`, `captcha`, `stopper`, `date`, `stopped`, `handler`, `type`) VALUES (NULL, :userID, :target, :time, :port, :reqmethod, :postdata, :method, :rate, :hdata, :delay, :httpversion, :precheck, :emulation, :captcha,  :stopper, :date, '0', :handler, :type);");
		$DataBase->Bind(':userID', $userID);
		$DataBase->Bind(':target', $target);
		$DataBase->Bind(':port', $port);
		$DataBase->Bind(':time', $time);
		$DataBase->Bind(':reqmethod', $reqmethod);
		$DataBase->Bind(':postdata', $postdata);
		$DataBase->Bind(':method', $method);
		$DataBase->Bind(':rate', $rate);
		$DataBase->Bind(':hdata', $hdata);
		$DataBase->Bind(':delay', $delay);
		$DataBase->Bind(':httpversion', $httpversion);
		$DataBase->Bind(':precheck', $precheck);
		$DataBase->Bind(':emulation', $emulation);
		$DataBase->Bind(':captcha', $captcha);
		$DataBase->Bind(':stopper', $stopper);
		$DataBase->Bind(':date', time());
		$DataBase->Bind(':handler', $handler);
		$DataBase->Bind(':type', $type);

		return $DataBase->Execute();
	}

}

?>