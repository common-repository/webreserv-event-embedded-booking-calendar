<?php

/*
Plugin Name: WebReserv EVENT Embedded Booking Calendar
Plugin URI: http://blog.webreserv.eu/webreserv-booking-plugins-for-wordpress/
Description: The WebReserv EVENT Embedded Booking Calendar plugin lets you embed the WebReserv EVENT Booking Calendar directly in any PAGE or POST. The installation includes the code for a demo account so you can see how it works without a WebReserv account. Works for nearly any type of business. Remember to create a free WebReserv account to try it out with your bookable product. 
Version: 0.9 BETA
Author: WebReserv	
Author URI: http://blog.webreserv.eu/webreserv-booking-plugins-for-wordpress/
*/

// Set The Path to the plugin
define('WEBRESERVPATH', get_option('siteurl').'/wp-content/plugins/webreserv-event-embedded-booking-calendar');
// Set Installed flags and priveliges
$event_webreserv_installed = true;
$event_webreserv_privileges = 0;

// Define the default iFrame
$event_wriframe = "<iframe src=http://www.webreserv.eu/bookingcalendar.do?businessid=demogymes width=700px height=500px scrolling=auto frameborder=0></iframe>";

// Initialize
add_action('init', 'event_webreserv_calendar_init');
add_action('widgets_init', 'event_widget_init_webreserv');
add_filter('the_content','event_webreserv_insert');




// Insertion Function for POSTs and PAGEs
function event_webreserv_insert($event_content)
{
  if (preg_match('{EVENT_WEBRESERV}',$event_content))
    {
    $event_content = str_replace('{EVENT_WEBRESERV}',event_webreserv(),$event_content);
    }
  return $event_content;
}

function event_webreserv()
{
   global  $event_userdata, $table_prefix, $wpdb, $event_webreserv_installed;
   get_currentuserinfo();
   $event_str='';
  if( !event_webreserv_calendar_installed() )

		$event_webreserv_installed = event_webreserv_calendar_install();

    if( !$event_webreserv_installed )

    {

		echo "Plugin not installed correctly";

		return;

	}

	    $event_query = "

			SELECT code AS code

			FROM ".$table_prefix."event_webreserv_calendar	LIMIT 1		";

		//echo $event_query;

		$code = $wpdb->get_var( $event_query );

		

	//}

	?>

	

	<?php

    $event_str.='<div class="wrap">';

	if( $code === null )

	{

		$event_str.= '<h4>You don\'t have the event webreserv Calendar, please set code in Settings menu.</h4>';

	}

	else

	{

		

				

		$event_str.='<center>';

		

		$event_str.='<div id="event_CalendarDiv">';

		?><?php

		 $event_str.= $code;

		  ?>

		<?php 

		$event_str.='</div>';

		 

		 

		$event_str.='</center>';

	 

	}

	?>

	<?php

    $event_str.='</div>';

	

	return $event_str;

	

	

}



function event_widget_init_webreserv() {

  if (!function_exists('event_register_sidebar_widget'))

  	return;

 // register_sidebar_widget('WebReserv2','widget_calendar_webreserv');

}



function event_widget_calendar_webreserv() {

	echo "Event webreserv";

	}



function event_webreserv_calendar_init()

{		

	global $event_webreserv_privileges, $table_prefix, $wpdb, $event_webreserv_path, $event_webreserv_default, $event_webreserv_installed;

 	add_action('admin_menu', 'event_webreserv_calendar_config_page');

}



function event_webreserv_calendar_config_page() 

{



	if ( function_exists('add_submenu_page') )

	{

		add_menu_page('webreserv EVENT Calendar', 'WebReserv EVENT Booking Calendar', 8, __FILE__, 'event_webreserv_calendar_main_page');

		//add_submenu_page(__FILE__, 'Settings', 'Settings', $event_webreserv_privileges, 'maintenance', 'event_webreserv_calendar_manage_page');

		//add_submenu_page(__FILE__, 'Admin Settings', 'Admin Settings', 8, 'admin_maitenance', 'event_webreserv_calendar_admin_manage_page');

	}

}



function event_webreserv_calendar_main_page()

{

	global $event_webreserv_default, $event_userdata, $table_prefix, $wpdb, $event_webreserv_installed, $event_wriframe;

    get_currentuserinfo();

    

    if( !event_webreserv_calendar_installed() )

		$event_webreserv_installed = event_webreserv_calendar_install();

	

    if( !$event_webreserv_installed )

    {

		echo "PLUGIN NOT CORRECTLY INSTALLED, PLEASE CHECK ALL INSTALL PROCEDURE!";

		return;

	}

	?>

	<div class="wrap">

	<?php

	$valid = true;



	$event_queryS = "select * from ".$table_prefix."event_webreserv_calendar limit 1";


	$event_d1 = $wpdb->get_var( $event_queryS );


	if( $event_d1 === null )

		{

			$event_query ="

				INSERT INTO ".$table_prefix."event_webreserv_calendar (code)

				VALUES ('". $event_wriframe ."')

			";

			$wpdb->query( $event_query );

		}

	else

		{

			$event_query = "SELECT code AS code FROM ".$table_prefix."event_webreserv_calendar	LIMIT 1";

			$event_wriframe = $wpdb->get_var( $event_query );

		}

	

		

	if( isset($_POST["set"]) AND $_POST["set"] == "SAVE" )

	{

			

		if( !event_webreserv_calendar_code( $_POST["code"] ) )

			$event_valid = false;

		else

		

		{

			$event_query ="Update ".$table_prefix."event_webreserv_calendar set code = '".$_POST["code"]."'";
			// where calendar_id = " & $event_d1 ->calendar_id;



			$wpdb->query( $event_query );

			$event_wriframe = str_replace("\\", "", ($_POST["code"]));

		}

	}

	

	if( isset( $_GET["ui"]) and $_GET["ui"] == "true" )

	{

		$event_query = "

			DROP TABLE ".$table_prefix."event_webreserv_calendar

		";

		mysql_query( $event_query ) or die( mysql_error() );

		

		delete_option( 'event_webreserv_calendar_privileges' ); //Removing option from database...

		

		$event_installed = event_webreserv_calendar_installed();

		

		if( !$event_installed ) {

			echo "PLUGIN UNINSTALLED. NOW DE-ACTIVATE PLUGIN.<br />";

			echo " <a href=plugins.php>CLICK HERE</a>";

			return;

			}

		else

		{

			echo "PROBLEMS WITH UNINSTALL FUNCTION.";

		}

			

	}

	?>



	<div style="margin-bottom:20px;"><h2>WebReserv EVENT Embedded Booking Calendar</h2></div>

	<div>

	<div style="float:left;">
	<br>
	<b>Back-Office Screenshots</b><br>
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=navigation_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/navigation_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
     	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=bookings_overview_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/bookings_overview_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=bookings_overview_2_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/bookings_overview_2_small.PNG" border="0" /></a><br>
	<br>
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=reports_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/reports_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
     	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=widget_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/widget_1_small.PNG" border="0" /></a>&nbsp;&nbsp;
	<a href="http://tasks.webreserv.eu/webreserv_screenshots/show_large_screenshot.html?image=help_1_large.PNG" target="_blank"><img src="<?php echo WEBRESERVPATH; ?>/help_1_small.PNG" border="0" /></a>


	<br />


	<span style="float:left;width:400px;padding-left:0px;">
<hr>
<strong>Sign Up for a WebReserv Account</strong><br>
<p style="font-size:10px;">Create a FREE account on either WebReserv .EU or .COM</p>
<b>Sign Up for a WebReserv.EU Account</b><br>
<p style="font-size:10px;">If your business is located in Europe (Not just EU, but any country in Europe), then you can sign up for a WebReserv.EU account.<br>
<a href ="http://www.webreserv.eu/signup.do" target="_new">Click here to create a <b>.EU</b> account</a></p>
<b>Sign Up for a WebReserv.COM Account</b><br>
<p style="font-size:10px;">If your business is located in any other country in the world, then you can sign up for a WebReserv.COM account.<br>
<a href ="http://www.webreserv.com/signup.do" target="_new">Click here to create a <b>.COM</b> account</a></p>

	<br />

	</span>
</div>	

	

	<div style="float:left;width:400px;padding-left:20px;" >

    <form action="<?php echo $_SERVER["PHP_SELF"]."?page=".$_GET["page"]; ?>" method="POST">

	<b>Steps to Set-up the Component</b><br><br>
 	<b>1 - Enter your WebReserv Calendar Code</b><br />
<p style="font-size:10px;">Paste the code in for your WebReserv Account.<br>
	<a href="http://blog.webreserv.eu/webreserv-event-embedded-booking-calendar/" Target="_new">How to create the WebReserv code for your Business.</a><br>
	<i>Remember to press SAVE.</i>
        <textarea type ="text" name="code" rows="7" cols="60"><?php echo $event_wriframe ?></textarea></p>
        <input type="submit" name="set" value="SAVE" /></form><br>
	<b>2 - Add the {EVENT_WEBRESERV} code. </b><br />
<p style="font-size:10px;">Now insert the code <b>{EVENT_WEBRESERV}</b> (In UPPER CASE and including the parenthesis) on any POST or PAGE.<br>
	Remember you can set the height and width in the code above.</p>	
<hr>
	<b>Now use the Back Office to Manage Bookings</b><br />
<p style="font-size:10px;">Your Wordpress website is now configured with the WebReserv Booking Component.<br>
	Log into the WebReserv Back Office to :<br>
	 - Configure Events<br>
	 - Set-up rates<br>
	 - Manage Bookings (Both online and manual)<br>
	 - See Reports<br>
	 - Etc<br> 
	  <br /></p>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href ="http://www.webreserv.eu/login.do" target="_blank" class="button">&nbsp;&nbsp;Log Into WebReserv.EU Back Office&nbsp;&nbsp;&nbsp;&nbsp;	</a><br><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href ="http://www.webreserv.com/login.do" target="_blank" class="button">&nbsp;&nbsp;Log Into WebReserv.COM Back Office&nbsp;&nbsp;</a>


	<p><br />


	  <br />

	  <br />

	

	</p>

	</div>

	</div>

	<div>

	<div style="clear:both"></div>

	

	<?php



}



function event_webreserv_calendar_code( $code )

{

	if( strpos($code, "<iframe") === FALSE )

		return false;

	else

		return true;

}



function event_wr_get_admin_url()

{

 global $event_wriframe;

 $event_adminURL = preg_match("/http:\/\/(.*).com/", $event_wriframe, $event_matches);

 if ($event_adminURL = true)

 {

 $event_adminURL = htmlentities($event_matches['0']);

 $event_adminURL = $event_adminURL .'/admin';

 }

 return $event_adminURL;

}





function event_webreserv_calendar_installed()

{

	global $table_prefix, $wpdb;

	

	$event_query = "	 
			SHOW TABLES LIKE '".$table_prefix."event_webreserv_calendar' 
			";
	$event_install = $wpdb->get_var( $event_query );
	

	if( $event_install === NULL )

		return false;

	else

		return true;

}



function event_webreserv_calendar_install()

{

	global $table_prefix, $wpdb;


	$event_query = "

		CREATE TABLE ".$table_prefix."event_webreserv_calendar (

			calendar_id INT(11) NOT NULL auto_increment,

			code TEXT NOT NULL,

			PRIMARY KEY( calendar_id )

		)

	";

	$wpdb->query( $event_query );



	//Using option for webreserv calendar plugin!

	add_option( "event_webreserv_calendar_privileges", "2" );

	

	if( !event_webreserv_calendar_installed() )

		return false;

	else

		return true;

}







?>
