<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `parcel_list` where parcel_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="parcel-form">
        <input type="hidden" name="id" value="<?php echo isset($parcel_id) ? $parcel_id : '' ?>">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code" class="control-label">Tracking Code</label>
                        <input type="text" name="code" autofocus id="code" required class="form-control form-control-sm rounded-0" value="<?php echo isset($code) ? $code : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="parcel_type_id" class="control-label">Parcel Type</label>
                        <select name="parcel_type_id" id="parcel_type_id" required class="form-select form-select-sm rounded-0 select2" data-placeholder="Select Parcel Type here">
                            <option value="" disabled <?php echo !isset($parcel_type_id) ? "selected": '' ?>></option>
                            <?php 
                            $parcel_type = $conn->query("SELECT * FROM `parcel_type_list` where status = 1 ".(isset($parcel_type_id) ? " OR parcel_type_id = '{$parcel_type_id}'" : "")." order by name asc ");
                            while($row = $parcel_type->fetchArray()):
                            ?>
                                <option value="<?php echo $row['parcel_type_id'] ?>"<?php echo isset($parcel_type_id) && $parcel_type_id == $row['parcel_type_id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sender_name" class="control-label">Sender Name</label>
                        <input type="text" name="sender_name" id="sender_name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($sender_name) ? $sender_name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="sender_contact" class="control-label">Sender Contact</label>
                        <input type="text" name="sender_contact" id="sender_contact" required class="form-control form-control-sm rounded-0" value="<?php echo isset($sender_contact) ? $sender_contact : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="sender_address" class="control-label">Sender Address</label>
                        <textarea rows="3" name="sender_address" style="resize:none" id="sender_address" required class="form-control form-control-sm rounded-0"><?php echo isset($sender_address) ? $sender_address : '' ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="receiver_name" class="control-label">Receiver Name</label>
                        <input type="text" name="receiver_name" id="receiver_name" required class="form-control form-control-sm rounded-0" value="<?php echo isset($receiver_name) ? $receiver_name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="receiver_contact" class="control-label">Receiver Contact</label>
                        <input type="text" name="receiver_contact" id="receiver_contact" required class="form-control form-control-sm rounded-0" value="<?php echo isset($receiver_contact) ? $receiver_contact : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="receiver_address" class="control-label">Receiver Address</label>
                        <textarea rows="3" name="receiver_address" style="resize:none" id="receiver_address" required class="form-control form-control-sm rounded-0"><?php echo isset($receiver_address) ? $receiver_address : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="remarks" class="control-label">Remarks</label>
                        <textarea rows="3" name="remarks" style="resize:none" id="remarks" required class="form-control form-control-sm rounded-0"><?php echo isset($remarks) ? $remarks : '' ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('.select2').select2({
            width:'100%',
            dropdownParent: $('#uni_modal')
        })
        $('#parcel-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=save_parcel',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($parcel_id) ?>" != 1)
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>