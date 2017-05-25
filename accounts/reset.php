<?php

/**
 *
 * reset.php - Password reset facility
 * @author Morgan Harris [morgan@snowproject.net]
 * @license http://www.gnu.org/licenses/gpl.txt
 *
 */

/* This page serves four functions, based on the $_POST['action'] variable.
If not set, the page simply displays a form confirming that student would like to reset their password.
  -This form sends the 'init' action to the page.
If set to 'init', the page generates a random hash, which is part of a link sent to the user's email.
  -This link is retrieved via GET, so there is no POSTDATA.
If not set, but $_GET['hash'] is set, the page displays the password reset form.
  -This form sends the 'reset' action to the page.
If set to 'reset', the page performs the password reset.

This script relies on the existence of the `user_reset_request` table in MySQL.
This table has the CREATE definition:
*/

require_once("../includes/inc_global.php");
require_once("../includes/classes/class_user.php");

$action = fetch_POST('action');

switch($action) {
  case "init":
    //phase 2
    //first, we create a random hash for this user. this doesn't need to be especially secure, so md5(rand()) will do fine.
    isset($_POST['username']) or die(gettext("Username not set."));
    $hash = md5(rand());
    $sql = 'SELECT user_id FROM ' . APP__DB_TABLE_PREFIX . "user WHERE username = '{$_POST['username']}' AND source_id = '' AND " .
           '(email IS NOT NULL) AND (email != \'\')';
    $uid = $DB->fetch_value($sql);
    if (!$uid) {
      $content = gettext("Unable to reset the password for this account.");
      break;
    }
    //inserts the user/hash pair into the database
    $sql = "INSERT INTO " . APP__DB_TABLE_PREFIX . "user_reset_request SET hash = '$hash', user_id = $uid";
    $appname = APP__NAME; $appwww = APP__WWW;
    $DB->execute($sql);
    $email = sprintf(gettext('You have requested for your password to be reset on %s. Please click or copy and paste the following link into your browser to continue the password reset process.

    %s/accounts/reset.php?u=%d&hash=%s

    If you have not requested a password reset, please ignore this email - your password will not be reset without further action.'), $appname, $appwww, $uid, $hash);

    //echo $email;
    $uemail = $DB->fetch_value("SELECT email FROM " . APP__DB_TABLE_PREFIX . "user WHERE user_id = $uid");
    mail($uemail,APP__NAME.' '.gettext("Password Reset"),$email,"From: " . $BRANDING['email.noreply']);
    $content = sprintf(gettext("An email has been sent to %s."), $uemail);
    break;
  case "reset":
    //phase 4
    $hash = $_POST['hash'];
    $uid = $_POST['uid'];
    $rslt = $DB->fetch_value("SELECT COUNT(*) FROM " . APP__DB_TABLE_PREFIX . "user_reset_request WHERE hash = '$hash' AND user_id = $uid");
    if ($rslt) {
      if ($_POST['newpass']==$_POST['confirmpass']) {
        $user = new User();
        $user_row = $DB->fetch_row("SELECT * FROM " . APP__DB_TABLE_PREFIX . "user WHERE user_id = $uid");
        $user->load_from_row($user_row);
        $user->set_dao_object($DB);
        $user->update_password(md5($_POST['newpass']));
        $user->save_user();
        $DB->execute("DELETE FROM " . APP__DB_TABLE_PREFIX . "user_reset_request WHERE user_id = $uid");
        $content = gettext('Your password has been reset.').'<a href="'.APP__WWW.'/login.php">'.gettext('Click here</a> to log in again.');
      } else {
        $content = gettext("The two passwords did not match.");
      }
    } else {
      $content = gettext("There was an error resetting this password.");
    }
    break;
  default:
    if (isset($_GET['hash'])) {
      //phase 3
      $hash = $_GET['hash'];
      $uid = $_GET['u'];
      if ((!isset($_GET['hash'])) || (!isset($_GET['u']))) {
        $content = gettext("Error: reset link incorrect. If you copied and pasted the link from your mail client, be sure you did so correctly.");
        break;
      }
      $rslt = $DB->fetch_value("SELECT COUNT(*) FROM " . APP__DB_TABLE_PREFIX . "user_reset_request WHERE hash = '$hash' AND user_id = $uid");
      if ($rslt) {
      $content = '<form action="reset.php" method="post">
        <table>
          <tr>
            <th scope="row">'.gettext('New Password').'</th>
            <td><input type="password" name="newpass" value="" id="newpass"/></td>
          </tr>
          <tr>
            <th scope="row">'.gettext('New Password (again)').'</th>
            <td><input type="password" name="confirmpass" value="" id="confirmpass"/></td>
          </tr>
          <tr>
            <td></td>
            <td><input type="submit" value="'.gettext('Reset Password').'" /></td>
          </tr>
        </table>
        <input type="hidden" name="hash" value="'.$hash.'"/>
        <input type="hidden" name="uid" value="'.$uid.'"/>
        <input type="hidden" name="action" value="reset" />
      </form>';
      } else {
        $content = gettext("There was an error resetting this password.<br/>Please contact the site administrator.");
      }
      break;
    }
    //phase 1
    //just display the form confirming the password reset.
    $content = '<strong>'.gettext('You are about to reset your password.<br/>').'</strong>'.gettext('In order to do so, a link will be sent to your student email account. This link will take you to a page that will enable you to reset your password.').'<br/>
<br/>
<form action="reset.php" method="post">
  <table>
    <tr>
      <th scope="row">'.gettext('Username').'</th>
      <td><input type="text" name="username" /></td>
    </tr>
    <tr>
      <td></td>
      <td>
        <input type="submit" value="'.gettext('Reset My Password').'"/>
      </td>
    </tr>
  </table>
  <input type="hidden" name="action" value="init"/>
</form>';
    break;
}

$UI->page_title = "Password Reset";
$UI->menu_selected = '';
$UI->breadcrumbs = array('login page' => '../', 'Password Reset' => null);
$UI->help_link = null;

$UI->head();
$UI->body();

$UI->content_start();
echo "<p>\n";
echo $content;
echo "</p>\n";
$UI->content_end(false);
?>
