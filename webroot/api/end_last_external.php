<?php
require_once '../../define_root.php';
require_once INCLUDE_ROOT . "View/api_header.php";

if(isset($_POST['session'])):
	$run_session = new RunSession($fdb, $run->id, null, $_POST['session']);

	if($run_session->session !== NULL)
		$run_session->endLastExternal();
	else
		alert('<strong>Error.</strong> Invalid session token.','alert-danger');
	
endif;
echo $site->renderAlerts();
