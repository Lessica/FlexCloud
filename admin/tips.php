<?php

include("../connect.php");
// Connect to Server
$con = mysql_connect($server,$username,$password);
$title = "82Flex - 修改公告";

if (!$con) {
	echo("MYSQL ERROR!");
	goto endlabel;
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
	goto submitnewtips;
}

loginlabel:
$jump ="<meta http-equiv=\"refresh\" content=\"0; url=" . dirname("http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]) . "/login.php\">";

submitnewtips:
if ($_POST != NULL) {
	$title = "公告修改完成！";
	$contents = mysql_real_escape_string(str_replace("\n", "\\n", str_replace("\r\n", "\\n", stripslashes($_POST['newtips']))));
	mysql_query("UPDATE `info` SET `Contents` = '" . $contents . "' WHERE `Name` = 'G_Tips'",$con);
	$E_Tips = stripslashes($_POST['newtips']);
}
else {
	$GetTips = mysql_fetch_row(mysql_query("SELECT `Contents` FROM `info` WHERE `Name` = 'G_Tips'",$con));
	$E_Tips = $GetTips[0];
	if ($E_Tips == "") {
		$E_Tips = $G_Tips;
	}
}

endlabel:
mysql_close();

?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $jump; ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width; initial-scale=1.3;  minimum-scale=1.0; maximum-scale=2.0"/>
<meta name="MobileOptimized" content="240"/>
<title>82Flex 管理中心</title>
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
.ipt-btn-gray-s{ width:41px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/images/sqq/wap2/btn_s_gray.gif) no-repeat;}
.ipt-btn-gray-m{ width:60px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/info/wap2.0/page_common/btn_m_gray.gif) no-repeat;}
.ipt-btn-gray-l{ width:80px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/info/wap2.0/page_common/btn_l_gray.gif) no-repeat;}
.ipt-btn-gray-xl{ width:100px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/info/wap2.0/page_common/btn_xl_gray.gif) no-repeat;}
.ipt-btn-gray-g{ width:61px; height:21px; border:none; background:url(http://3gimg.qq.com:8080/images/sqq/wap2/btn_b_gold.gif) no-repeat;}
.ipt-txt{ margin-right:3px;color:#000000;border:1px solid #b1d5e5;}
.m-txt{ margin-right:3px;color:#000000;border:1px solid #b1d5e5;}
.m-txt{ width:20px; height:18px; margin:0 3px 0 0; border:1px solid #b1d5e5;}
.m10-txt{ margin-right:3px;color:#000000;border:1px solid #b1d5e5;}
.m10-txt{ width:200px; height:180px; margin:0 3px 0 0; border:1px solid #b1d5e5;}
.indexmsg-content{ padding:2px 1px; border-bottom:1px solid #c3c3c3;}
.ipt-intro{ margin-right:3px;}
/* Header Details */
.site-logo{ padding:5px 0 0 5px; background:#fff;}
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
<div class="module-title"><p>　<?php echo $title; ?></p></div>
<div class="module-content">
<form name="editform" action="tips.php?token=<?php echo $token; ?>" method="post"><br/>
　修改公告：<br/>
　<textarea name="newtips" type="text" emptyok="false" class="m10-txt" /><?php echo str_replace("\\n", "\n", htmlspecialchars($E_Tips)); ?></textarea><br/>
　<input type="submit" value="确认"/><br/>
</form>
<br/>
<a href="admin.php?token=<?php echo $token; ?>">　返回首页</a><br/>
<br/>
</body>
</html>
