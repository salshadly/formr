<?php
require_once '../../../define_root.php';
require_once INCLUDE_ROOT . "View/admin_header.php";

require_once INCLUDE_ROOT . "Model/EmailAccount.php";

$acc = new EmailAccount($fdb, $_GET['account_id'], $user->id);
if($user->created($acc)):
	alert("<strong>Error:</strong> Not your email account.",'alert-danger');
	redirect_to("/admin/mail/index");
endif;

if(!empty($_POST) AND isset($_POST['test_account'])) 
{
	$acc->test();
}
elseif(!empty($_POST)) 
{
	$acc->changeSettings($_POST);
	alert('<strong>Success!</strong> Your email account settings were changed!','alert-success');
	redirect_to("/admin/mail/edit?account_id=".$_GET['account_id']);
}
require_once INCLUDE_ROOT . "View/header.php";
require_once INCLUDE_ROOT . "View/acp_nav.php";
?>
<div class="col-md-6 col-lg-5 col-sm-7 col-md-offset-1 well">
	<h2>edit email account</h2>
<form class="form-horizontal form-horizontal-small-left"  id="edit_email_account" name="edit_email_account" method="post" action="<?=WEBROOT?>admin/mail/edit?account_id=<?=h($_GET['account_id'])?>">
	<div class="form-group">
		<label class="control-label" for="from">
			<?php echo _("From:"); ?>
		</label>
		<div class="controls">
			<input class="form-control" required type="text" placeholder="you@example.org" name="from" id="from" value="<?=$acc->account['from']; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label" for="from_name">
			<?php echo _("From (Name):"); ?>
		</label>
		<div class="controls">
			<input class="form-control" required type="text" placeholder="Your Name" name="from_name" id="from_name" value="<?=$acc->account['from_name']; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label" for="host">
			<?php echo _("SMTP Host:"); ?>
		</label>
		<div class="controls">
			<input class="form-control" required type="text" placeholder="ssl://smtp.gmail.com" name="host" id="host" value="<?=$acc->account['host']; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label" for="port">
			<?php echo _("Port:"); ?>
		</label>
		<div class="controls">
			<input class="form-control" required type="text" placeholder="467" name="port" id="port" value="<?=$acc->account['port']; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label" for="tls">
			<?php echo _("TLS:"); ?>
		</label>
		<div class="controls">
			<input type="hidden" name="tls" value="0">
			<input type="checkbox"name="tls" id="tls" value="1" <?= ($acc->account['tls']) ? 'checked':''; ?>>
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label" for="username">
			<?php echo _("Username:"); ?>
		</label>
		<div class="controls">
			<input class="form-control" required type="text" placeholder="you@example.org" name="username" id="username" value="<?=$acc->account['username']; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label" for="password">
			<?php echo _("Password:"); ?>
		</label>
		<div class="controls">
			<input class="form-control" required type="text" placeholder="you@example.org" name="password" id="password" value="<?=$acc->account['password']; ?>">
		</div>
	</div>
	
	<div class="form-group">
		<div class="controls">
			<input class="btn" required type="submit" value="<?php echo _("Save account"); ?>">
			<input class="btn" name='test_account' required type="submit" value="Test" title="Sends a test mail to a random mailinator address">
		</div>
	</div>
</form>
</div>
<?php
require_once INCLUDE_ROOT . "View/footer.php";
