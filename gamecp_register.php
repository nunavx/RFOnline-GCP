<?php

/*
	# Intrepid GameCP
	# Add-on: Register Script with Cash Coin + Premium Services.
	# New column for register new ID (Pin).
	# Add new column in tbl_rfaccount (Pin) first for working script.
	# You should add new config in DB: "specialreg_regisprem", "specialreg_regispoint"
	#
	# Add-on Credits (c) Nuna, Nuna at RF-DEV.
*/


define("IN_GAMECP_SALT58585", true);
include("./gamecp_common.php");
$notuser = true;
$isuser = false;
$exit_stage1 = false;
$exit_stage2 = false;
$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$re_password = isset($_POST["re_password"]) ? $_POST["re_password"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";

# Did you see right here ? this is new Pin that you need
$pin = isset($_POST["pin"]) ? $_POST["pin"] : "";

$submit = isset($_POST["submit"]) ? $_POST["submit"] : "";
$ip = isset($_SERVER["REMOTE_ADDR"]) ? gethostbyname($_SERVER["REMOTE_ADDR"]) : "";
$exit_form = false;

# Uh.... so many account you already registered ?
if( !$config["security_max_accounts"] ) 
{
    $config["security_max_accounts"] = 3;
}

#include("./includes/main/votenumberformat.php");
$lefttitle = "Register";
$title = $program_name . " - " . $lefttitle;
$navbits = array( $script_name => $program_name, "" => $lefttitle );
$out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
$out .= "\t<tr>" . "\n";
$out .= "\t\t<td class=\"alt1\">" . "\n";
$out .= "\t\t\t<p style=\"padding: 2px;\">Welcome to the user registration page.<br/>";
$out .= "<br/>No e-mail confirmation is required. ";
$out .= "<br/>Username and passwords are case-sensitive</p>" . "\n";
$out .= "\t\t</td>" . "\n";
$out .= "\t</tr>" . "\n";
$out .= "</table>" . "\n";
if( $submit != "" ) 
{
	# i'm handsome right ?
    if( $username == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to fill in a username</p>";
    }
    else
    {
        if( eregi("[^a-zA-Z0-9]", $username) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Invalid username entered. Letters and numbers only!</p>";
        }
        else
        {
            if( strlen($username) < 4 || 12 < strlen($username) ) 
            {
                $exit_stage1 = true;
                $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Username must be greater than 4 and less than 12 characters long</p>";
            }

        }

    }

	# What are you looking for ?
    if( $password == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to fill in your password</p>";
    }
    else
    {
        if( eregi("[^a-zA-Z0-9]", $password) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Invalid password entered. Letters and numbers only!</p>";
        }
        else
        {
            if( strlen($password) < 4 || 16 < strlen($password) ) 
            {
                $exit_stage1 = true;
                $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Password must be greater than 4 and less than 16 characters long</p>";
            }

        }

    }

    if( $password != "" && $re_password == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to re-type your password</p>";
    }

	# Don't forget to fill your email :D
    if( $email == "" ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You forgot to fill in an E-Mail address</p>";
    }
    else
    {
        if( !isemail($email) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You have entered an invalid E-Mail address</p>";
        }

    }

	# Password must same !
    if( $password != "" && $re_password != "" && $password != $re_password ) 
    {
        $exit_stage1 = true;
        $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Your two passwords do not match. Please re-type them.</p>";
    }
    else
    {
        if( eregi("[^a-zA-Z0-9_-]", $password) ) 
        {
            $exit_stage1 = true;
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Invalid password. Letters and numbers only, both required!</p>";
        }

    }

    if( $exit_stage1 != true ) 
    {
        $username = antiject(trim($username));
        $password = antiject(trim($password));
        $email = antiject(trim($email));
        connectuserdb();
        $username_check = mssql_query("SELECT id FROM tbl_rfaccount WHERE id=CONVERT(binary,'" . $username . "')");
        if( 0 < mssql_num_rows($username_check) ) 
        {
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Sorry, the username you choose has already been taken.</p>";
            $exit_stage2 = true;
        }

		# Oh.... you're using same email many times :(		
        $email_check = mssql_query("SELECT Email FROM tbl_rfaccount WHERE Email='" . $email . "'");
        if( 0 < mssql_num_rows($email_check) ) 
        {
            $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">Sorry, your e-mail address has already been used.</p>";
            $exit_stage2 = true;
        }
		
		# set max account for IP
        if( 0 < $config["security_max_accounts"] ) 
        {
            $maxip_check = mssql_query("SELECT createip FROM tbl_UserAccount WHERE createip='" . $ip . "' OR lastconnectip='" . $ip . "'") or exit( "wtf did not work" );
            if( $config["security_max_accounts"] <= mssql_num_rows($maxip_check) ) 
            {
                $out .= "<p style=\"color: red; font-weight: bold; text-align: center;\">You cannot register more than " . $config["security_max_accounts"] . " accounts per ip.</p>";
                $exit_stage2 = true;
            }

        }

        if( $exit_stage2 == false ) 
        {
			# Yipieeeee..... your account has been created
            $register_query = "INSERT INTO tbl_rfaccount(id,password,email,pin) VALUES ((CONVERT (binary,'" . $username . "')),(CONVERT (binary,'" . $password . "')),'" . $email . "','" . $pin . "')";
            if( !($register_query = mssql_query($register_query)) ) 
            {
                $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error inserting data into the database</p>";
                if( $config["security_enable_debug"] == 1 ) 
                {
                    $out .= "<p>DEBUG(?):<br/>" . "\n";
                    $out .= mssql_get_last_message();
                    $out .= "</p>";
                }

            }
            else
				
			# Insert User Status for new ID, it required to Login
            {
                $insert_sql = "" . "INSERT INTO tbl_UserAccount (id, createip) VALUES(convert(binary,'" . $username . "'), '" . $ip . "')";
                if( !($insert_result = mssql_query($insert_sql)) ) 
                {
                    $out .= "<p style=\"text-align: center; font-weight: bold;\">SQL Error inserting data into the User database</p>";
                    if( $config["security_enable_debug"] == 1 ) 
                    {
                        $out .= "<p>DEBUG(?):<br/>" . "\n";
                        $out .= mssql_get_last_message();
                        $out .= "</p>";
                    }

                }
                else
                {
                    $timenow = time();
                    game_cp_1($username, $timenow, $_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"]);
                    @mssql_free_result($register_query);
                    $out .= "<p style=\"font-weight: bold; text-align: center; padding: 2px;\" class=\"alt2\">Successfully registered a new account!</p>";
                    $exit_form = true;
                }

            }
			
			# Should we add Free Cash Coin and Premium Services ???..... do it here
			{
                $insert_cash = "" . "INSERT INTO BILLING.dbo.tbl_UserStatus (id,Status,DTStartPrem,DTEndPrem,cash) VALUES ('" . $username . "', '2',(CONVERT(datetime,GETDATE())), (CONVERT(datetime,GETDATE()+" . $config["specialreg_regisprem"] . ")), '" . $config["specialreg_regispoint"] . "')";
                if( !($insert_result = mssql_query($insert_cash)) ) 
                {
                    $out .= "";
                    if( $config["security_enable_debug"] == 1 ) 
                    {
                        $out .= "<p>DEBUG(?):<br/>" . "\n";
                        $out .= mssql_get_last_message();
                        $out .= "</p>";
                    }

                }
                else
                {
                    $timenow = time();
                    game_cp_1($username, $timenow, $_SERVER["REMOTE_ADDR"], $_SERVER["HTTP_USER_AGENT"]);
                    @mssql_free_result($register_query);
                    $out .= "";
                    $exit_form = true;
                }

            }

        }

        @mssql_free_result($username_check);
    }

}

if( $exit_form != true ) 
{
	# Here your Registration Form!
    $out .= "<form method=\"post\" action=\"gamecp_register.php\">";
    $out .= "<input type=\"hidden\" name=\"regstatus\" value=\"done\">";
    $out .= "<table class=\"tborder\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">" . "\n";
    $out .= "<tr>";
    $out .= "<label>Username:</label>";
    $out .= "<input type=\"text\" autocomplete=\"off\" class=\"form-control\" placeholder=\"Username length must be between 4 to 12 characters only.\" name=\"username\" size=\"12\" maxlength=\"16\" value=\"" . $username . "\">";
    $out .= "</tr>";
    $out .= "<tr>";
    $out .= "<br><label>Email address:</label>";
    $out .= "<input type=\"text\" autocomplete=\"off\" class=\"form-control\" placeholder=\"Please make sure you enter a valid and working Email.\" size=\"12\" maxlength=\"50\" name=\"email\" value=\"" . $email . "\">";
    $out .= "</tr>";
    $out .= "<tr>";
	$out .= "<br><label>Confirm E-mail address:</label>";
    $out .= "<input type=\"text\" autocomplete=\"off\" class=\"form-control\" placeholder=\"Re-type your E-Mail address.\" size=\"12\" maxlength=\"50\" name=\"re_email\">";
    $out .= "</tr>";
    $out .= "<tr>";
    $out .= "<br><label>Password:</label>";
    $out .= "<input type=\"password\" autocomplete=\"off\" class=\"form-control\" placeholder=\"Password length must be between 4 to 12 characters only.\" size=\"12\" maxlength=\"16\" name=\"password\" value=\"" . $password . "\">";
    $out .= "</tr>";
    $out .= "<tr>";
    $out .= "<br><label>Confirm Password:</label>";
    $out .= "<input type=\"password\" autocomplete=\"off\" class=\"form-control\" placeholder=\"Re-type your Password.\" size=\"12\" maxlength=\"16\" name=\"re_password\">";
    $out .= "</tr>";
    $out .= "<tr>";
	$out .= "<tr>";
    $out .= "<br><label>Pin:</label>";
    $out .= "<input type=\"text\" autocomplete=\"off\" class=\"form-control\" placeholder=\"PIN must be only numbers. REMEMBER YOUR PIN!\" size=\"12\" pattern=\"[0-9]{6}\" maxlength=\"6\" name=\"pin\" value=\"" . $pin . "\">";
    $out .= "</tr>";
    $out .= "\t<br><td class=\"alt1\" align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"submit\" class=\"btn btn-primary\" value=\"Register\">&nbsp;<input type=\"reset\" class=\"btn btn-primary\" value=\"Reset\"></td>";
    $out .= "</tr>";
    $out .= "</table>";
}

gamecp_nav();
eval("print_outputs(\"" . gamecp_template("gamecp") . "\");");

