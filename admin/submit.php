<?php

include("../connect.php");
// Connect to Server
$con = mysql_connect($server,$username,$password);
$alert_contents = "未知请求。";
$return_num = -1;

if (!$con) {
	$alert_contents = "数据库连接失败！";
	goto endlabel;
  }

mysql_query("SET NAMES utf8",$con);
mysql_select_db($database,$con);

$token = mysql_real_escape_string(stripslashes((string)$_GET['token']));

if (strlen($token) == 0) {
	$alert_contents = "权限验证失败！";
	goto endlabel;
}

$query = mysql_query("SELECT `Username`, `Loginkey`, `Rights`, `Money`, `LastLoginStamp` FROM `users` WHERE (`Loginkey` = '" . $token . "' and `Rights` >= 1)",$con);
$LoginInfo = mysql_fetch_row($query);

if ($LoginInfo == false) {
	$alert_contents = "权限验证失败！";
	goto endlabel;
}
else {
	goto mainlabel;
}

mainlabel:
$update_type = $_GET['type'];
$update_id = (int)$_GET['id'];

if ($update_id <= 0) {
	$alert_contents = "数据更新失败：id 不得为空。";
}

if ($update_type == "edit_patches") {
	$update_name = mysql_real_escape_string($_POST['name']);
	$update_description = mysql_real_escape_string($_POST['description']);
	if (strlen($update_name) >= 4 && strlen($update_description) >= 14) {
		$query = mysql_query("UPDATE `patches` SET `Name` = '" . $update_name . "', `Description` = '" . $update_description . "' WHERE `ID` = '" . (string)$update_id . "'");
		if ($query == FALSE) {
			$alert_contents = "数据更新错误：" . mysql_error();
		}
		else {
			$alert_contents = "数据更新成功！";
			$return_num = -2;
		}
	}
	else {
		$alert_contents = "数据更新失败：Name 不得少于 4 字节，Description 不得少于 14 字节。";
	}
}
elseif ($update_type == "edit_applist") {
	$update_name = mysql_real_escape_string($_POST['name']);
	if (strlen($update_name) >= 4) {
		$query = mysql_query("UPDATE `applist` SET `Name` = '" . $update_name . "' WHERE `ID` = '" . (string)$update_id . "'");
		if ($query == FALSE) {
			$alert_contents = "数据更新错误：" . mysql_error();
		}
		else {
			$alert_contents = "数据更新成功！";
			$return_num = -2;
		}
	}
	else {
		$alert_contents = "数据更新失败：Name 不得少于 4 字节。";
	}
}
elseif ($update_type == "patches") {
	$update_toshow = (int)$_GET['toshow'];
	if ($update_toshow == 1) {
		$row = mysql_fetch_row(mysql_query("SELECT `Control` FROM `patches` WHERE (`ID` = '" . (string)$update_id . "' and `Control` != '0')",$con));
		if ($row != false) {
			mysql_query("UPDATE `patches` SET `ToShow` = '2' WHERE `ID` = '" . $row[0] . "'",$con);
		}
	}
	$query = mysql_query("UPDATE `patches` SET `ToShow` = '" . (string)$update_toshow . "' WHERE `ID` = '" . (string)$update_id . "'",$con);
	if ($query == true) {
		$alert_contents = "数据更新成功！";
	}
	else {
		$alert_contents = "数据更新错误：" . mysql_error();
	}
}
elseif ($update_type == "applist") {
	$update_toshow = (int)$_GET['toshow'];
	mysql_query("UPDATE `applist` SET `ToShow` = '" . (string)$update_toshow . "' WHERE `ID` = '" . (string)$update_id . "'",$con);
	if ($query == true) {
		$alert_contents = "数据更新成功！";
	}
	else {
		$alert_contents = "数据更新错误：" . mysql_error();
	}
}
elseif ($update_type == "users") {
	$update_toshow = (int)$_GET['toshow'];
	mysql_query("UPDATE `users` SET `ToShow` = '" . (string)$update_toshow . "' WHERE `ID` = '" . (string)$update_id . "'",$con);
	if ($query == true) {
		$alert_contents = "数据更新成功！";
	}
	else {
		$alert_contents = "数据更新错误：" . mysql_error();
	}
}

endlabel:
mysql_close();
header("Content-Type: text/html; charset=UTF-8");
echo "<script>alert(\"" . $alert_contents . "\");</script>";
echo "<script>history.go(" . (string)$return_num . ");</script>";
?>
