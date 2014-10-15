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

$LoginInfo = mysql_fetch_row(mysql_query("SELECT `Username`, `Money`, `LastLoginStamp` FROM `users` WHERE (`Loginkey` = '" . $token . "' and `Rights` >= 1)",$con));

if ($LoginInfo == false) {
	goto loginlabel;
}
else {
	goto endlabel;
}

loginlabel:
$jump = "<meta http-equiv=\"refresh\" content=\"0; url=" . dirname("http://" . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]) . "/login.php\">";

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
<script language="JavaScript" type="text/javascript">
var city=[["Name","Author","Description","Identifier"],["Name","Identifier"],["Name"]]; 
function getCity(){
	var sltProvince=document.form_search.type;
	var sltCity=document.form_search.t;   
	var provinceCity=city[sltProvince.selectedIndex - 1];
	sltCity.length=1;  
	for(var i=0;i<provinceCity.length;i++){   
		sltCity[i+1]=new Option(provinceCity[i],i+1);
	}
}
</script>
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
<div class="module-title"><p>　82Flex - 搜索</p></div>
<div class="module-content">
<FORM METHOD="GET" action="list.php" name="form_search"></br>
<p class="tips">　查询内容：</br>
　<input name="s" class="ipt-longtxt" maxlength="128" ></input>
</p>
<p class="tips">
　<SELECT NAME="type" onChange="getCity()" class="ipt-txt">
<OPTION VALUE="0">请选择查询表名</OPTION>
<OPTION VALUE="search_patches">patches</OPTION>
<OPTION VALUE="search_applist">applist</OPTION>
<OPTION VALUE="search_users">users</OPTION>
</SELECT></br>
　<SELECT NAME="t" class="ipt-txt">
<OPTION VALUE="0">请选择查询字段名</OPTION>
</SELECT></br>
　<input type="submit" />
<input type="hidden" name="token" value="<?php echo $token; ?>" />
</FORM>
</br></p>
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