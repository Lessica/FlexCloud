<?php

include("../connect.php");
// Connect to Server
$con = mysql_connect($server,$username,$password);

if (!$con) {
	echo("MYSQL ERROR!");
	goto loginlabel;
  }

mysql_query("SET NAMES utf8",$con);
mysql_select_db($database,$con);

$token = mysql_real_escape_string(stripslashes((string)$_GET['token']));

if (strlen($token) == 0) {
	goto loginlabel;
}

$query = mysql_query("SELECT `Username`, `Loginkey`, `Rights`, `Money`, `LastLoginStamp` FROM `users` WHERE (`Loginkey` = '" . $token . "' and `Rights` >= 1)",$con);
$LoginInfo = mysql_fetch_row($query);

if ($LoginInfo == false) {
	goto loginlabel;
} 
else {
	goto mainlabel;
}

loginlabel:
$jump ="<meta http-equiv=\"refresh\" content=\"0; url=" . dirname("http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]) . "/login.php\">";

mainlabel:
$list_type = $_GET['type'];
$list_toshow = (int)$_GET['toshow'];
$list_search = stripslashes((string)$_GET['s']);
$list_search_type = $_GET['t'];
$list_contents = "";
if ($list_type == "search_patches") {
	if (strlen($list_search) == 0) {
		$list_contents = "";
		goto endlabel;
	}
	else {
		switch ($list_search_type) {
			case '1':
				$t = "Name";
				break;
			case '2':
				$t = "Author";
				break;
			case '3':
				$t = "Description";
				break;
			case '4':
				$t = "Identifier";
				break;
			default:
				goto endlabel;
		}
		$query = mysql_query("SELECT `ID`, `Name`, `Author`,`Description`, `ToShow` FROM `patches` WHERE `" . $t . "` LIKE '%" . mysql_real_escape_string($list_search) . "%' ORDER BY `ID` DESC LIMIT 25",$con);
		while ($row = mysql_fetch_row($query)) {
			if ((int)$row[4] == 1) {
				$list_contents .= "<li>Label: " . htmlspecialchars($row[1]) . "</br>Author: " . $row[2] . "</br>Description: "  . htmlspecialchars($row[3]) . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=patches&toshow=2&id=" . $row[0] . "\">隐藏" . $button . "</a>　" . "<a href = \"view.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">查看</a>　" . "<a href = \"edit.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">编辑</a>　" . "</li>";
			}
			elseif ((int)$row[4] == 2) {
				$list_contents .= "<li>Label: " . htmlspecialchars($row[1]) . "</br>Author: " . $row[2] . "</br>Description: "  . htmlspecialchars($row[3]) . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=patches&toshow=1&id=" . $row[0] . "\">显示" . $button . "</a>　" . "<a href = \"view.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">查看</a>　" . "<a href = \"edit.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">编辑</a>　" . "</li>";
			}
			else {
				$list_contents .= "<li>Label: " . htmlspecialchars($row[1]) . "</br>Author: " . $row[2] . "</br>Description: "  . htmlspecialchars($row[3]) . "</br>" . "<a href = \"view.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">查看</a>　" . "<a href = \"edit.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">编辑</a>　" . "</li>";
			}
		}
	}
}
elseif ($list_type == "search_users") {
	if (strlen($list_search) == 0) {
		$list_contents = "";
		goto endlabel;
	}
	else {
		$query = mysql_query("SELECT `ID`, `Username`, `CreateStamp`, `ToShow` FROM `users` WHERE `Username` LIKE '%" . mysql_real_escape_string($list_search) . "%' ORDER BY `ID` DESC LIMIT 50",$con);
		while ($row = mysql_fetch_row($query)) {
			if ((int)$row[3] >= 0) {
				$list_contents .= "<li>Username: " . htmlspecialchars($row[1]) . "</br>Register Time: " . $row[2] . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=users&toshow=-2&id=" . $row[0] . "\">封禁用户</a>　" . "<a href = \"view.php?token=" . $token . "&type=users&id=" . $row[0] . "\">查看</a>　" . "</li>";
			}
			else {
				$list_contents .= "<li>Username: " . htmlspecialchars($row[1]) . "</br>Register Time: " . $row[2] . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=users&toshow=0&id=" . $row[0] . "\">释放用户</a>　" . "<a href = \"view.php?token=" . $token . "&type=users&id=" . $row[0] . "\">查看</a>　" . "</li>";
			}
		}
	}
}
elseif ($list_type == "search_applist") {
	if (strlen($list_search) == 0) {
		$list_contents = "";
		goto endlabel;
	}
	else {
		switch ($list_search_type) {
			case '1':
				$t = "Name";
				break;
			case '2':
				$t = "Identifier";
				break;
			default:
				goto endlabel;
		}
		$query = mysql_query("SELECT `ID`, `Name`, `Identifier` FROM `applist` WHERE `" . $t . "` LIKE '%" . mysql_real_escape_string($list_search) . "%' ORDER BY `ID` DESC LIMIT 50",$con);
		while ($row = mysql_fetch_row($query)) {
			$list_contents .= "<li>Label: " . htmlspecialchars($row[1]) . "</br>Identifier: " . htmlspecialchars($row[2]) . "</br>" . "<a href = \"list.php?token=" . $token . "&type=search_patches&t=4&s=" . htmlspecialchars($row[2]) . "\">查看补丁</a>　" . "<a href = \"edit.php?token=" . $token . "&type=applist&id=" . $row[0] . "\">编辑名称</a>　" . "</li>";
		}
	}
}
elseif ($list_type == "patches") {
	$query = mysql_query("SELECT `ID`, `Name`, `UploadStamp`, `Control` FROM `patches` WHERE `ToShow` = '" . (string)$list_toshow . "' ORDER BY `ID` DESC LIMIT 50",$con);
	while ($row = mysql_fetch_row($query)) {
		if ((int)$row[3] != 0) {
			$button = "升级";
		}
		else {
			$button = "审核";
		}
		$list_contents .= "<li>" . htmlspecialchars($row[1]) . "</br>" . $row[2] . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=patches&toshow=1&id=" . $row[0] . "\">通过" . $button . "</a>　" . "<a href = \"submit.php?token=" . $token . "&type=patches&toshow=2&id=" . $row[0] . "\">拒绝" . $button . "</a>　" . "<a href = \"view.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">查看</a>　" . "<a href = \"edit.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">编辑</a>　" . "</li>";
	}
}
elseif ($list_type == "applist") {
	$query = mysql_query("SELECT `ID`, `Name`, `Identifier` FROM `applist` WHERE `ToShow` = '" . (string)$list_toshow . "' ORDER BY `ID` DESC LIMIT 50",$con);
	while ($row = mysql_fetch_row($query)) {
		$list_contents .= "<li>" . htmlspecialchars($row[1]) . "</br>" . htmlspecialchars($row[2]) . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=applist&toshow=1&id=" . $row[0] . "\">通过</a>　" . "<a href = \"submit.php?token=" . $token . "&type=applist&toshow=2&id=" . $row[0] . "\">拒绝</a>　" . "<a href = \"edit.php?token=" . $token . "&type=applist&id=" . $row[0] . "\">编辑名称</a>　" . "</li>";
	}
}
elseif ($list_type == "users") {
	$query = mysql_query("SELECT `ID`, `Username`, `CreateStamp` FROM `users` WHERE `ToShow` = '" . (string)$list_toshow . "' ORDER BY `ID` DESC LIMIT 50",$con);
	while ($row = mysql_fetch_row($query)) {
		$list_contents .= "<li>" . htmlspecialchars($row[1]) . "</br>" . $row[2] . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=users&toshow=0&id=" . $row[0] . "\">通过</a>　" . "<a href = \"submit.php?token=" . $token . "&type=users&toshow=-2&id=" . $row[0] . "\">拒绝</a>　" . "<a href = \"view.php?token=" . $token . "&type=users&id=" . $row[0] . "\">查看</a>　" . "</li>";
	}
}

elseif ($list_type == "recent") {
	$query = mysql_query("SELECT `ID`, `Name`, `UploadStamp` FROM `patches` WHERE `ToShow` = '1' ORDER BY `ID` DESC LIMIT 50",$con);
	while ($row = mysql_fetch_row($query)) {
		$list_contents .= "<li>" . htmlspecialchars($row[1]) . "</br>" . $row[2] . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=patches&toshow=2&id=" . $row[0] . "\">隐藏" . $button . "</a>　" . "<a href = \"view.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">查看</a>　" . "<a href = \"edit.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">编辑</a>　" . "</li>";
	}
}
elseif ($list_type == "popular") {
	$query = mysql_query("SELECT `ID`, `Name`, `UploadStamp` FROM `patches` WHERE `ToShow` = '1' ORDER BY `DownloadTimes` DESC LIMIT 50",$con);
	while ($row = mysql_fetch_row($query)) {
		$list_contents .= "<li>" . htmlspecialchars($row[1]) . "</br>" . $row[2] . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=patches&toshow=2&id=" . $row[0] . "\">隐藏" . $button . "</a>　" . "<a href = \"view.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">查看</a>　" . "<a href = \"edit.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">编辑</a>　" . "</li>";
	}
}
elseif ($list_type == "toprated") {
	$query = mysql_query("SELECT `ID`, `Name`, `UploadStamp` FROM `patches` WHERE `ToShow` = '1' ORDER BY `AverageRating` DESC LIMIT 50",$con);
	while ($row = mysql_fetch_row($query)) {
	$list_contents .= "<li>" . htmlspecialchars($row[1]) . "</br>" . $row[2] . "</br>" . "<a href = \"submit.php?token=" . $token . "&type=patches&toshow=2&id=" . $row[0] . "\">隐藏" . $button . "</a>　" . "<a href = \"view.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">查看</a>　" . "<a href = \"edit.php?token=" . $token . "&type=patches&id=" . $row[0] . "\">编辑</a>　" . "</li>";
	}
}

endlabel:
mysql_close();

?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $jump; ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width; initial-scale=1.3;  minimum-scale=1.0; maximum-scale=2.0"/>
<meta name="MobileOptimized" content="240"/>
<title>82Flex 管理中心</title>
<style type="text/css">
body,div,p,a,table,textarea,form,input,img,ul,li{margin:0;padding:0;}
body{font-size:13px;margin:0 auto; line-height:18px;}
li{list-style:none; text-indent:0;}
img,a img{border:0;}
a{text-decoration:none;color:#236cc6;}
/* Text Style */
.txt-bold{ font-weight:bold;}
.txt-normal{font-weight:normal;}
.txt-fade{ font-size:13px; color:#8a8a8a;}
.txt-gray{ color:#8a8a8a;}
.stock-up{ color:#fc0000; font-family:Verdana;}
.stock-down{ color:#20b800; font-family:Verdana;}
/* Header Details */
.mq-logo{ padding:5px 0 0 5px; padding:5px 0 0 10px; background:#fff;}
.main-nav{ padding:1px 0 0 12px; background:#ff7f1e url(http://3gimg.qq.com/info/wap2.0/mq/main_nav.png) repeat-x;}
.main-nav p{ line-height:24px;}
.main-nav p a{ margin:0 7px 0 0; color:#fff;}
.main-nav .current{color:#fde0c7}
.search-box{ padding:3px 0 3px 35px; background:#e0eef3 url(http://3gimg.qq.com:8080/info/wap2.0/index/icon_soso.gif) 12px 6px no-repeat;}
.search-box .ipt-txt{ width:125px; height:18px; margin:0 3px 0 0; border:1px solid #b1d5e5;}
.status{padding:6px 0 6px 12px;}
.bt{border-bottom:1px solid #cdcdcd;}
.bb{border-bottom:1px solid #eeeeee;}
.status a{color:#226cc5;}
/* Module Details */
.module-title{ height:24px; line-height:24px; padding:0 0 0 12px; background:#eeeeee; color:#EE4D08; font-weight:700;}
.module-title .separate{ margin-left:5px; margin-right:5px;}
.module-subtitle{ font-size:12px; line-height:16px; background:#eaeaea; padding:0 0 0 5px;}
.module-subtitle .separate{ margin-left:5px; margin-right:5px;}
.module-content{ padding:3px 0;}
.module-content ul{}
.module-content ul li{ line-height:18px; padding:0 0 0 12px;}
.module-content .list{ padding:0 0 0 12px; line-height:18px;}
.module-content .para{ line-height:18px;  padding:0 12px 8px 12px; margin:10px 0 0px 0;}
.item ul li{border-bottom:1px solid #eeeeee; padding:6px 0 6px 12px;}
.item ul li p{line-height:22px;}
.module-content .img-s{}i
.module-content .img-m{}
.module-content .img-l{ text-align:center;}
.module{}
.module-title a{color:#4c4c4c;}
.module-content ul li a{color:#236cc6;}
.module-content .list a{color:#236cc6;}
.page p{padding:0 0 0 12px;}
.page p a{color:#236cc6;}
/* Content Details */
.focus{}
.focus-stock{ color:#4c4c4c;}
.focus-stock a{ color:#4c4c4c;}
.focus-image{ text-align:center;}
/* Footer Details */
.footer{ background:#f0f0f0; padding:5px 10px;}
.footer .version a{ color:#004299;}
.footer p{ text-align:center; line-height:18px; color:#515151;}
.footer p a{ color:#515151;}
.footer .separate{ margin-left:3px; margin-right:3px;}
.footer .txt-em{}
.ft-link p{line-height:18px;  padding:0 0 0 10px; margin:0 0 5px 0;}
.ft-link p a{ color:#515151;}
.main-nav2{ padding:1px 0 0 10px; background:#FE7A1E url(http://61.177.126.130/images/sqq/wap2/bg_module_title2.png) repeat-x;}
.main-nav2 p{ line-height:23px; color:#fff; font-weight:bold;}
.main-nav2 p span {color:#FFFFFF; margin:0 7px 0 0;}
.main-nav2 p a {color:#FFFFFF; margin:0 7px 0 0;}
.top {
color:#000000;
line-height:16px;
padding:5px 10px;
border-top:1px solid #c3c3c3;
}
.top a {
color:#8A8A8A;
margin:0;
}
.zt {
color:#8A8A8A;
line-height:16px;
padding:5px 10px;
}
.zt a {
color:#004299;
}
</style>
</head>
<body class="index">
<div class="main-nav">
<p>
<a href="admin.php?token=<?php echo $token; ?>">首页</a><a href="list.php?token=<?php echo $token; ?>&type=patches&toshow=0">审核</a>
</p>
</div>
<div class="module-content">
<div class="module-title"><a href = "list.php?token=<?php echo $token; ?>&type=patches&toshow=0">审核补丁</a>　<a href = "list.php?token=<?php echo $token; ?>&type=applist&toshow=0">审核应用</a>　<a href = "list.php?token=<?php echo $token; ?>&type=users&toshow=-1">审核用户</a></div>
<div class="module-title"><a href = "list.php?token=<?php echo $token; ?>&type=recent">最新补丁</a>　<a href = "list.php?token=<?php echo $token; ?>&type=popular">热门补丁</a>　<a href = "list.php?token=<?php echo $token; ?>&type=toprated">最高评分</a></div>
<div class="module-content item"><ul>
<?php
if ($list_contents != "") {
	echo $list_contents;
}
else {
	echo "<li>未找到符合条件的项目！</li>";
}
?>
</ul></div>
<div class="module-content">
<p>
<br/>
<br/>
</p></div>
</body>
</html>
