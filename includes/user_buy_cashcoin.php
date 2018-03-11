<?php

/**
 * Game Control Panel v2
 * Copyright (c) www.intrepid-web.net
 * 
 * Purchase Cash Coin With Vote Points
 * Addon by Nuna (c) RF-DEV, 2018
 *
 * The use of this product is subject to a license agreement
 * which can be found at http://www.intrepid-web.net/rf-game-cp-v2/license-agreement/
 */

if (!defined('COMMON_INITIATED')) {
    die("Hacking attempt! Logged");
}

if( !empty($setmodules) ) 
{
    $file = basename(__FILE__);
    $module["Account"]["Purchase Cash Coin"] = $file;
}
else
{
    $lefttitle = "Purchase Cash Coin";
    $time = date("F j Y G:i");
    if( $this_script == $script_name ) 
    {
        if( $isuser == true ) 
        {
            $page = isset($_POST["page"]) ? $_POST["page"] : "";
            $exit_1 = false;
            $exit_buy = false;
            $currency = "";
            if( !isset($config["specialcs_costpoint"])) 
            {
                $out .= "Configuration values cannot be found, unable to enable this script.";
                return 0;
            }

            $premium_exchange_rate = ceil($config["specialcs_costpoint"]);
            if( $config["specialcs_convertenable"] == 0) 
            {
                $out .= "<p style=\"text-align: center; font-weight: bold;\">This feature has been disabled by the admins</p>";
                return 0;
            }

            if( empty($page) ) 
            {
                $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"], 2) . "</span> Vote Points</p>" . "\n";
                connectdatadb();
                $user_sql = "SELECT id = CAST(L.id AS varchar(255)), pass = CAST(L.Password AS varchar(255)), B.Cash FROM RF_User.dbo.tbl_rfaccount AS L INNER JOIN BILLING.dbo.tbl_UserStatus AS B ON L.id = B.id WHERE B.id = '" . $userdata["username"] . "'";
                if( !($user_result = @mssql_query($user_sql)) ) 
                {
                    $exit_1 = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to get your characters data</p>" . "\n";
                    if( $is_superadmin == true ) 
                    {
                        $out .= "<p>" . "\n";
                        $out .= "SQL: " . $user_sql . "<br/>" . "\n";
                        $out .= "MSSQL ERROR: " . mssql_get_last_message() . "\n";
                        $out .= "" . "\n";
                        $out .= "</p>" . "\n";
                    }

                }

				$out .= "<p><b>Cash Coin exchange rate:</b>" . "\n";
				$out .= "<form method=\"post\">" . "\n";
				$out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"30%\">" . "\n";
				$out .= "\t<tr>" . "\n";
				$out .= "\t\t<td class=\"alt2\" valign=\"top\">Amount:</td>" . "\n";
				$out .= "\t\t<td class=\"alt1\" valign=\"top\" colspan=\"2\">" . antiject($config["specialcs_cashpoint"]) . " Cash Coin</td>" . "\n";
				$out .= "\t</tr>" . "\n";
				$out .= "\t<tr>" . "\n";
				$out .= "\t\t<td class=\"alt2\" valign=\"top\">Cost:</td>" . "\n";
				$out .= "\t\t<td class=\"alt1\" valign=\"top\" colspan=\"2\">" . number_format($premium_exchange_rate) . " Vote Points</td>" . "\n";
				$out .= "\t</tr>" . "\n";
				$out .= "\t<tr>" . "\n";
				if( $userdata["points"] < $config["specialcs_cashpoint"])
				{	
					$out .= "\t\t<td class=\"alt2\" valign=\"top\" colspan=\"3\"><input type=\"hidden\" name=\"page\" value=\"buy\"/><input type=\"hidden\" value=\"" . $userdata["username"] . "\"/><input type=\"submit\" class=\"btn btn-default disabled\" value=\"You dont have enough VP!\"/></td>" . "\n";
				}
				else
				{	
					$out .= "\t\t<td class=\"alt2\" valign=\"top\" colspan=\"3\"><input type=\"hidden\" name=\"page\" value=\"buy\"/><input type=\"hidden\" name=\"char_serial\" value=\"" . $userdata["username"] . "\"/><input type=\"submit\" name=\"submit\"  class=\"btn btn-default\" value=\"Buy Now!\"/></td>" . "\n";
				}
				$out .= "\t</tr>" . "\n";
				$out .= "</table>" . "\n";
				$out .= "</form>" . "\n";
                
                if( mssql_num_rows($user_result) <= 0 ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You do not have any characters</p>" . "\n";
                }

                mssql_free_result($user_result);
                return 1;
            }

            if( $page == "buy" ) 
            {
                $out .= "<p style=\"font-weight: bold; font-size: 15px; text-align: center;\">Your account currently has <span style=\"color: #8F92E8;\">" . number_format($userdata["points"], 2) . "</span> Vote Points</p>" . "\n";
                $char_serial = isset($_POST["username"]) && is_int((int) $_POST["username"]) ? antiject((int) $_POST["username"]) : 0;
                $exchange_premium = isset($_POST["exchange_premium"]) && is_int((int) $_POST["exchange_premium"]) ? antiject((int) $_POST["exchange_premium"]) : 0;
                $t_login = $userdata3["lastlogintime"];
				$t_logout = $userdata3["lastlogofftime"];
				$t_cur = time( );
				//$t_maxlogin = $t_login + 3600;
				 if ( $t_login <= $t_logout )
                {
                    $status = "offline";
                }
                else
                {
                    $status = "online";
                }
                if ( $status == "online" )
                {
                    $exit_buy = 1;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You cannot buy items when logged into the game!<br/>If you have logged out and yet see this message, log back in and properly log out again (click the log out button!).</p>"."\n";
                }
                
                if( $userdata["points"] < $config["specialcs_cashpoint"])
                {
                    $exit_buy = true;
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">You need atleast " . $config["specialcs_cashpoint"] . " vote points to make this exchange </p>";
                }

                $premium = floor(1 * $premium_exchange_rate);                

                #mssql_free_result($user_result);
                if( $exit_buy == false ) 
                {
                    $update_id = "" . "UPDATE BILLING.dbo.tbl_UserStatus SET Cash = Cash+".$config["specialcs_cashpoint"]." WHERE id = '" . $userdata["username"] . "'";
                    if( !($update_result = @mssql_query($update_id)) ) 
                    {
                        $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error while trying to update your account</p>";
                        $out .= "<p><b>Debug:</b><br/><i>SQL: </i>" . $update_id . "<br/><i>SQL Return:</i> " . mssql_get_last_message() . "</p>";
                        return 1;
                    }

                    $subtract = $config["specialcs_costpoint"];
                    $update_points = "" . "UPDATE gamecp_gamepoints SET user_points = user_points-" . $subtract . " WHERE user_account_id = '" . $userdata["serial"] . "'";
                    if( !($update_p_result = @mssql_query($update_points)) ) 
                    {
                        gamecp_log(1, $userdata["username"], "" . "GAMECP - CONVERT POINTS - Failed to update Game Points: -" . $subtract, 1);
                    }

                    $out .= "<p style=\"text-align: center; font-weight: bold;\">Successfully exchanged: <u>" . $config["specialcs_costpoint"] . "</u> Vote points into <u>" . $config["specialcs_cashpoint"] . "</u> Cash Coin";
                    gamecp_log(1, $userdata["username"], "" . "GAMECP - CONVERT POINTS - Char Serial: " . $char_serial . " | Exchanged: " . $config["specialcs_costpoint"] . " VP to " . $config["specialcs_cashpoint"] . " Cash Coin");
                    return 1;
                }

            }
            else
            {
                $out .= $lang["invalid_page_id"];
                return 1;
            }

        }
        else
        {
            $out .= $lang["no_permission"];
            return 1;
        }

    }
    else
    {
        $out .= $lang["invalid_page_load"];
    }

}


