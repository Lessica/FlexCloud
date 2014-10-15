<?php

include("../connect.php");
// Connect to Server
$con = mysql_connect($server,$username,$password);
$title = "82Flex - 登录管理中心";

if (!$con) {
	echo("MYSQL ERROR!");
	$title = "数据库连接失败，请联系管理员！";
	goto endlabel;
  }

mysql_query("SET NAMES utf8",$con);
mysql_select_db($database,$con);

if ($_GET['logout'] == "yes" and strlen($_GET['token']) != 0) {
	$newkey = randstr(40);
	mysql_query("UPDATE `users` SET `Loginkey` = '" . $newkey . "' WHERE `Loginkey` = '" . $_GET['token'] . "'",$con);
	$title = "注销用户成功！";
	goto endlabel;
}

if ($_POST != NULL) {
	$Username = mysql_real_escape_string(stripslashes($_POST['username']));
	$Password = mysql_real_escape_string(stripslashes($_POST['password']));
	if (strlen($Username) < 4 or strlen($Password) < 7) {
		$title = "登录失败：请输入正确的用户名或密码！";
		goto endlabel;
	}
	
	$LoginInfo = mysql_fetch_row(mysql_query("SELECT `Loginkey` FROM `users` WHERE (`Username` = '" . $Username . "' and `Password` = '" . $Password . "') and `Rights` >= 1",$con));
	if ($LoginInfo != false) {
		goto success;
	}
	else {
		$title = "登录失败：用户名或密码不正确，或者您没有访问权限！";
		goto endlabel;
	}
	
	success:
	$jump ="<meta http-equiv=\"refresh\" content=\"0; url=" . dirname("http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]) . "/admin.php?token=" . $LoginInfo[0] . "\">";
}

endlabel:
mysql_close();

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

?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $jump; ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width; initial-scale=1.3;  minimum-scale=1.0; maximum-scale=2.0"/>
<meta name="MobileOptimized" content="240"/>
<title>82Flex 登录</title>
<style type="text/css">
/* Reset */
body,div,p,a,table,textarea,form,input,img,ul,li{ margin:0; padding:0; }
body,div,p,th,td,textarea,input{ font-size:13px;}
/* width:238px; */
body{ margin:0 auto; border:1px solid #c6c6c6;}
li{ list-style:none; text-indent:0;}
/* Common Elements */
a{text-decoration:none;color:#004299;}
/* Common Buttons */
.m10-txt{ margin-right:3px;color:#000000;border:1px solid #b1d5e5;}
.m10-txt{ width:200px; height:24px; margin:0 3px 0 0; border:1px solid #b1d5e5;}
/* Module Details
url(http://3gimg.qq.com:8080/images/sqq/wap2/bg_module_title.png) repeat-x
*/
.module-title{ height:24px; line-height:24px; padding:0 0 0 0px; background:#F0F0F0; border-top:1px solid #C3C3C3;}
.module-title .separate{ margin-left:8px; margin-right:8px; color:#bbbbbb;}
.module-title p{ color:#ee4d08;font-weight:bold; line-height:24px; padding:2px 0 0 0;}
.module-title a{ color:#004299;font-weight:normal;}
.module-subtitle{ height:24px; line-height:24px; padding:0 0 0 8px; background:#eaeaea;}
.module-subtitle .separate{ margin-left:3px; margin-right:3px; color:#bbbbbb;}
.module-subtitle a{ color:#004299;font-weight:normal;}
.module-content{ padding:3px 0;}
.module-content p{padding:0 0 0 0px; line-height:18px;}
.module-content p a{color:#004299; padding:3px 0;}
.module-content ul{}
.module-content ul li{ line-height:18px; padding:0 0 0 19px; background:url(http://3gimg.qq.com:8080/images/sqq/wap2/list_dot.gif) 10px 9px no-repeat;}
.module-content .tips{ padding:0 0 0 0px; line-height:18px;}
.module-content .tips span {color:#000000;}
.module-content .tips a {color:#004299;}
.module-content .mark{ padding:0; line-height:18px; color:#FF0000;}
.module-content .list{ padding:0 0 0 19px; line-height:18px;}
.module-content .para{ line-height:18px;  padding:0 0 0 10px; margin:0 0 5px 0;}
.module-content .img-s{}
.module-content .img-m{}
.module-content .img-l{ text-align:center;}
.module-content .alert-padding { padding:0 0 0 20px;}
/* Text Style */
.txt-bold{ font-weight:bold;}
.txt-fade{ font-size:13px; color:#8a8a8a;}
.txt-gray{ color:#8a8a8a; font-weight:normal;}
.txt-blue{ color:#236cc6;}
.stock-up{ color:#fc0000;}
.stock-down{ color:#20b800;}
/* Footer Details */
.footer{ background:#f0f0f0; padding:5px 10px;}
.footer p{ text-align:center; line-height:18px; color:#515151;}
.footer p a{ color:#515151;}
.footer .separate{ margin-left:3px; margin-right:3px;}
.footer .txt-em{}
/* Header Details */
.main-nav{ padding:1px 0 0 10px; background:#FE7A1E url(http://3gimg.qq.com:8080/images/sqq/wap2/bg_module_title2.png) repeat-x;}
.main-nav p{ line-height:23px; color:#fff; font-weight:bold;}
.main-nav p span {color:#FFFFFF; margin:0 7px 0 0;}
.main-nav p a {color:#FFFFFF; margin:0 7px 0 0;}
.main-nav2{ padding:1px 0 0 10px; background:#FE7A1E url(http://3gimg.qq.com:8080/images/sqq/wap2/bg_module_title2.png) repeat-x;}
.main-nav2 p{ line-height:23px; color:#fff; font-weight:bold;}
.main-nav2 p span {color:#FFFFFF; margin:0 7px 0 0;}
.main-nav2 p a {color:#FFFFFF; margin:0 7px 0 0;}
.search-box{ padding:3px 10px; background:#FEEADB;}
.search-box .ipt-txt{ width:150px; height:18px; margin:0 3px 0 0; border:1px solid #b1d5e5;}
/* Content Details */
.focus{}
.focus-stock{ color:#4c4c4c;}
.focus-stock a{ color:#4c4c4c;}
.focus-image{ text-align:center;}
/* Module Costom  */
.module{}
.module-content ul li a{ color:#004299;}
.module-content .list a{ color:#004299;}
/* Footer */
.footer .version a{ color:#004299}
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
.contenttop {
color:#000000;
line-height:16px;
padding:5px 10px;
border-top:1px solid #c3c3c3;
}
.contenttop a {
color:#004299;
margin:0;
}
.biztop {
line-height:16px;
padding:5px 10px;
border-top:1px solid #c3c3c3;
}
.biztop span {
color:#000000;
font-weight:bold;
}
.biztop a {
color:#004299;
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
.module-topline {
line-height:16px;
padding:5px 10px;
border-top:1px solid #c3c3c3;
}
.module-topline p {
color:#8A8A8A;
line-height:18px;
}
.module-topline a {
color:#004299;
margin:0 0 0 0;
}
.txt-warning  {
background:none repeat scroll 0 0 #FFF9B7;
border-bottom:1px solid #E9DDC5;
border-top:1px solid #E9DDC5;
padding:3px 5px 3px 5px;
}
.newstitle-box {
background:none repeat scroll 0 0 #E3EEF8;
border-bottom:1px solid #9FC6EC;
}
.atc-title  {
font-weight:bold;
padding:0 10px;
text-align:center;
}
.atc-title a {
color:#000000;
}
.atc-title a:hover {
color:#3F3F3F;
}
.atc-from {
color:#A7A7A7;
font-size:12px;
padding:0 10px;
text-align:center;
}
.atc-date {
color:#A7A7A7;
font-size:12px;
padding:0 10px;
text-align:center;
}
.atc-img {
margin:5px 0;
padding:0 10px;
text-align:center;
}
.atc-img .separate {
margin:0 2px;
}
.atc-subtitle {
background:url("http://3gimg.qq.com:8080/images/sqq/wap2/bg_module_title.png") repeat-x scroll 0 0 #F0F0F0;
font-weight:bold;
height:24px;
line-height:24px;
padding:0 0 0 10px;
}
.atc-subtitle a {
color:#3F3F3F;
}
.atc-subtitle .separate {
margin:0 2px;
}
.atc-context {
padding:5px 10px;
}
.atc-context .ipt-txt {
width:26px;
}
.spacing-5 {
padding:5px 0;
}
.tabs-1 {
padding:0 0 0 5px;
}
.tabs-2 {
padding:0 0 0 10px;
}
.tabs-2 a {color:#004299;}
.ipt-longtxt{ width:150px; height:18px; margin:0 3px 0 0; border:1px solid #b1d5e5;}
.chkbox{border:1px solid #000000; color:#000000;}
</style>
</head>
<body class="index">
<div class="module-title"><p>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $title;?></p></div>
<div class="module-content">

<form name="loginform" action="login.php" method="post">
<br/>&nbsp;&nbsp;&nbsp;&nbsp;用户名：<br/>&nbsp;&nbsp;&nbsp;&nbsp;<input name="username" type="text" emptyok="false" class="m10-txt" /><br/>
<br/>&nbsp;&nbsp;&nbsp;&nbsp;密码：<br/>&nbsp;&nbsp;&nbsp;&nbsp;<input name="password" type="password" emptyok="false" class="m10-txt" /><br/>
&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="登录"/><br/>
</form>
</div>
<br/>
<br/>
</body>
</html>
