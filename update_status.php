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
    <form action="" id="status-form">
        <input type="hidden" name="id" value="<?php echo isset($parcel_id) ? $parcel_id : '' ?>">
        <div class="form-group">
            <label for="status" class="control-label">Status</label>
            <select name="status" id="status" required class="form-select form-select-sm rounded-0" >
                <option value="0"<?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Pending</option>
                <option value="1"<?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Out for Delivery</option>
                <option value="2"<?php echo isset($status) && $status == 2 ? 'selected' : '' ?>>Delivered</option>
                <option value="3"<?php echo isset($status) && $status == 3 ? 'selected' : '' ?>>Delivery Failed</option>
            </select>
        </div>
        <div class="form-group" style="display:none">
            <label for="carrier_id" class="control-label">Carrier/Deliver Rider</label>
            <select name="carrier_id" id="carrier_id" required class="form-select form-select-sm rounded-0 select2" data-placeholder="Select Parcel Type here">
                <option value="" disabled <?php echo !isset($carrier_id) ? "selected": '' ?>></option>
                <?php 
                $carrier = $conn->query("SELECT * FROM `carrier_list` where status = 1 order by name asc ");
                while($row = $carrier->fetchArray()):
                ?>
                    <option value="<?php echo $row['carrier_id'] ?>"<?php echo isset($carrier_id) && $carrier_id == $row['carrier_id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group" style="display:none">
            <label for="remarks" class="control-label">Remarks</label>
            <textarea rows="3" name="remarks" style="resize:none" id="remarks" class="form-control form-control-sm rounded-0"></textarea>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#carrier_id').select2({
            width:'100%',
            dropdownParent: $('#uni_modal')
        })
        $('#status').change(function(){
            var status = $(this).val()
                $('#carrier_id').attr('required',false)
                $('#remarks').attr('required',false)
                $('#carrier_id').parent().hide()
                $('#remarks').parent().hide()
            if(status == 1){
                $('#carrier_id').attr('required',true)
                $('#carrier_id').parent().show()
            }else if(status == 3){
                $('#remarks').attr('required',true)
                $('#remarks').parent().show()
            }
        })
        $('#status').trigger('change')
        $('#status-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'./Actions.php?a=update_parcel_status',
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
                        location.reload()
                        _el.addClass('alert alert-success')
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