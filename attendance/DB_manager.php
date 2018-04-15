<?php
class DB_manager{
    //put your code here
    
    private $host="localhost";
    private $username="moodle-owner";
    private $password="modle123$%";
    //private static $instance;// single database object i will explain it next section 
    private $db_name="moodle";//your database name 
    private $mysqli; // 
    private $c;
    private static $instance;


private function __construct()
    {
      $this->mysqli = $this->database_connect($this->host, $this->username,
      $this->password);
      $this->database_select($this->db_name);
      
    }
    public function getInstance(){
        if(self::$instance == NULL ){
            self::$instance  = new DB_manager();
        }
        return self::$instance ;
    }

    private function database_connect($database_host, $database_username, $database_password) 
    {
        
        if ($this->c = new mysqli($database_host, $database_username, $database_password)) {
            return $this->c;
            
        } else {
            
              
                die("Database connection error");
            
        }
    }

    public function get_row($query) 
    {
        if (!strstr(strtoupper($query), "LIMIT"))
            $query .= " LIMIT 0,1";
        if (!($res =$this->database_query($query))) {
         die( "Database error: " . mysqli_error($this->mysqli) . "<br/>In query: " . $query);
        }
        return mysqli_fetch_assoc($res);
    }
    
   public function insert($table, $data)
    {

        $q="INSERT INTO `$table` ";
        $v=""; $n="";

        foreach($data as $key=>$val)
        {
            $n.="`$key`, ";
            if(strtolower($val)=='null') $v.="NULL, ";
            elseif(strtolower($val)=='now()') $v.="NOW(), ";
            else $v.= "'$val', ";
        }

        $q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v,', ') .");";

        if($this->database_query($q))

        {
            return mysqli_insert_id($this->mysqli);

        }
        else return false;
    }
     
    private function database_select($database_name)
    {
        return $this->mysqli->select_db($database_name)
            or die("no db is selecteted");
    }
    
        
    public function database_close() 
    {
        if(!mysqli_close($this->database_connection))
            die ("Connection close failed.");
           
    }
 
    public function database_query($database_query) 
    {
       
       if( $query_result = $this->mysqli->query($database_query))
        return $query_result;
    }
   
   
    public function database_all_array($database_result) 
    {
        $array_return=array();
        while ($row = mysqli_fetch_array($database_result)) {
            $array_return[] = $row;
        }
//      if(count($array_return)>0)
        return $array_return;


    }
            /**
     * Executes query result (table, array of array)
     *
     * @param string database_result
 
     * @access public
     * @return associated array of rows 
     */
   public function database_all_assoc($database_result) 
    {
       $array_return = array();
       
        while ($row = mysqli_fetch_assoc($database_result)) {
            $array_return[] = $row;
        }
       
        return $array_return;
    }
    
    public   function database_affected_rows($database_result) 
    {

        return mysqli_affected_rows($database_result);
    }
    
    public   function database_num_rows($database_result)
    {
       return mysqli_num_rows($database_result);
    }
    
#-#############################################
# desc: does an update query with an array
# param: table, assoc array with data (not escaped), where condition (optional. if none given, all records updated)
# returns: (query_id) for fetching results etc
 public function update($table,$data,$data2){
        $q = "update `" . $table . "` set ";
            foreach($data as $key=>$val)
        {
          if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
            elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
            else $q.= "`$key`='".$val."', ";
        }
        $n="";$v="";
       foreach ($data2 as $key => $value)
                    {
                       $v .=" $key = '$value' AND "; 
                    }
         $q = rtrim($q, ', ') . ' WHERE '. rtrim($v, ' AND ').";";
        return $this->c->query($q);
          
        
     
}
         public function delete($table, $data){
                   $q = " Delete from $table  where ";
        
                  foreach ($data as $key => $value) 
                 {
            
                $q .="$key = '$value' and  "; 
            
                  }
        
                 $q = trim($q, ' and ' );
                 return $this->c->query($q);
    
                   }
          
             public function select($table, $col, $data){
                    $q  = "SELECT $col FROM $table WHERE ";
                     foreach ($data as $key => $value)
                    {
                       $q .=" $key = '$value' AND "; 
                    }
                    
                 $q = rtrim($q, ' AND ');
                  $r = $this->c->query($q);
                 
                    return $r;
                   }
    public function select_allcond($table , $data){
 $q = "SELECT * FROM $table WHERE ";
                foreach ($data as $key => $value)
                    {
                       $q .=" $key = '$value' AND "; 
                    }
                    
                 $q = rtrim($q, ' AND ');
    $r = $this->c->query($q);
return $r;
    }
    public function select_allord($table ,$data,$type){
 $q = "SELECT * FROM $table ORDER BY $data $type ;";
        
    $r = $this->c->query($q);
return $r;
    }
       public function select_all($table){
 $q = "SELECT * FROM $table";
   $r = $this->c->query($q);
return $r;

    }
 public function teacher(){
 $q = "SELECT DISTINCT u.id
from mdl_attendance_excuse w
INNER JOIN mdl_attendance_sessions z ON z.id = w.sessionid
INNER JOIN mdl_attendance y ON y.id = z.attendanceid
INNER JOIN mdl_enrol e on y.course = e.courseid
INNER JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
INNER JOIN mdl_user u ON u.id = ue.userid
INNER JOIN mdl_role_assignments us ON us.userid = u.id
Inner join mdl_role t ON t.id=us.roleid
WHERE t.id = 3 OR t.id = 4";
   $r = $this->c->query($q);
return mysqli_fetch_assoc($r);

    }
 public function execused_before($user_id,$session_id){

        $q = "SELECT sessionid,studentid FROM mdl_attendance_excuse WHERE studentid =".$user_id." and sessionid=".$session_id."";
        $r = $this->c->query($q);
        return mysqli_fetch_assoc($r);        
        
    }

/*SELECT COUNT(l.statusset) from mdl_attendance a
INNER JOIN mdl_attendance_sessions s ON a.id = s.attendanceid
INNER JOIN mdl_attendance_log l ON s.id = l.sessionid 
where a.id=2 and l.studentid=4 and l.statusid=2;*/

}
?>
