<?php
require_once '../../../define_root.php';
require_once INCLUDE_ROOT . "View/admin_header.php";
require_once INCLUDE_ROOT . "View/header.php";
require_once INCLUDE_ROOT . "View/acp_nav.php";
?>
<div class="row">
	<div class="col-md-12">
		<h2>log of user activity in this run</h2>
<?php
$g_users = $fdb->prepare("SELECT 
	`survey_run_sessions`.session,
	`survey_unit_sessions`.id AS session_id,
	`survey_runs`.name AS run_name,
	`survey_run_units`.position,
	`survey_units`.type AS unit_type,
	`survey_unit_sessions`.created,
	`survey_unit_sessions`.ended,
	`survey_users`.email
	
	
FROM `survey_unit_sessions`

LEFT JOIN `survey_run_sessions`
ON `survey_run_sessions`.id = `survey_unit_sessions`.run_session_id
LEFT JOIN `survey_users`
ON `survey_users`.id = `survey_run_sessions`.user_id
LEFT JOIN `survey_units`
ON `survey_unit_sessions`.unit_id = `survey_units`.id
LEFT JOIN `survey_run_units`
ON `survey_unit_sessions`.unit_id = `survey_run_units`.unit_id
LEFT JOIN `survey_runs`
ON `survey_runs`.id = `survey_run_units`.run_id
WHERE `survey_runs`.name = :run_name
ORDER BY `survey_run_sessions`.id DESC,`survey_unit_sessions`.id ASC;");
$g_users->bindParam(':run_name',$run->name);
$g_users->execute();

$users = array();
while($userx = $g_users->fetch(PDO::FETCH_ASSOC))
{
	$userx['Unit in Run'] = $userx['unit_type']. " <span class='hastooltip' title='position in run {$userx['run_name']} '>({$userx['position']})</span>";
	$userx['Email'] = "<small title=\"{$userx['session']}\">{$userx['email']}</small>";
	$userx['entered'] = "<small>{$userx['created']}</small>";
	$userx['left'] = "<small>{$userx['ended']}</small>";
	if($userx['unit_type']!= 'Survey') $userx['delete'] = "<a onclick='return confirm(\"Are you sure you want to delete this unit session?\")' href='".WEBROOT."admin/run/{$userx['run_name']}/delete_unit_session?session_id={$userx['session_id']}' class='hastooltip' title='Delete this waypoint'><i class='fa-remove'></i></a>";
	else $userx['Delete'] =  "<a onclick='return confirm(\"You shouldnt delete survey sessions, you might delete data! REALLY sure?\")' href='".WEBROOT."admin/run/{$userx['run_name']}/delete_unit_session?session_id={$userx['session_id']}' class='hastooltip' title='Survey sessions should not be deleted'><i class='fa fa-times'></i></a>";
	
	unset($userx['session']);
	unset($userx['run_name']);
	unset($userx['unit_type']);
	unset($userx['created']);
	unset($userx['ended']);
	unset($userx['position']);
	unset($userx['email']);
#	$user['body'] = "<small title=\"{$user['body']}\">". substr($user['body'],0,50). "…</small>";
	
	$users[] = $userx;
}
if(!empty($users)) {
	?>
	<table class='table'>
		<thead><tr>
	<?php
	foreach(current($users) AS $field => $value):
	    echo "<th>{$field}</th>";
	endforeach;
	?>
		</tr></thead>
	<tbody>
		<?php
		$last_user = '';
		$tr_class = '';
		
		// printing table rows
		foreach($users AS $row):
			if($row['Email']!==$last_user):
				$tr_class = ($tr_class=='') ? 'alternate' : '';
				$last_user = $row['Email'];
			endif;
			echo '<tr class="'.$tr_class.'">';

		    // $row is array... foreach( .. ) puts every element
		    // of $row to $cell variable
		    foreach($row as $cell):
		        echo "<td>$cell</td>";
			endforeach;

		    echo "</tr>\n";
		endforeach;
	}
		?>

	</tbody></table>
	</div>
</div>

<?php
require_once INCLUDE_ROOT . "View/footer.php";