<?php
/**
 * 
 * Contact Us
 * 
 * This is the form that the user fills in which is processed and emailed in the contact_send.php
 * 
 * @copyright 2007 Loughborough University
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version 0.9
 * 
 */

require_once("../includes/inc_global.php");
require_once('../includes/functions/lib_form_functions.php');

check_user($_user);

// Process GET/POST

$contact_type = fetch_GET('q');

// Get tutors to contact

$_module_id = fetch_SESSION('_module_id', null);
$type = APP__USER_TYPE_TUTOR;
$query = 'SELECT u.user_id, u.source_id, u.username AS id, u.lastname, u.forename, u.email, u.id_number AS `id number`, u.date_last_login AS `last login` FROM ' .
         APP__DB_TABLE_PREFIX . 'user u INNER JOIN ' . APP__DB_TABLE_PREFIX . 'user_module um ON u.user_id = um.user_id ' .
         "WHERE (um.module_id = {$_module_id}) AND (um.user_type = '{$type}') " .  // AND (source_id = '{$_source_id}')";
         'ORDER BY u.lastname, u.forename, u.source_id, u.username';
         
$rs = $DB->fetch($query);
if(!is_array($rs))
  $rs = array();

$fixed_contacts = array(
  array('user_id' => 0, 'forename' => 'All', 'lastname' => 'Tutors'),
  array('user_id' => -1, 'forename' => 'WebPA', 'lastname' => 'Helpdesk'),
);
$rs = array_merge($fixed_contacts, $rs);
// Begin Page
 
$UI->page_title = APP__NAME .' '.gettext('Contact');
$UI->menu_selected = gettext('contact');
$UI->help_link = '?q=node/379#intool';	
$UI->breadcrumbs = array	('home'		=> '../' ,
    gettext('contact')	=> null ,);


$UI->head();
?>
<script language="JavaScript" type="text/javascript">
<!--

	function do_send() {
		document.contact_form.submit();
	}// /do_send()

//-->
</script>
<?php
$UI->body();

$UI->content_start();

?>
	<p><?php echo gettext('If you want to report a problem or bug with any part of the WebPA system, have a technical query, or just need to ask a specific question regarding WebPA, please complete the form below.');?></p>
		
	<div class="content_box">
		<p><?php echo gettext('Please supply as much information with your message as possible (especially when sending a bug report!), this will allow us to respond to your message much faster!');?></p>
	
		<form action="contact_send.php" method="post" name="contact_form">
		<input type="hidden" name="contact_app" value="<?php echo($_config['app_id']); ?>" />
		
		<div class="form_section">
			<table class="form" cellpadding="2" cellspacing="2">
			<tr>
			        <td><label for="contact_person"><?php echo gettext('Contact Person');?></label></td>
			        <td>
                                    <select name="contact_person" id="contact_person">
                                      <?php foreach($rs as $usr){
                                      echo '<option value="'.$usr['user_id'].'">'.$usr['forename'].' '.$usr['lastname'].'</option>';
                                      } ?>
                                    </select>
                                </td>
                        </tr>
                        <tr>
				<td><label for="contact_name"><?php echo gettext('Your Name');?></label></td>
				<td><input type="text" name="contact_name" id="contact_name" maxlength="60" size="50" value="<?php echo("{$_user->forename} {$_user->lastname}"); ?>" /></td>
			</tr>
			<tr>
				<td><label for="contact_username"><?php echo gettext('Your Username');?></label></td>
				<td><input type="text" name="contact_username" id="contact_username" maxlength="15" size="10" value="<?php echo($_user->username); ?>" /></td>
			</tr>
			<tr>
				<td><label for="contact_email"><?php echo gettext('Your Email');?></label></td>
				<td><input type="text" name="contact_email" id="contact_email" maxlength="255" size="50" value="<?php echo($_user->email); ?>" /></td>
			</tr>
			<tr>
				<td><label for="contact_phone"><?php echo gettext('Your Phone Number');?></label></td>
				<td><input type="text" name="contact_phone" id="contact_phone" maxlength="25" size="20" value="" /></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td><label for="contact_type"><?php echo gettext('Type of Message');?></label></td>
				<td>
					<select name="contact_type" id="contact_type">
						<?php
							$contact_types = array	('help'		=> gettext('Request for help!') ,
													 'info'		=> gettext('Information request') ,
													 'bug'		=> gettext('Bug / Error report') ,
													 'wish'		=> gettext('Suggestion / Wish List') ,
													 'misc'		=> gettext('Other type of message') ,);
																			
							render_options($contact_types, $contact_type);
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="contact_message"><?php echo gettext('Message Text');?></label></td>
				<td><textarea name="contact_message" id="contact_message" cols="60" rows="6"></textarea></td>
			</tr>
			</table>
		</div>
		
		<div class="button_bar">
			<input type="reset" name="resetbutton" id="resetbutton" value="<?php echo gettext('reset form');?>" />
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
			<input type="button" name="sendbutton" id="sendbutton" value="<?php echo gettext('send message');?>" onclick="do_send()" />
		</div>
		</form>
	</div>
<?php

$UI->content_end();

?>