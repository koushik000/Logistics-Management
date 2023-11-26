<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT p.*,pt.name as `type` FROM `parcel_list` p inner join parcel_type_list pt on p.parcel_type_id = pt.parcel_type_id where p.parcel_id = '{$_GET['id']}'");
    @$res = $qry->fetchArray();
    
    if($res){
        foreach($res as $k => $v){
            $$k = $v;
        }
    }else{
        $_SESSION['flashdata']['type'] = "danger";
        $_SESSION['flashdata']['msg'] = "Unknown Parcel ID";
        echo "<script>location.replace('./?page=parcels')</script>";
    }
}else{
    $_SESSION['flashdata']['type'] = "danger";
    $_SESSION['flashdata']['msg'] = "Unknown Parcel ID";
    echo "<script>location.replace('./?page=parcels')</script>";
}
?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Parcel: <?php echo $code ?></h5>
        </div>
        <div class="card-body">
            <div class="col-md-12 text-end">
                <div class="btn btn-info btn-sm rounded-0 text-light" type="button" id="edit_data"><i class="fa fa-edit"></i> Edit Details</div>
                <div class="btn btn-primary btn-sm rounded-0" type="button" id="update_status">Update Status</div>
                <div class="btn btn-danger btn-sm rounded-0" type="button" id="delete_data"><i class="fa fa-trash"></i> Delete</div>
            </div>
            <fieldset class="shadow rounded-0 border-start border-success border-3 p-3 bsides-light">
                <legend class="text-info">Parcel Information</legend>
                <div class="row">
                    <div class="col-md-6">
                        <dl>
                            <dt class="text-info">Tracking Code:</dt>
                            <dd class="ps-2"><?php echo $code ?></dd>
                            <dt class="text-info">Parcel Type:</dt>
                            <dd class="ps-2"><?php echo $type ?></dd>
                            <dt class="text-info">Sender Name:</dt>
                            <dd class="ps-2"><?php echo $sender_name ?></dd>
                            <dt class="text-info">Contact:</dt>
                            <dd class="ps-2"><?php echo $sender_contact ?></dd>
                            <dt class="text-info">Address:</dt>
                            <dd class="ps-2"><?php echo $sender_address ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl>
                            <dt class="text-info">Receiver Name:</dt>
                            <dd class="ps-2"><?php echo $receiver_name ?></dd>
                            <dt class="text-info">Contact:</dt>
                            <dd class="ps-2"><?php echo  $receiver_contact ?></dd>
                            <dt class="text-info">Address:</dt>
                            <dd class="ps-2"><?php echo  $receiver_address ?></dd>
                            <dt class="text-info">Remarks:</dt>
                            <dd class="ps-2"><?php echo $remarks ?></dd>
                            <dt class="text-info">Status:</dt>
                            <dd class="ps-2">
                                <?php 
                                    switch($status){
                                        case '0':
                                            echo '<span class="badge bg-dark text-light rounded-pill">Pending</span>';
                                            break;
                                        case '1':
                                            echo '<span class="badge bg-primary rounded-pill">Out for Delivery</span>';
                                            break;
                                        case '2':
                                            echo '<span class="badge bg-success rounded-pill">Delivered</span>';
                                            break;
                                        case '3':
                                            echo '<span class="badge bg-danger rounded-pill">Delivery Faield</span>';
                                            break;
                                    }
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </fieldset>
            <hr>
            <fieldset>
                <legend class="text-info">Tracks</legend>
                <div class="row row-cols-sm-1 row-cols-md-3 row-cols-xl-4 gx-3 gy-2">
                <?php 
                $tracks = $conn->query("SELECT * FROM `parcel_tracks` where parcel_id = '{$parcel_id}'");
                while($row = $tracks->fetchArray()):
                ?>
                    <div class="col">
                        <div class="shadow bg-white border-start border-primary border-2 h-100 p-2">
                            <div><?php echo $row['description'] ?></div>
                            <div class="text-end text-muted"><small><?php echo date("M d, Y H:i",strtotime($row['date_added'])) ?></small></div>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#update_status').click(function(){
            uni_modal('Update Parcel\'s Status',"update_status.php?id=<?php echo $parcel_id ?>")
        })
        $('#edit_data').click(function(){
            uni_modal('Edit Parcel Details',"manage_parcel.php?id=<?php echo $parcel_id ?>","large")
        })
        $('#delete_data').click(function(){
            _conf("Are you sure to delete <b><?php echo $code ?></b> from parcel list?",'delete_data',['<?php echo $parcel_id ?>'])
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./Actions.php?a=delete_parcel',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.replace('./?page=parcels')
                }else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>')
                    el.addClass('alert alert-danger pop-msg')
                    el.text(resp.msg)
                    el.hide()
                    $('#confirm_modal .modal-body').prepend(el)
                    el.show('slow')
                }else{
                    alert("An error occurred.")
                }
                $('#confirm_modal button').attr('disabled',false)

            }
        })
    }
</script>