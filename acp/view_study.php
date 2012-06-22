<?php
/* require_once $_SERVER['DOCUMENT_ROOT']."/tmp/config/config.php"; */
require_once "../config/config.php";
global $currentUser;
if(!userIsAdmin() or !isset($_GET['id'])) {
  header("Location: index.php");
  die();
}
global $language,$available_languages,$lang;
$study=new Study;
$study->fillIn($_GET['id']);
if(!$study->status)
  header("Location: ../index.php");
if(!$currentUser->ownsStudy($_GET['id']))
  header("Location: ../index.php");
?>
<?php
include("pre_content.php");
?>	
<p>
<h2><?php echo $study->name;?></h2>
</p>
<p><a href="../admin/index.php?study_id=<?php echo $study->id; ?>">Admin Bereich</a></p>
<p><a href="edit_study.php?id=<?php echo $study->id; ?>">Einstellungen</a></p>
<p><a href="edit_study_mails.php?id=<?php echo $study->id; ?>">E-Mail Benachrichtigungen</a></p>



<br>
<p><a href="acp.php">Zur&uuml;ck zum ACP</a></p>

<?php
include("post_content.php");
?>	