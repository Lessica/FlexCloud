<?php

include("../connect.php");
// Connect to Server
$con = mysql_connect($server,$username,$password);
$title = "查看详情";
$tips = "没有可以显示的内容。";
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
$q_info = mysql_query("SELECT `Name`, `Counts` FROM `info` WHERE `Name` = 'PV'",$con);
$info = mysql_fetch_row($q_info);
$PV = $info[1];
$q_info = mysql_query("SELECT count(*) FROM `applist`");
$info = mysql_fetch_row($q_info);
$num_1 = $info[0];
$q_info = mysql_query("SELECT count(*) FROM `patches`");
$info = mysql_fetch_row($q_info);
$num_2 = $info[0];
$q_info = mysql_query("SELECT count(*) FROM `users`");
$info = mysql_fetch_row($q_info);
$num_3 = $info[0];

$content = "　Cloud Stats: \n\n　mysql.stat(): \n　" . str_replace("  ","\n　",mysql_stat()) . "\n\n　PV Counts: " . $PV . "\n　Number Of Applications: " . $num_1 . "\n　Number Of Patches: " . $num_2 . "\n　Number Of Users: " . $num_3;

endlabel:
mysql_close();

?>

<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $jump; ?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta http-equiv="Cache-control" content="max-age=0" />
<meta name="viewport" content="width=device-width; initial-scale=1.3;  minimum-scale=1.0; maximum-scale=2.0"/>
<meta name="MobileOptimized" content="240"/>
<title>82Flex - 管理中心</title>
<style type="text/css">
/* Reset */
body,div,p,a,table,textarea,form,input,img,ul,li{ margin:0; padding:0; border:0;}
body,div,p,th,td,textarea,input{ font-size:13px;}
/* width:238px; */
body{ margin:0 auto; border:1px solid #c6c6c6;}
li{ list-style:none; text-indent:0;}
/* Common Elements */
a{ text-decoration:none;}
/* Common Buttons */
.ipt-btn-gray-s{ width:41px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/images/sqq/wap2/btn_s_gray.gif) no-repeat;}
.ipt-btn-gray-m{ width:60px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/info/wap2.0/page_common/btn_m_gray.gif) no-repeat;}
.ipt-btn-gray-l{ width:80px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/info/wap2.0/page_common/btn_l_gray.gif) no-repeat;}
.ipt-btn-gray-xl{ width:100px; height:20px; border:none; background:url(http://3gimg.qq.com:8080/info/wap2.0/page_common/btn_xl_gray.gif) no-repeat;}
.ipt-txt{ margin-right:3px;color:#000000;border:1px solid #b1d5e5;}
.ipt-longtxt{ width:150px; height:18px; margin:0 3px 0 0; border:1px solid #b1d5e5;}
.module-title{ height:24px; line-height:24px; padding:0 0 0 10px; background:#F0F0F0; border-top:1px solid #C3C3C3;}
.module-title p{ color:#ee4d08;font-weight:bold; line-height:24px; padding:2px 0 0 0;}
.module-content{ padding:3px 0;}
.module-content p{padding:0 0 0 10px; line-height:18px;}
.module-content p a{color:#004299; padding:3px 0;}
.module-content p span{ color:#8A8A8A; }
.module-content .tips{ padding:0 0 0 10px; line-height:18px;}
.module-content .tips span {color:#000000;}
.module-content .tips a {color:#004299;}
/* Footer Details */
.footer{ background:#f0f0f0; padding:5px 10px;}
.footer p{ text-align:center; line-height:18px; color:#515151;}
.footer p a{ color:#515151;}
.footer .separate{ margin-left:3px; margin-right:3px;}
.footer .version a{ color:#004299}
.top { color:#000000; line-height:16px; padding:5px 10px; border-top:1px solid #c3c3c3;}
.top a { color:#8A8A8A; margin:0;}
.zt { color:#8A8A8A; line-height:16px; padding:5px 10px;}
.zt a { color:#004299;}
</style>
</head>
<body>
<div class="module-title"><p>　<?php echo $title; ?><br/></p></div>
<div class="module-content">
<p class="tips">
<pre>
<?php
    print_r (htmlspecialchars($content));
?>
</pre>
</p>
</div>
<div class="module-content">
<p class="tips">
<a href="javascript:history.go(-1);">　返回上一页</a>
</br>
</br>
<a href="admin.php?token=<?php echo $token; ?>">　返回首页</a><br/>
</br>
</p>
</div>
</body>
</html>