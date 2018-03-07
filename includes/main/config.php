<?php
//////////////////////////////////////////////////
//	Game CP: RF Online Game Control Panel		//
//	Module: config.php							//
//	Copyright (C) www.AaronDM.com				//
//////////////////////////////////////////////////

if(!defined("IN_GAMECP_SALT58585")) {
	die("Hacking Attempt");
	exit;
	return;
}

# Administrative Options
$admin = array();
$admin['super_admin'] = 'Admin,teste';

# Get our list of possible ban reasons
$ban_reasons = array('Multiple Account Voting','Duper','PayPal Related','Insulting Players','Fraud','3 Day Ban','Not obeying a GM','Crashing the server','Fraud Related','Multiple Account Warning','Misuse of Chat/Spamming','Miuse of Chat/Spamming more than Once','Glitching Guard Towers','Guard Towers in Core','Guard Tower "Port In" Location','Abusing Race Leader Powers','Safe Zone Debuffing/Healing','Animus Safe Zone Attacking','Glitching Animus','Disrespecting a GM','Scamming','Verbally Harassing a Player','Harassing a Play','Speed Hacking','Damage Hacking','Fly Hacking','Terrain Exploiting','Settlement/Wharf Tower Exploiting','Multiple use of Third Party Programs','Shooting Over Crag Mine Barricades','Multiple Rates Jades','Impersonating a GM','Using a Nuke in the Core','Real Money Trading(RMT)','Partying with a Cheater','PayPal Restrictions Bypass','Conspiracy','Aiding a hacker',"Account trading or sharing", "Auto-chat abuse", "Spamming in chat", "Trading in public chat",'Non-English in public chat','Contact a GM regarding BAN','Piloting Accounts','TEMP');
sort($ban_reasons);
$reasons_count = @count($ban_reasons);

# Configurable Variables [DON'T TOUCH IF YOU DONT KNOW WHATS GOING ON!]
$dont_allow = array(".","..","index.html","pagination","library","libchart","gamecp_license.txt","generated");

# Database Settings (BE ADVISED: MAKE NEW USERNAMES AND PASSWORDS FOR THE GAMECP, DO NOT USE YOUR MASTER)
$mssql = array();
$mssql['user']['host'] = 'mbak-siti';
$mssql['user']['db'] = 'RF_USER';
$mssql['user']['username'] = 'sa';
$mssql['user']['password'] = 'wanker12';

$mssql['data']['host'] = 'mbak-siti';
$mssql['data']['db'] = 'RF_World';
$mssql['data']['username'] = 'sa'; // If user has only 'read' access
$mssql['data']['password'] = 'wanker12'; // Item Edit and Delete characters wont work

$mssql['gamecp']['host'] = 'mbak-siti';
$mssql['gamecp']['db'] = 'RF_GameCP';
$mssql['gamecp']['username'] = 'sa';
$mssql['gamecp']['password'] = 'wanker12';

$mssql['items']['host'] = 'mbak-siti';
$mssql['items']['db'] = 'RF_ItemsDB';
$mssql['items']['username'] = 'sa';
$mssql['items']['password'] = 'wanker12';

?>