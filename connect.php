<?php
	/*
		Flex API Service 1.905
		Author: i_82 (i.82@me.com)
		Copyright © 2006-2013 WEIPHONE TECHNOLOGY TEAM.
		All rights reserved. 
	*/
	
	/* General Config */
	
	// The amount of the ranking items.
	// $G_RankNum = 50; (C)
	$G_RankNum = 50;
	
	// Write units to files
	// Enable => true
	// Disable => false (C)
	$G_Units = false;
	
	// Import units files to database
	// Enable => true
	// Disable => false (C)
	$G_Import = true;
	
	
	// Register Account Check
	// Enable => -1 (C)
	// Disable => 0
	$G_RegCheck = 0;
	
	// If the current time on the server can not correspond to your users, you should edit it.
	// $G_TimeZone = 0; (C)
	$G_TimeZone = 0;
	
	// PV Counts: Replaced by %counts%
	// Enable and show => 2
	// Enable => 1
	// Disable => 0
	$G_Counts = 2;
	
	// Tips: Replaced by %tips%
	$G_Tips = "[公告：\\n欢迎使用 Flex 2 中文云端！\\n威锋网原创教程：\\nhttp://bbs.weiphone.com/read-htm-tid-6723698.html\\n祝大家元旦快乐～\\n交流群：72221818]\\n[用量：%counts%]";
	
	/* Addition */
	/*
		Purchase Check Function Switch
		You should run Purchase.sql on MYSQL BEFORE using this function.
		Enable => true
		Disable => false (C)
	*/
	$F_Purchase = false;
	$F_Purchase_URL = "cydia://package/com.fuyuchi.flex";
	
	/*
		Set MYSQL Server
		You should run Main.sql on MYSQL BEFORE using this server.
		Depends: PHP >= 4.3.0 (Allow Safe Mode)
		RECOMMEND FREE SERVER PRODUCER: CLANMARK.COM
	*/
	$server = "localhost";
	$username = "flex";
	$password = "";
	$database = "test";
?>