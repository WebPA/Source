<?php
/**
 * Contact Us
 *
 * This is the form that the user fills in which is processed and emailed in the contact_send.php
 *
 * @copyright Loughborough University
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL version 3
 *
 * @link https://github.com/webpa/webpa
 */

require_once '../includes/inc_global.php';
require_once '../includes/functions/lib_form_functions.php';

check_user($_user);

// Begin Page

$UI->page_title = APP__NAME . ' Contact Support';
$UI->menu_selected = 'contact';
$UI->help_link = '?q=node/379#intool';
$UI->breadcrumbs = ['home' => '../', 'contact' => null];

$UI->head();
?>
<script language="JavaScript" type="text/javascript">
<!--

    function do_send() {
        document.contact_form.submit();
    }

//-->
</script>
<?php
$UI->body();

$UI->content_start();

?>
    <div class="content_box">
        <h2>We're sorry something is wrong. How can we help?</h2>

        <form action="contact_send.php" method="post" name="contact_form">
            <input type="hidden" name="contact_app" value="<?= $_config['app_id']; ?>">
            <div class="form_section">
                <table class="form" cellpadding="2" cellspacing="2">
                    <tr>
                        <td>
                            <label for="contact_name">Your name</label>
                        </td>
                        <td>
                            <input type="text" name="contact_name" id="contact_name" maxlength="60" size="50" value="<?= "{$_user->forename} {$_user->lastname}"; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="contact_username">Your username</label>
                        </td>
                        <td>
                            <input type="text" name="contact_username" id="contact_username" maxlength="15" size="10" value="<?= $_user->username; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="contact_email">Your email address</label>
                        </td>
                        <td>
                            <input type="text" name="contact_email" id="contact_email" maxlength="255" size="50" value="<?= $_user->email; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="contact_phone">Your phone number</label>
                        </td>
                        <td>
                            <input type="text" name="contact_phone" id="contact_phone" maxlength="25" size="20">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="subject">Subject</label>
                        </td>
                        <td>
                            <input type="text" name="subject" id="subject">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="description">Description</label>
                        </td>
                        <td>
                            <textarea name="description" id="description" cols="60" rows="6"></textarea>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="button_bar">
                <input type="reset" name="resetbutton" id="resetbutton" value="reset form">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <input type="button" name="sendbutton" id="sendbutton" value="send message" onclick="do_send()">
            </div>
        </form>
    </div>
<?php

$UI->content_end();

?>
