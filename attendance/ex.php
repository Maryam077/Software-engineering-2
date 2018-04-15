<?php

include_once('../../config.php');

include_once "DB_manager.php";
$sp = DB_manager::getInstance();;

$id = $_POST['uid'];
$now = time();
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
$sp->insert("mdl_attendance_excuse", $record);
$teacherid = $sp->teacher();

foreach ($teacherid as $key => $val) {
    $message = new stdClass();
    $message->component = 'mod_quiz'; //your component name
    $message->name = 'submission'; //this is the message name from messages.php
    $message->userfrom = $id;
    $message->userto = $val;
    $message->subject = 'Student';
    $message->fullmessage = 'A student wants an attendance excuse';
    $message->fullmessageformat = FORMAT_PLAIN;
    $message->fullmessagehtml = '<html><p><h4>' . $_POST['excuse'] . '</h4></p><br><p>' . $_POST['excusedes'] . '</p><br><Button value="accept" style="background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;">Accept</Button>
    
    <Button value="reject" style="background-color: #ff0000; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;">Reject</Button></html>';
    $message->smallmessage = '';
    $message->notification = 1; //this is only set to 0 for personal messages between users
    $msgid = message_send($message);
    //var_dump($msgid);
    $ss = array("messageid" => $msgid, "isread" => 0);
    $sp->insert('mdl_message_popup', $ss);
}
?>