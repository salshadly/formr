<?php
/* require_once $_SERVER['DOCUMENT_ROOT']."/tmp/config/config.php"; */
require_once "../config/config.php";
global $currentUser;
if(!userIsAdmin() or !isset($_GET['id'])) {
  header("Location: index.php");
  die();
}
global $language,$available_languages,$lang;
$run=new Run;
$run->fillIn($_GET['id']);
if(!$run->status)
  header("Location: ../index.php");
if(!$currentUser->ownsRun($_GET['id']))
  header("Location: ../index.php");

if(!empty($_POST)) {
  $errors=array();
  if(isset($_POST['study'])) {
    $study=new Study;
    $study->fillIn($_POST['study']);
    if(!$study->status)
      $errors=$study->GetErrors();
    else {
      if(!$currentUser->ownsStudy($study->id)) 
        $errors=$study->GetErrors();
      else {
        if(isset($_POST['optional']))
          $run->addStudy($study,true);
        else 
          $run->addStudy($study);
        if(!$run->status)
          $errors=$run->GetErrors();
      }
    }
  }
}

?>
<?php
include("pre_content.php");
?>	
<p>
<h2><?php echo $run->name;?></h2>
</p>
  <p>
<?php

if($run->isEmpty()) { ?>

    Dieser Run enth&auml;lt noch keine Studien.                         
<?php 
                         } else {

  $run_data=$run->GetRunData();
  if($run_data) {
    foreach($run_data as $rd) {
      echo "<p>";
      if($rd[2]!=0) {
        if($rd[3]==1)
          echo "<p><h6>Optional - <img src='../images/pfeil.gif' alt='Pfeil' /> - <a href=change_option.php?id=$run->id&sid=$rd[0]&op=0>Mache verpflichtend</a></h6></p>";
        else 
          echo "<p><h6>Verpflichtend - <img src='../images/pfeil.gif' alt='Pfeil' /> - <a href=change_option.php?id=$run->id&sid=$rd[0]&op=1>Mache Optional</a></h6></p>";
      }
      echo "$rd[2]: <a href=view_study.php?id=$rd[0]>$rd[1]</a>";
      echo " - <a href=remove_study_from_run.php?id=$run->id&sid=$rd[0]>[x]</a>";
      echo "</p>";
    }
  }

}
?>
 </p>                    
<br>
<br>
<br>
<br>
<form id="add_study" name="add_study" method="post" action="view_run.php?id=<?php echo $run->id?>">
  <p>
  <p>
  <label>Studie
  </label>
<?php
$studies=$currentUser->GetStudies();
  if($studies) {
    echo "<select name='study'>";
    foreach($studies as $study) 
      echo "<option value=$study->id>$study->name</option>";
    echo "</select>";
  }
?>
  </p>
 <?php
  if(!$run->isEmpty()) {
?>
  <p>
  <label>Optional
  </label>
  <input type="checkbox" name="optional" id="optional"/>
  </p>
<?php
      }
?>
  <button type="submit">Studie hinzuf&uuml;gen</button>
  </form>




<br>
<p><a href="edit_run.php?id=<?php echo $run->id; ?>">Einstellungen</a></p>
<p><a href="acp.php">Zur&uuml;ck zum ACP</a></p>

<?php
include("post_content.php");
?>	