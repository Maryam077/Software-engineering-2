<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NewFunctions
 *
 * @author marya
 */
include_once('../../config.php');

include_once "DB_manager.php";

class NewFunctions {

    //put your code here
    public function __construct() {
        
    }

    public static function notify_absent_student($DB, $userid) {
        $query = "SELECT count(statusid) FROM {attendance_log} 
    INNER JOIN {attendance_statuses} ON {attendance_log}.statusid = {attendance_statuses}.id
    WHERE {attendance_statuses}.description = 'Absent'  AND  studentid = '" . $userid . "'";
        $s = $DB->get_records_sql($query);
        foreach ($s as $key => $val) {

            if ($key == '2' || $key > '2') {
                $message = new stdClass();
                $message->component = 'mod_quiz'; //your component name
                $message->name = 'submission'; //this is the message name from messages.php
                $message->userfrom = $userid;
                $message->userto = $userid;
                $message->subject = 'warning';
                $message->fullmessage = 'you absent 2 times or more so please Take care';
                $message->fullmessageformat = FORMAT_PLAIN;
                $message->fullmessagehtml = '';
                $message->smallmessage = '';
                $message->notification = 1; //this is only set to 0 for personal messages between users
                $msgid = message_send($message);
                //$DB->insert_record('message_popup', $msgid);
                $record = new StdClass();
                $record->messageid = $msgid;
                $record->isread = 0;

                $DB->insert_record('message_popup', $record);
            }
        }
    }

    public static function notification_absent_students($DB, $user_id) {

        $query = "SELECT count(statusid) FROM {attendance_log} 
INNER JOIN {attendance_statuses} ON {attendance_log}.statusid = {attendance_statuses}.id
WHERE {attendance_statuses}.description = 'Absent'  AND  studentid = '" . $user_id . "'";
        $s = $DB->get_records_sql($query);
        foreach ($s as $key => $val) {
            if ($key == '2' || $key > '2') {
                echo \core\notification::add("you absent 2 times or more so please Take care", "Warning");
            }
        }
    }

    public static function notify_teacher_with_absence($record) {
        $id = $_POST['uid'];
        $now = time();
        $sp = DB_manager::getInstance();
      $ss=  $sp->insert("mdl_attendance_excuse", $record);
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
            $message->fullmessagehtml = '<html><p><h4>'.$_POST['excuse'].'</h4></p><br><p>'.$_POST['excusedes'].'</p><br>
<form action="regist.php" method="POST">
 <input type="hidden" name="excuseid" value="'.$ss.'">
<Button name="accept" value="accept" style="background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;">Accept</Button>
    
    <Button name="reject" value="reject" style="background-color: #ff0000; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;">Reject</Button>
</form>
    </html>';
            $message->smallmessage = '';
            $message->notification = 1; //this is only set to 0 for personal messages between users
            $msgid = message_send($message);
            //var_dump($msgid);
            $ss = array("messageid" => $msgid, "isread" => 0);
            $sp->insert('mdl_message_popup', $ss);
        }
    }

}
