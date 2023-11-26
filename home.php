<h3>Welcome to Logistic Hub Parcels  Management System</h3>
<hr>
<div class="col-12">
    <div class="col-md-12">
     <div class="row row-cols-sm-1 row-cols-md-2 row-cols-xl-4 gx-3 gy-2 text-light">
        <div class="col">
            <div class="px-2 py-3 bg-gradient bg-primary h-100">
                <div class="w-100 d-flex align-items-center">
                    <span class="fa fa-th-list fs-3 col-auto"></span>
                    <div class='col-auto flex-grow-1 text-end'>
                        <span class="fw-bold">Total Parcel Type</span><br>
                        <span>
                            <?php 
                            $parcel_type = $conn->query("SELECT count(parcel_type_id) from parcel_type_list where status = 1")->fetchArray()[0];
                            echo $parcel_type > 0 ? number_format($parcel_type) : 0;
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="px-2 py-3 bg-gradient bg-warning h-100">
                <div class="w-100 d-flex align-items-center">
                    <span class="fa fa-user-friends fs-3 col-auto"></span>
                    <div class='col-auto flex-grow-1 text-end'>
                        <span class="fw-bold">Total Carriers</span><br>
                        <span>
                            <?php 
                            $carrier = $conn->query("SELECT count(carrier_id) from carrier_list where status = 1")->fetchArray()[0];
                            echo $carrier > 0 ? number_format($carrier) : 0;
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="px-2 py-3 bg-gradient bg-info h-100">
                <div class="w-100 d-flex align-items-center">
                    <span class="fa fa-boxes fs-3 col-auto"></span>
                    <div class='col-auto flex-grow-1 text-end'>
                        <span class="fw-bold">Total Parcels</span><br>
                        <span>
                            <?php 
                            $parcel = $conn->query("SELECT count(parcel_id) from parcel_list where status != 1")->fetchArray()[0];
                            echo $parcel > 0 ? number_format($parcel) : 0;
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="px-2 py-3 bg-gradient bg-dark h-100">
                <div class="w-100 d-flex align-items-center">
                    <span class="fa fa-truck fs-3 col-auto"></span>
                    <div class='col-auto flex-grow-1 text-end'>
                        <span class="fw-bold">Out for Delivery</span><br>
                        <span>
                            <?php 
                            $parcel = $conn->query("SELECT count(parcel_id) from parcel_list where status = 2")->fetchArray()[0];
                            echo $parcel > 0 ? number_format($parcel) : 0;
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
     </div>
    </div>
</div>
<script>
    $(function(){
        
    })
</script>