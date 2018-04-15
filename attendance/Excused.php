<?php

require_once('../../config.php');
require_once('classes/functions.php');
session_start();
$s  = $_SESSION['fromform'] ;
  $id=  $_SESSION['id'];


  $now=time();
$functions = new NewFunctions();
if(isset($_POST['submit'])){
   // echo 'TEST';
     $record = array(
            "studentid" => $id,
            "statusid" => $_POST['status'],
            "statusset" => "ay hagaa",
            "sessionid" => $_POST['session'],
            "timetaken" => $now,
            "takenby" => $id,
            "excuse" => $_POST['excuse'],
            "excusedesc" => $_POST['excusedes']
        );
    $functions->notify_teacher_with_absence($record);
    redirect('Excuse_sent.php');
}

$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('standard');
$PAGE->set_title("User Manual");
$PAGE->set_heading("User Manual");
$PAGE->set_url($CFG->wwwroot . '/about.php');
echo $OUTPUT->header();


?>


<form action='' method='POST'>
  Excuse: <br>
  <input type="text" name="excuse"><br>
  Excuse Description:<br>
  <input type="text" name="excusedes">
    <input type="hidden" name="fromform" value="">
      <input type="hidden" name="uid" value="<?php echo$id?>">
 <input type="hidden" name="status" value="<?php echo$s->status?>">
 <input type="hidden" name="session" value="<?php echo$s->sessid?>">
 <input type="submit" value="Submit" name="submit">

</form>


<?php
echo $OUTPUT->footer();?>