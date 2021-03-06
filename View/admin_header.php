<?php
require_once INCLUDE_ROOT . "Model/Site.php";

if(!$user->loggedIn() AND !$user->isAdmin()) {
	alert("<strong>Sorry:</strong> Only admins have access.",'alert-info');
	redirect_to("index");
}
if(isset($_GET['study_name'])):
	require_once INCLUDE_ROOT . "Model/Study.php";

	$study = new Study($fdb,null,array('name' => $_GET['study_name']));

	if(!$study->valid):
		alert("<strong>Error:</strong> Study broken.",'alert-danger');
		redirect_to("/index");
	elseif(!$user->created($study)):
		alert("<strong>Error:</strong> Not your study.",'alert-danger');
		redirect_to("/index");
	endif;
elseif(isset($_GET['run_name'])):
	require_once INCLUDE_ROOT . "Model/Run.php";
	$run = new Run($fdb, $_GET['run_name']);
	
	if(!$run->valid):
		alert("<strong>Error:</strong> Run broken.",'alert-danger');
		redirect_to("/index");
	elseif(!$user->created($run)):
		alert("<strong>Error:</strong> Not your run.",'alert-danger');
		redirect_to("/index");
	endif;
endif;

$css = (isset($css)?$css:'') . '<link rel="stylesheet" href="'.WEBROOT.'assets/admin.css" type="text/css" media="screen">';
