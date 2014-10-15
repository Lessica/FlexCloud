<?php

/*
	Set Header
	Content-Type: text/html; charset=UTF-8 (C)
	X-Powered-By: PHP/5.4.23 (C)
*/
header("Content-Type: application/json; charset=UTF-8");
header("X-Powered-By: PHP/5.4.23");
date_default_timezone_set('PRC');

/* Main Service */
include("connect.php");
// Connect to Server
$con = mysql_connect($server,$username,$password);

if (!$con) {
	$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"无法连接数据库，请联系管理员。"));
	goto endlabel;
  }

mysql_query("SET NAMES utf8",$con);
mysql_select_db($database,$con);

$GetTips = mysql_fetch_row(mysql_query("SELECT `Contents` FROM `info` WHERE `Name` = 'G_Tips'",$con));
$E_Tips = $GetTips[0];
if ($E_Tips == "") {
	$E_Tips = $G_Tips;
}

// Get Request Content And Action
$raw = $_POST['request'];
$obj = json_decode($raw);

if ($obj == null) {
	$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的查询请求！"));
	goto endlabel;
}

staterecord:
// Record Service State
if ($G_Counts != 0) {
	mysql_query("UPDATE `info` SET `Counts` = `Counts` + 1 WHERE `Name` = 'PV'",$con);
}

$action = stripslashes((string)$obj->{"action"});
$replace = false;

// Reactions

// APP LIST
if ($action == "appInfo") {
	$appID = (int)$obj->{"appID"};
	$UDID = stripslashes((string)$obj->{"udid"});
	if ($appID <= 0 OR strlen($UDID) != 40) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：未知设备或应用编号。"));
		goto endlabel;
	}
		$sql = "SELECT `ID`, `Name`, `Identifier` FROM `applist` WHERE `ID` = '" . $appID . "'";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取应用列表失败：applist，请联系管理员。"));
		goto endlabel;
	}
	else {
		$row = mysql_fetch_assoc($query);
		if (!$row) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取应用列表失败：applist，请联系管理员。"));
		goto endlabel;
		}
		else {
			$result = array("status"=>"success","result"=>array("app"=>array("id"=>(int)$row['ID'],"name"=>(string)$row['Name'],"identifier"=>(string)$row['Identifier'])));
		}
	}
}
elseif ($action == "applist") {
	$UDID = stripslashes((string)$obj->{"udid"});
	if (strlen($UDID) != 40) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：未知设备。"));
		goto endlabel;
	}
	
	$sql = "SELECT `ID`, `Name`, `Identifier` FROM `applist` WHERE `ToShow` = '1'";
	$query = mysql_query($sql,$con);
	$i = 0;
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取应用列表失败：applist，请联系管理员。"));
		goto endlabel;
	}
	while ($row = mysql_fetch_row($query)) {
		$arr[$i] = array("id"=>(int)$row[0],"name"=>$row[1],"identifier"=>$row[2]);
		$i++;
	}
	
	stateshow:
	if ($G_Counts == 2) {
		// Set State Show
		$q_info = mysql_query("SELECT `Name`, `Counts` FROM `info` WHERE `Name` = 'PV'",$con);
		$info = mysql_fetch_row($q_info);
		$E_Tips = str_replace('%counts%', (string)$info[1], $E_Tips);
	}
	
	if (count($arr) > 0) {
		$arr = array_values($arr);
		$result = array("status"=>"success","result"=>$arr);
	}
	else {
		$result = array("status"=>"success","result"=>array());
	}
}

// APP PATCH LIST

elseif ($action == "appPatchList") {
	$appID = (int)$obj->{"appID"};
	$UDID = stripslashes((string)$obj->{"udid"});
	$iOS_Long = (int)$obj->{"iosLong"};
	$Filter = $obj->{"shouldFilterByiOSVersion"};
	if ($appID <= 0 or strlen($UDID) != 40) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：未知设备或应用编号。"));
		goto endlabel;
	}
	$sql = "SELECT `ID`, `Identifier` FROM `applist` WHERE `ID` = '" . $appID . "'";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取应用列表失败：applist，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	if ($Filter == false) {
		$sql = "SELECT `ID`, `Name`, `Description`, `Author`, `DownloadTimes`, `iOS`, `Identifier`, `ToShow`, `UUID`, `AuthorID`, `AverageRating`,`appID` FROM `patches` WHERE `Identifier` = '" . $row[1] . "' and (`ToShow` = '1' or `ToShow` = '9')";
	}
	else {
		$sql = "SELECT `ID`, `Name`, `Description`, `Author`, `DownloadTimes`, `iOS`, `Identifier`, `ToShow`, `UUID`, `AuthorID`, `AverageRating`,`appID` FROM `patches` WHERE (`Identifier` = '" . $row[1] . "' and (`ToShow` = '1' or `ToShow` = '9') and `iosLong` >= 70000)";
	}
	goto MakePatchList;
}

// PATCH INFO

elseif ($action == "patchInfo") {
	$patchID = (int)$obj->{"patchID"};
	$UDID = stripslashes((string)$obj->{"udid"});
	if ($patchID <= 0 or strlen($UDID) != 40) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：未知设备或补丁编号。"));
		goto endlabel;
	}
	
	// Get User Rate
	$sql = "SELECT `ID`, `UserID`, `PatchID`, `Stars` FROM `rates` WHERE (`UserID` = '" . mysql_real_escape_string($UDID) . "' and `PatchID` = '" . $patchID . "') LIMIT 1";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取评分列表失败：rates，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	if ($row != false) {
		$UserRate = (double)$row[3] / 10;
	}
	else {
		$UserRate = 0;
	}
	
	// Get Patch Info
	$sql = "SELECT `ID`, `Name`, `Identifier`, `UUID`, `DownloadTimes`, `Description`, `Author`, `AuthorID`, `AverageRating` FROM `patches` WHERE `ID` = '" . $patchID . "'";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取补丁列表失败：patches，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	if ($row != false) {
		$arr = array("label"=>$row[1],"identifier"=>$row[2],"uuid"=>$row[3],"downloads"=>(int)$row[4],"description"=>$row[5],"author"=>$row[6],"authorID"=>(int)$row[7],"userRating"=>(double)$UserRate,"averageRating"=>(double)$row[8],"enabled"=>false);
		$patch = array("patch"=>$arr);
		$result = array("status"=>"success","result"=>$patch);
	}
	else {
		$result = array("status"=>"error","alert"=>array("title"=>"读取失败","message"=>"找不到指定记录。"));
	}
}

// Download Patch

elseif ($action == "downloadPatch") {
	$patchID = (int)$obj->{"patchID"};
	$UDID = stripslashes((string)$obj->{"udid"});
	if ($patchID <= 0 or strlen($UDID) != 40) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：未知设备或补丁编号。"));
		goto endlabel;
	}
	
	// Check Purchasing Info via UDID
	if ($F_Purchase == true) {
		$sql = "SELECT * FROM `purchase` WHERE UDID = '" . mysql_real_escape_string($UDID) . "' LIMIT 1";
		$query = mysql_query($sql,$con);
		if ($query == false) {
			$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取授权列表失败：purchase，请联系管理员。"));
			goto endlabel;
		}
		$row = mysql_fetch_row($query);
		if ($row == false) {
			$result = array("status"=>"error","alert"=>array("title"=>"授权验证失败","message"=>"您尚未取得云端下载权限，请申请或购买后再执行该操作。","link"=>$F_Purchase_URL,"linkButton"=>"购买"));
			goto endlabel;
		}
	}
	
	// Get Patch Info
	$sql = "SELECT `ID`, `Name`, `Identifier`, `UUID`, `Description`, `Author`, `Units` FROM `patches` WHERE `ID` = '" . $patchID . "' LIMIT 1";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取补丁列表失败：patches，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	if ($row != false) {
		// Get Patch Contents
		if (strlen($row[6]) > 0) {
			$units = $row[6];
			$UnitsExist = true;
		}
		elseif (file_exists("./patches/file_" . $patchID . ".dat")) {
			$units =  p_escape(file_get_contents("./patches/file_" . $patchID . ".dat"));
			
			// Import To Database
			if ($G_Import == true) {
				$SubUnits = mysql_real_escape_string($units);
				mysql_query("UPDATE `patches` SET `Units` = '" . $SubUnits . "' WHERE `ID` = '" . $patchID . "'",$con);
				$UnitsExist = true;
			}
			else {
				$UnitsExist = true;
			}
		}
		else {
			$UnitsExist = false;
		}
		if ($UnitsExist == true) {
			$t = time();
			$arr = array("label"=>$row[1],"identifier"=>$row[2],"uuid"=>$row[3],"downloadDate"=>(int)$t,"description"=>$row[4],"author"=>$row[5],"enabled"=>false,"units"=>"%units%","id"=>(int)$patchID);
			$patch = array("patch"=>$arr);
			$result = array("status"=>"success","result"=>$patch);
			$replace = true;
			// Add Download Times
			mysql_query("UPDATE `patches` SET `DownloadTimes` = `DownloadTimes` + 1 WHERE `ID` = '" . $patchID . "'",$con);
		}
		else {
		$result = array("status"=>"error","alert"=>array("title"=>"文件系统错误","message"=>"找不到指定文件。"));
		}
	}
	else {
		$result = array("status"=>"error","alert"=>array("title"=>"读取失败","message"=>"找不到指定记录。"));
	}
}

// Rate Patch

elseif ($action == "ratePatch") {
	$patchID = (int)$obj->{"patchID"};
	$UDID = stripslashes((string)$obj->{"udid"});
	$Rating = (double)$obj->{"rating"};
	$Stars = $Rating * 10;
	if ($patchID <= 0 or strlen($UDID) != 40 or $Rating <= 0) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：未知设备或补丁编号。"));
		goto endlabel;
	}
	
	$sql = "SELECT * FROM `rates` WHERE (`PatchID` = '" . $patchID . "' and `UserID` = '" . mysql_real_escape_string($UDID) . "') LIMIT 1";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取评分列表失败：rates，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	if ($row != false) {
		// Rate Exists
		$sql = "UPDATE `rates` SET `Stars` = '" . $Stars . "' WHERE (`PatchID` = '" . $patchID . "' and `UserID` = '" . mysql_real_escape_string($UDID) . "')";
		mysql_query($sql,$con);
	}
	else {
		// No Rates
		$sql = "INSERT INTO `rates`(`UserID`, `PatchID`, `Stars`) values('" . mysql_real_escape_string($UDID) . "', '" . $patchID . "', '" . $Stars . "')";
		$query = mysql_query($sql,$con);
		if ($query == false) {
			$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"添加评分失败：rates，请联系管理员。"));
			goto endlabel;
		}
	}
	
	// Get Average Rates
	$sql = "SELECT `ID`, `UserID`, `PatchID`, `Stars` FROM `rates` WHERE `PatchID` = '" . $patchID . "'";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取评分列表失败：rates，请联系管理员。"));
		goto endlabel;
	}
	$rates = 0;
	$ratetimes = 0;
	while ($row = mysql_fetch_row($query)) {
		$rates = (double)$rates + (double)$row[3];
		$ratetimes++;
	}
	if ($ratetimes > 0) {
		$averagerates = $rates/($ratetimes * 10);
	}
	else {
		$averagerates = 1;
	}
	
	// Update Average Rating
	mysql_query("UPDATE `patches` SET `AverageRating` = '" . $averagerates . "' WHERE `ID` = '" . $patchID . "'");
	$result = array("status"=>"success","result"=>array("averageRating"=>(double)$averagerates));
}

// Login Function

elseif ($action == "login") {
	$Username = stripslashes((string)$obj->{"username"});
	$Password = stripslashes((string)$obj->{"password"});
	if (strlen($Username) < 1 or strlen($Password) < 7) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：用户名或密码。"));
		goto endlabel;
	}
	
	// Verify Account
	$sql = "SELECT `Username`, `Password`, `Loginkey`, `ToShow` FROM `users` WHERE (`Username` = '" . mysql_real_escape_string($Username) . "' and `Password` = '" . mysql_real_escape_string($Password) . "') LIMIT 1";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取用户列表失败：users，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	$Account = (int)$row[3];
	if ($row != false) {
		if ($Account == -1) {
			$result = array("status"=>"error","alert"=>array("title"=>"登录失败","message"=>"您的注册信息正在审核中，请稍后再试。"));
		}
		elseif ($Account == -2) {
			$result = array("status"=>"error","alert"=>array("title"=>"登录失败","message"=>"您的账号存在安全问题，已被系统冻结，请联系管理员解锁。"));
		}
		else {
			$t = time();
			$sql = "UPDATE `users` SET `LastLoginStamp` = '" . date('Y-m-d H:i:s',$t) . "' WHERE `Username` = '" . mysql_real_escape_string($Username) . "'";
			mysql_query($sql,$con);
			$result = array("status"=>"success","result"=>array("session"=>(string)$row[2],"username"=>(string)$row[0]));
		}
	}
	else {
		$result = array("status"=>"error","alert"=>array("title"=>"登录失败","message"=>"用户名或密码错误，请重试。"));
	}
}

// Register

elseif ($action == "register") {
	$Username = stripslashes((string)$obj->{"username"});
	$Password = stripslashes((string)$obj->{"password"});
	$Email = stripslashes((string)$obj->{"email"});
	
	// Check String
	if (strlen($Username) < 4 OR strlen($Password) < 7 OR strlen($Email) <= 0) {
		$result = array("status"=>"error","alert"=>array("title"=>"注册失败","message"=>"注册信息限制：用户名不得少于 4 个字符，密码不得少于 7 个字符，电子邮箱不得为空，请重试。"));
		goto endlabel;
	}
	
	$sql = "SELECT `Username`, `Email` FROM `users` WHERE (`Username` = '" . mysql_real_escape_string($Username) . "' or `Email` = '" . mysql_real_escape_string($Email) . "') LIMIT 1";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取用户列表失败：users，请联系管理员。"));
		goto endlabel;
	}
	
	$row = mysql_fetch_row($query);
	if ($row != false) {
		$result = array("status"=>"error","alert"=>array("title"=>"注册失败","message"=>"注册信息检查：用户名或邮箱已被占用，请重试。"));
		goto endlabel;
	}
	else {
		goto addnew;
	}
	
	addnew:
	// Add new user
	$t = time();
	$NewKey = randstr(40);
	$sql = "INSERT INTO `users`(`Username`, `Password`, `Email`, `Loginkey`, `CreateStamp`, `LastLoginStamp`, `ToShow`) VALUES('" . mysql_real_escape_string($Username) . "','" . mysql_real_escape_string($Password) . "','" . mysql_real_escape_string($Email) . "','" . $NewKey . "','" . date('Y-m-d H:i:s',$t) . "','" . date('Y-m-d H:i:s',$t) . "','" . $G_RegCheck . "')";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"添加用户失败：users，请联系管理员。"));
		goto endlabel;
	}
	
	if ($G_RegCheck == 0) {
		$result = array("status"=>"success","result"=>array("session"=>(string)$NewKey,"username"=>(string)$Username));
	}
	else {
		$result = array("status"=>"error","alert"=>array("title"=>"注册成功","message"=>"您的注册信息已经提交，请等待审核通过。"));
	}
}

// Submit Patch

elseif ($action == "submitPatch") {
	// Check Content
	$Identifier = stripslashes((string)$obj->{"patch"}->{"appIdentifier"});
	$appName = stripslashes((string)$obj->{"patch"}->{"appName"});
	$description = (string)$obj->{"patch"}->{"cloudDescription"};

	
	if (strlen($Identifier) <= 4 or strlen($appName) == 0 or strlen($description) <= 14) {
		$result = array("status"=>"error","alert"=>array("title"=>"提交失败","message"=>"提交信息限制：补丁描述不得少于 14 个字符，应用标识符不得少于 4 个字符，应用名称不得为空，请重试。"));
		goto endlabel;
	}
	
	// Check Applist
	$sql = "SELECT * FROM `applist` WHERE `Identifier` = '" . mysql_real_escape_string($Identifier) . "' LIMIT 1";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取应用列表失败：applist，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	if ($row == false) {
		// Create Applist Item
		$sql = "INSERT INTO `applist`(`Name`, `Identifier`, `ToShow`) VALUES('" . mysql_real_escape_string($appName) . "','" . mysql_real_escape_string($Identifier) . "','0')";
		$query = mysql_query($sql,$con);
		if ($query == false) {
			$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"添加应用失败：applist，请联系管理员。"));
			goto endlabel;
		}
	}
	
	// Check Session
	$session = stripslashes((string)$obj->{"session"});
	$sql = "SELECT `ID`, `Username`, `Loginkey` FROM `users` WHERE (`Loginkey` = '" . mysql_real_escape_string($session) . "' and `ToShow` >= 0) LIMIT 1";
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取用户列表失败：users，请联系管理员。"));
		goto endlabel;
	}
	$row = mysql_fetch_row($query);
	if ($row == false) {
		$result = array("status"=>"logout","alert"=>array("title"=>"提交失败","message"=>"您必须重新登录以进行该操作。"));
		goto endlabel;
	}
	else {
		// Check UUID
		$ThisUserID = $row[0];
		$ThisUser = $row[1];
		$UUID = stripslashes((string)$obj->{"patch"}->{"UUID"});
		$sql = "SELECT `Name`, `Author`, `UUID`, `AuthorID`, `ToShow`, `ID`, `UploadStamp` FROM `patches` WHERE (`UUID` = '" . mysql_real_escape_string($UUID) . "') ORDER BY ID DESC LIMIT 1";
		$query = mysql_query($sql,$con);
		if ($query == false) {
			$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取补丁列表失败：patches，请联系管理员。"));
			goto endlabel;
		}
		$row = mysql_fetch_row($query);
		if ($row != false) {
			if ($row[3] == $ThisUserID or $row[1] == $ThisUser) {
				goto UpdateSubmit;
			}
			else {
				$result = array("status"=>"error","alert"=>array("title"=>"提交失败","message"=>"原补丁名：" . $row[0] . "\n原作者：" . $row[1] . "\n上传时间：" . $row[6] . "\n补丁已经被原作者提交，请勿重复操作。"));
				goto endlabel;
			}
		}
		else {
			$Mark = 0;
			goto NewSubmit;
		}
	}
	goto endlabel;
	
	NewSubmit:
	$SubName = mysql_real_escape_string(stripslashes((string)$obj->{"patch"}->{"name"}));
	if (strlen($SubName) > 60) {
		$result = array("status"=>"error","alert"=>array("title"=>"提交失败","message"=>"标题长度超出系统限制！"));
		goto endlabel;
	}
	$SubAuthor = mysql_real_escape_string($ThisUser);
	$SubAuthorID = $ThisUserID;
	$SubDescription = mysql_real_escape_string($description);
	$SubVersion =mysql_real_escape_string(stripslashes((string)$obj->{"version"}));
	$SubappVersion =mysql_real_escape_string(stripslashes((string)$obj->{"patch"}->{"appVersion"}));
	$SubappTargetVersion =mysql_real_escape_string(stripslashes((string)$obj->{"patch"}->{"appTargetVersion"}));
	$SubiOS =mysql_real_escape_string(stripslashes((string)$obj->{"ios"}));
	$SubiOS_Long = (int)$obj->{"iosLong"};
	$SubTime = time();
	$SubIdentifier =mysql_real_escape_string($Identifier);
	$SubUUID = mysql_real_escape_string($UUID);
	$SubUnits_raw = $obj->{"patch"}->{"units"};
	if ($SubUnits_raw == NULL) {
		$result = array("status"=>"error","alert"=>array("title"=>"提交失败","message"=>"您上传的补丁不包含任何有效 Units，请不要开玩笑！"));
		goto endlabel;
	}
	else {
		$SubUnits = mysql_real_escape_string(json_encode($SubUnits_raw));
	}
	$SubSwitchedOn = $obj->{"patch"}->{"switchedOn"};
	if ($SubSwitchedOn == true) {
		$SubSwitched = "true";
	}
	else {
		$SubSwitched = "false";
	}
	$SubappID = 0;
	$u_query = mysql_query("SELECT `ID` FROM `applist` WHERE `Identifier` = '" . $SubIdentifier . "'");
	if ($u_query != false) {
		$u_row = mysql_fetch_row($u_query);
		if ($u_row != false) {
			$SubappID = $u_row[0];
		}
	}
	$sql = "INSERT INTO `patches`(`Name`, `Author`, `AuthorID`, `Description`, `Version`, `iOS`, `Identifier`, `UUID`, `ToShow`, `Control`, `Units`, `DownloadTimes`,`iosLong`,`SwitchedOn`,`UploadStamp`,`appVersion`,`appTargetVersion`,`appID`) VALUES('" . $SubName . "','" . $SubAuthor . "','" . $SubAuthorID . "','" . $SubDescription . "','" . $SubVersion . "','" . $SubiOS . "','" . $SubIdentifier . "','" . $SubUUID . "','0','" . $Mark . "','" . $SubUnits . "','0','" . $SubiOS_Long . "','" . $SubSwitched . "','" . date('Y-m-d H:i:s',$SubTime) . "','" . $SubappVersion . "','" . $SubappTargetVersion . "','" . $SubappID . "')";
	
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"添加新补丁项目失败：patches，请联系管理员。"));
		goto endlabel;
	}
	
	$InsertID = mysql_insert_id($con);
	if ($InsertID == 0) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"获取新补丁自增编号失败：patches，请联系管理员。"));
		goto endlabel;
	}
	
	// Save Units As Files
	if ($G_Units == true) {
		$FileSize = file_put_contents("./patches/file_" . $InsertID . ".dat", $SubUnits);
	}
	else {
		$FileSize = 0;
	}
	if ($FileSize > 0 or $G_Units == false) {
		if ($Mark == 0) {
			$result = array("status"=>"success","result"=>array("id"=>$InsertID),"alert"=>array("title"=>"上传新补丁成功","message"=>"上传成功，上传新补丁审核预计在 3 到 5 个工作日内完成，请耐心等待。"));
		}
		else {
			$result = array("status"=>"success","result"=>array("id"=>$InsertID),"alert"=>array("title"=>"更新补丁成功","message"=>"上传成功，更新补丁审核预计在 1 到 2 个工作日内完成，请耐心等待。"));
		}
	}
	else {
		$result = array("status"=>"error","alert"=>array("title"=>"文件系统错误","message"=>"补丁数据文件写入失败，请联系管理员。"));
		mysql_query("UPDATE `patches` SET `ToShow` = '-1' WHERE `ID` = '" . $InsertID . "'",$con);
	}
	
	goto endlabel;
	
	UpdateSubmit:
	// Check Last Row
	$State = (int)$row[4];
	$Origin = (int)$row[5];
	$ReadableTime = date('Y-m-d H:i:s',strtotime($row[6]) + $G_TimeZone);
	if ($State != 1 and $State != 2) {
		$result = array("status"=>"error","alert"=>array("title"=>"更新失败","message"=>"该补丁的上一条更新请求（" . $ReadableTime . "）尚未处理完成，请耐心等待。"));
		goto endlabel;
	}
	
	// Mark Original Patches
	if ($State == 1) {
		$Mark = $Origin;
		goto NewSubmit;
	}
	while ($row = mysql_fetch_row($query)) {
		if ((int)$row[4] == 1) {
			$Mark = (int)$row[5];
			goto NewSubmit;
		}
	}
	
	// Invaild Patches
	$Mark = -1;
	goto NewSubmit;
	
}

// Rank

elseif ($action == "appTopList") {
	$type = (string)$obj->{"type"};
	$UDID = stripslashes((string)$obj->{"udid"});
	if (strlen($UDID) != 40) {
		$result = array("status"=>"error","alert"=>array("title"=>"请求错误","message"=>"错误的参数：未知设备。"));
		goto endlabel;
	}
	if ($type == "recent") {
		$Order = "ID";
		$sql = "SELECT * FROM `patches` WHERE `ToShow` = '1' ORDER BY `" . $Order . "` DESC LIMIT " . (string)$G_RankNum;
	}
	elseif ($type == "popular") {
		$Order = "DownloadTimes";
		$sql = "SELECT * FROM `patches` WHERE `ToShow` = '1' ORDER BY `" . $Order . "` DESC LIMIT " . (string)$G_RankNum;
	}
	elseif ($type == "topRated") {
		$Order = "AverageRating";
		$sql = "SELECT * FROM `patches` WHERE `ToShow` = '1' ORDER BY `" . $Order . "` DESC LIMIT " . (string)$G_RankNum;
	}
	else {
		$patches = array("patches"=>array());
		$result = array("status"=>"success","result"=>$patches);
	}
	
	MakePatchList:
	$query = mysql_query($sql,$con);
	if ($query == false) {
		$result = array("status"=>"error","alert"=>array("title"=>"数据库错误","message"=>"读取补丁列表失败：patches，请联系管理员。"));
		goto endlabel;
	}
	
	$i = 0;
	$t = time();
	while ($row = mysql_fetch_assoc($query)) {
		
		/*
		$c_sql = "SELECT `ID`, `UserID`, `PatchID`, `Stars` FROM `rates` WHERE (`UserID` = '" . $UDID . "' and `PatchID` = '" . $row[0] . "') LIMIT 1";
		$c_query = mysql_query($c_sql,$con);
		$c_row = mysql_fetch_row($c_query);
		if ($c_row != false) {
			$UserRate = $c_row[3] / 10;
		}
		else {
			$UserRate = 0;
		}
		*/
		
		// Wait For Beta
		$UserRate = 0;
		$arr[$i] = array("id"=>(int)$row['ID'],"label"=>$row['Name'],"enabled"=>(bool)$row['SwitchedOn'],"app"=>(int)$row['appID'],"description"=>$row['Description'],"author"=>$row['Author'],"authorID"=>(int)$row['AuthorID'],"downloads"=>(int)$row['DownloadTimes'],"identifier"=>$row['Identifier'],"uuid"=>$row['UUID'],"averageRating"=>(double)$row['AverageRating'],"userRating"=>(double)$UserRate,"downloadDate"=>$t);
		$i++;
	}
	if (count($arr) > 0) {
		$arr = array_values($arr);
		$patches = array("patches"=>$arr);
		$result = array("status"=>"success","result"=>$patches);
	}
	else {
		$patches = array("patches"=>array());
		$result = array("status"=>"success","result"=>$patches);
	}
	goto endlabel;
}
else {
	$result = array("status"=>"success");
	goto endlabel;
}

// Encoding and Sending
endlabel:

if ($result != null) {
	$substring = json_encode($result);
	
	if ($E_Tips != null) {
		$substring = str_replace('%tips%', $E_Tips, $substring);
	}
	if ($replace == true) {
		if (is_array(json_decode($units))) {
					$substring = str_replace('"%units%"', $units, $substring);
		}
		else {
					$substring = str_replace('"%units%"', "[".$units."]", $substring);
		}
	}
	
	$substring = str_replace('\\\\n', '\\n', $substring);
	$substring = str_replace('\n', '\\n', $substring);
	step:
	echo $substring;
}
else {
	$result = array("status"=>"error","alert"=>array("title"=>"请求失败","message"=>"非法操作！"));
	goto endlabel;
}

// Close Server
mysql_close($con);

// Function Area

/*
	产生随机字符串
	产生一个指定长度的随机字符串
	@access public 
	@param int $len 产生字符串的位数 
	@return string 
*/

function randstr($len = 40) { 
	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; 
	// Characters to build the password from
	mt_srand((double)microtime() * 1000000 * getmypid());
	// Seed the random number generater (Must be done) 
	$ranseed = ''; 
	while (strlen($ranseed) < $len) {
		$ranseed .= substr($chars,(mt_rand() % strlen($chars)),1);
	}
	return $ranseed;
}

/*
	中文 escape 编码
	@param string $input
	@return string
*/

function p_escape($str) {
	preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/",$str,$newstr);
	$ar = $newstr[0];
	foreach ($ar as $k=>$v) {
		if (ord($ar[$k]) >= 127) {
			$tmpString = bin2hex(iconv("GBK","ucs-2//IGNORE",$v));
			$reString .= "\\u" . $tmpString;
		}
		else {
			$reString .= $v;
		}
	}
	return $reString;
}

?>