<div class="container margin-top-5">
    <div class="row padding0 margin0auto">
        <div class="col-lg-12 padding0 margin0auto pos-relative text-justify">
            <div class="row padding0 margin0auto">
                <h4 style="font-weight:bold;">Total Purchase : <?php echo count($viewData); ?></h4>
                <?php if (count($viewData) > 0) { ?>
                    <table><tr width="100%">
                            <th width="15%">Restaurant Name</th>
                            <th width="15%">User Name</th>
                            <th width="20%">Email</th>
                            <th width="10%">Phone</th>
                            <th width="10%">Quantity</th>
                            <th width="10%">Price(per unit)</th>
                            <th width="10%">Price(total)</th>
                            <th width="10%">Status</th>
                        </tr>
                        <?php foreach ($viewData as $data) { ?>
                            <tr>
                                <td><?php echo $data['vRestaurantName'] ?></td>
                                <td><?php echo $data['vFirstName'] . ' ' . $data['vLastName'] ?></td>
                                <td><?php echo $data['vEmail'] ?></td>
                                <td><?php echo $data['vMobileNo'] ?></td>
                                <td><?php echo $data['qty'] ?></td>
                                <td><?php echo $data['tDiscountedPrice'] ?></td>
                                <td><?php echo $data['iTotal'] ?></td>
                                <td>
                                    <?php if ($data['eAvailedStatus'] == 'Availed') { ?>
                                        <a title="Click here to cancel redeem" id="aatag<?php echo $data['recordId'] ?>" onclick="return changeStatus(<?php echo $data['recordId'] ?>,'offers/comboStatus/<?php echo $data['recordId'] ?>/y')"  class="btn btn-sm btn-success  marginright10 margintop10"><i class="fa fa-check-circle-o"></i> Redeemed </a>
                                    <?php } else { ?>
                                        <a title="Click here to redeem" id="aatag<?php echo $data['recordId'] ?>" onclick="return changeStatus(<?php echo $data['recordId'] ?>,'offers/comboStatus/<?php echo $data['recordId'] ?>/y')"  class="btn btn-sm btn-inverse  marginright10 margintop10"><i class="fa fa-times-circle-o "></i> Not Redeemed </a> 
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
</div>