<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Parcels List</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-dark btn-sm py-1 rounded-0" type="button" id="create_new">Add New</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="30%">
                <col width="25%">
                <col width="25%">
                <col width="15%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Tracking Code</th>
                    <th class="text-center p-0">Parcel Type</th>
                    <th class="text-center p-0">Status</th>
                    <th class="text-center p-0">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sql = "SELECT p.*,pt.name as `type` FROM `parcel_list` p inner join parcel_type_list pt on p.parcel_type_id = pt.parcel_type_id order by strftime('%s',p.date_added) desc";
                $qry = $conn->query($sql);
                $i = 1;
                    while($row = $qry->fetchArray()):
                ?>
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo $row['code'] ?></td>
                    <td class="py-0 px-1 text-center"><?php echo $row['type'] ?></td>
                    <td class="py-0 px-1 text-center">
                        <?php 
                            switch($row['status']){
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
                    </td>
                    <th class="text-center py-0 px-1">
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle btn-sm rounded-0 py-0" data-bs-toggle="dropdown" aria-expanded="false">
                            Action
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <li><a class="dropdown-item edit_data" href = './?page=view_parcel&id=<?php echo $row['parcel_id'] ?>' >View</a></li>
                            <li><a class="dropdown-item edit_data" data-id = '<?php echo $row['parcel_id'] ?>' href="javascript:void(0)">Edit</a></li>
                            <li><a class="dropdown-item delete_data" data-id = '<?php echo $row['parcel_id'] ?>' data-name = '<?php echo $row['code'] ?>' href="javascript:void(0)">Delete</a></li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <?php endwhile; ?>
                <?php if(!$qry->fetchArray()): ?>
                    <tr>
                        <th class="text-center p-0" colspan="5">No data display.</th>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Parcel Entry',"manage_parcel.php","large")
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Parcel Details',"manage_parcel.php?id="+$(this).attr('data-id'),"large")
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
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
                    location.reload()
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