<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-8 padding0 margin0auto">
            <div class="padding-left-15 text-justify">
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        <h1 class="padding0 margin0auto"><?= ucfirst($viewData['vCustomerName']); ?></h1>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        No of People: <label><?php echo $viewData['iNumberOfPeople']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Booking Date: <label><?php echo $viewData['dtBookingDate']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Booking Time: <label><?php echo $viewData['timeSlot']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Min Price: <label><?php echo $viewData['dMinPrice']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Max Price: <label><?php echo $viewData['dMaxPrice']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Location 1: <label><?php echo $viewData['vLocationName1']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Restaurant 1: <label><?php echo $viewData['vRestaurantName1']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Location 2: <label><?php echo $viewData['vLocationName2']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Restaurant 2: <label><?php echo $viewData['vRestaurantName2']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Location 3: <label><?php echo $viewData['vLocationName3']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Restaurant 3: <label><?php echo $viewData['vRestaurantName3']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Occasion: <label><?php echo $viewData['vOcassion']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Special Request: <label><?php echo $viewData['tNote']?></label>
                    </div>
                </div>
                <div class="row padding0 margin0auto">
                    <div class="col-lg-12 padding0 margin0auto">
                        Status: <label><?php echo $viewData['eStatus']?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>