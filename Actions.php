<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./login.php");
    }
    function save_user(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' ".($id > 0 ? " and user_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `user_list` {$data}";
            }else{
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New User successfully saved.';
                else
                $resp['msg'] = 'User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `user_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_parcel_type(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = addslashes(trim($v));
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `parcel_type_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `parcel_type_list` set {$data} where parcel_type_id = '{$id}'";
        }
        @$check= $this->query("SELECT COUNT(parcel_type_id) as count from `parcel_type_list` where `name` = '{$name}' ".($id > 0 ? " and parcel_type_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Parcel Type already exists.';
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Parcel Type successfully saved.";
                else
                    $resp['msg'] = "Parcel Type successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Parcel Type Failed.";
                else
                    $resp['msg'] = "Updating Parcel Type Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_parcel_type(){
        extract($_POST);
        $count = $this->query("SELECT COUNT(parcel_id) FROM `parcel_list` where parcel_type_id = '{$id}'")->fetchArray()[0];
        $has_data = false;
        $has_data = $count > 0 ? true : false;
        if($has_data){
            $resp['status']='failed';
            $resp['msg']='Parcel Type cannot be deleted because this has a connected data in the parcel list.';
        }else{
            @$delete = $this->query("DELETE FROM `parcel_type_list` where parcel_type_id = '{$id}'");
            if($delete){
                $resp['status']='success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Parcel Type successfully deleted.';
            }else{
                $resp['status']='failed';
                $resp['error']=$this->lastErrorMsg();
            }
        }
        
        return json_encode($resp);
    }
    function save_carrier(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = addslashes(trim($v));
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `carrier_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `carrier_list` set {$data} where carrier_id = '{$id}'";
        }
        @$save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id))
                $resp['msg'] = "Carrier/Driver successfully saved.";
            else
                $resp['msg'] = "Carrier/Driver successfully updated.";
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Carrier/Driver Failed.";
            else
                $resp['msg'] = "Updating Carrier/Driver Failed.";
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function delete_carrier(){
        extract($_POST);
        $count = $this->query("SELECT COUNT(delivery_id) FROM `delivery_list` where carrier_id = '{$id}'")->fetchArray()[0];
        $has_data = false;
        $has_data = $count > 0 ? true : false;
        if($has_data){
            $resp['status']='failed';
            $resp['msg']='Carrier/Driver cannot be deleted because this has a connected data in the parcel list.';
        }else{
            @$delete = $this->query("DELETE FROM `carrier_list` where carrier_id = '{$id}'");
            if($delete){
                $resp['status']='success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Carrier/Driver successfully deleted.';
            }else{
                $resp['status']='failed';
                $resp['error']=$this->lastErrorMsg();
            }
        }
        
        return json_encode($resp);
    }
    function save_parcel(){
        extract($_POST);
        $now = new DateTime("now", new DateTimeZone(tZone));
        $_POST['date_updated'] = $now->format('Y-m-d H:i:s');
        if(empty($id))
        $_POST['date_added'] = $now->format('Y-m-d H:i:s');
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                if(!is_numeric($v))
                $v = $this->escapeString($v);
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        if(empty($id)){
            $sql = "INSERT INTO `parcel_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `parcel_list` set {$data} where parcel_id = '{$id}'";
        }
        @$check= $this->query("SELECT COUNT(parcel_id) as count from `parcel_list` where `code` = '{$code}' ".($id > 0 ? " and parcel_id != '{$id}'" : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] ='failed';
            $resp['msg'] = 'Parcel Tracking Code already exists.';
        }else{
            if(!empty($id))
            @$get = $this->query("SELECT * FROM `parcel_list` where parcel_id = '{$id}' ")->fetchArray();
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Parcel successfully saved.";
                else
                    $resp['msg'] = "Parcel successfully updated.";
                $pid= !empty($id) ? $id : $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
                if(empty($pid)){
                    $track = $this->save_track($pid,"Parcel added in the list.");
                }else{
                    if(isset($get['code']) && $get['code'] != $code) {
                        $track = $this->save_track($pid,"Parcel [{$get['code']}] has updated into [{$code}] and its details.");
                    }else{
                        $track = $this->save_track($pid,"Parcel [{$code}] details has been updated.");
                    }
                }
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Parcel Failed.";
                else
                    $resp['msg'] = "Updating Parcel Failed.";
                $resp['error']=$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_parcel(){
        extract($_POST);
        $count = $this->query("SELECT COUNT(delivery_id) FROM `delivery_list` where parcel_id = '{$id}'")->fetchArray()[0];
        $has_data = false;
        $has_data = $count > 0 ? true : false;
        if($has_data){
            $resp['status']='failed';
            $resp['msg']='Parcel cannot be deleted because this has a connected data in the parcel list.';
        }else{
            @$delete = $this->query("DELETE FROM `parcel_list` where parcel_id = '{$id}'");
            if($delete){
                $resp['status']='success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Parcel successfully deleted.';
            }else{
                $resp['status']='failed';
                $resp['error']=$this->lastErrorMsg();
            }
        }
        
        return json_encode($resp);
    }
    function update_parcel_status(){
        extract($_POST);
        @$get =$this->query("SELECT * FROM `parcel_list` where parcel_id='{$id}'")->fetchArray();
        if(!$get){
            $resp['status']="failed";
            $resp['msg']="Unknown Parcel ID.";
        }else{
            $sql = "UPDATE `parcel_list` set status = '{$status}' where parcel_id='{$id}'";
            $save= $this->query($sql);
            if($save){
                if($status == 1){
                    $delivery = $this->save_delivery($get['parcel_id'],$carrier_id,0);
                    $cname = $this->query("SELECT * FROM `carrier_list` where carrier_id = '{$carrier_id}'")->fetchArray()['name'];
                    $description = "Parcel [{$get['code']}] is out for deliver. Carrier/DR Name: ".$cname;
                }else if($status == 2){
                    $this->query("UPDATE `delivery_list` set status = 1 where parcel_id = '{$id}'");
                    $description = "Parcel [{$get['code']}] was delivered successfully.";
                }else if($status == 3){
                    $this->query("UPDATE `delivery_list` set status = 1 where parcel_id = '{$id}'");
                    $description = "Parcel has failed to deliverd.\n\rReason: {$remarks}";
                }else{
                    $description = "Parcel has failed to deliverd.";
                }
                $track = $this->save_track($id,$description);
                $resp['status'] = "success";
                $_SESSION['flashdata']['type'] = "success";
                $_SESSION['flashdata']['msg'] = "Parcel Status was updated successfully";
            }else{
                $resp['status'] = "failed";
                $_SESSION['flashdata']['msg'] = "Failed to update data. Error: ".$this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function save_track($parcel_id='',$description=''){
        if(!empty($parcel_id) && !empty($description)){
            $sql = "INSERT INTO `parcel_tracks` (`parcel_id`,`description`,`date_added`)VALUES('{$parcel_id}','{$description}','".date('Y-m-d H:i:s')."')";
            $save = $this->query($sql);
            if($save){
                return true;
            }else{
                return false;
            }
        }
    }
    function save_delivery($parcel_id='',$carrier_id='',$status=0,$delivery_id=null){
        if(!empty($parcel_id) && !empty($carrier_id)){
            if($deliver_id = null){
                $sql = "INSERT INTO `delivery_list` (`parcel_id`,`carrier_id`,`status`,`date_added`)VALUES('{$parcel_id}','{$carrier_id}','{$status}','".date('Y-m-d H:i:s')."')";
            }else{
                $sql = "UPDATE`delivery_list` set  `parcel_id`='{$parcel_id}',`carrier_id`='{$carrier_id}',`status`='{$status}' where delivery_id = '{$delivery_id}'";
            }
            $save = $this->query($sql);
            if($save){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'c_login':
        echo $action->c_login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'c_logout':
        echo $action->c_logout();
    break;
    case 'save_user':
        echo $action->save_user();
    break;
    case 'delete_user':
        echo $action->delete_user();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_parcel_type':
        echo $action->save_parcel_type();
    break;
    case 'delete_parcel_type':
        echo $action->delete_parcel_type();
    break;
    case 'save_carrier':
        echo $action->save_carrier();
    break;
    case 'delete_carrier':
        echo $action->delete_carrier();
    break;
    case 'save_parcel':
        echo $action->save_parcel();
    break;
    case 'delete_parcel':
        echo $action->delete_parcel();
    break;
    case 'update_parcel_status':
        echo $action->update_parcel_status();
    break;
    default:
    // default action here
    break;
}