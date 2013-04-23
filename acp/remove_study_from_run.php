<?php
require_once "../config/config.php";
global $currentUser;
if(!userIsAdmin() or !isset($_GET['id']) or !isset($_GET['sid']) or !isset($_GET['pos'])) {
  header("Location: index.php");
  die();
}
$run=new Run;
$run->fillIn($_GET['id']);
if(!$run->status)
  header("Location: ../index.php");
if(!$currentUser->ownsRun($_GET['id']))
  header("Location: ../index.php");
$study=new Study;
$study->fillIn($_GET['sid']);
if(!$study->status)
  header("Location: ../index.php");
if(!$currentUser->ownsStudy($_GET['sid']))
  header("Location: ../index.php");
if(!is_numeric($_GET['pos']))
  header("Location: ../index.php");
if($res=$run->removeStudy($_GET['sid'],$_GET['pos'])) {
  header("Location: view_run.php?id=".$_GET['id']); //todo: user success message 
  die();
}
header("Location: ../index.php");
?>