<?php
if(empty($restaurants)){
    $restaurants  = array(); 
}
?>
<!doctype html>
<html lang="en-us">
        <select class="form-control maxwidth500" name="iRestaurantID[]" id="iRestaurantID" multiple="multiple">
            <?php foreach($restaurantList AS $restaurant){ ?>
            <option value="<?php echo $restaurant["iRestaurantID"]; ?>" <?php if(in_array($restaurant["iRestaurantID"], $restaurants)) echo "selected" ?>><?php echo ucfirst($restaurant["vRestaurantName"]); ?> [<?php echo $restaurant["iRestaurantID"]; ?>]</option>
            <?php } ?>
        </select>
        <link rel="stylesheet" href="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.css" type="text/css" media="screen" />
        <script src="<?= base_url() ?>js/bootstrap-multiselect/bootstrap-multiselect.js" type="text/javascript" charset="utf-8"></script>

        <script>
            jQuery(document).ready(function () {
                
                $('#iRestaurantID').multiselect({
                    includeSelectAllOption: true,
                    selectAllText: 'All Users!',
                    enableFiltering: true,
                    filterBehavior: 'value'
//                    selectAllValue: 'all'
                });
            });
        </script>
</html>