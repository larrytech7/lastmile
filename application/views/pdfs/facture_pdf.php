<!DOCTYPE html>
<html>
<head>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= base_url('resources/');?>assets/vendor/bootstrap/css/bootstrap.min.css" type="text/css">

    <!-- Fonts -->
    <link href="<?= base_url('resources/');?>fonts.googleapis.com/cssfb86.css?family=Nunito:400,600,700,800|Roboto:400,500,700" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="<?= base_url('resources/');?>assets/fonts/font-awesome/css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url('resources/');?>assets/fonts/ionicons/css/ionicons.min.css" type="text/css">

    <!-- Global style (main) -->
    <link id="stylesheet" type="text/css" href="<?= base_url('resources/');?>assets/css/boomerang.min.css" rel="stylesheet" media="screen">

    <!-- Custom style - Remove if not necessary -->
    <link type="text/css" href="<?= base_url('resources/');?>assets/css/custom-style.css" rel="stylesheet"/>
    <style>
        .container{
            max-width: 90% !important;
        }
    </style>
</head>
<body style="color: black !important">

<section class="slice-sm sct-color-2" style="background-color: rgb(132,139,157)">
    <div class="profile">
        <div class="container">
            <div class="row cols-xs-space cols-sm-space cols-md-space">
                <div class="col-lg-1">
                        <img style="text-align: end" src="<?= base_url('resources/assets/');?>images/logo/logo-trans.png" alt="OUSSMANE-TRANSIT" class="img img-responsive pull-left" width="80" height="80" />
                </div>
                <div class="col-lg-5">
                    <div>
                        <h4 style="color: white; text-align: start">S.A.S.U OUSSMANE TRANSIT <?= strcasecmp($package['container_destination'], 'douala') != 0 || strcasecmp($package['container_destination'], 'douala') != 0 ? '-CIV' : '' ?></h4>
                        <h6 style="color: white; text-align: start">1 All&eacute;e des Performances</h6>
                        <h6 style="color: white; text-align: start">93165 Noisy Le Grand Cedex</h6>
                    </div>
                </div>
                <div class="col-lg-6" style="text-align: end|right; color: #fff;">
                </div>
                <div class="col-lg-12">
                    <div class="main-content">
                        <div class="modal-content" style="padding: 10px" id="section-to-print">
                            <div class="page-title">
                                <div class="row align-items-center">
                                    <div class="col-md-12 col-12">
                                        <h4>FACTURE - <?= $package['package_id'] ?>. <span class="pull-right"> N<sup>o</sup> D&eacute;part : <?= $package['package_start_number'] ?></span>
                                        </h4>
                                        <h5>Date : <?= date('d-m-Y') ?></h5>
                                        <h5 class="text-center"> Destination &nbsp;:
                                            <i class="ion-location text-danger"></i> <?= $package['container_destination'] ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-condensed">
                                        <thead style="background-color: #997058">
                                            <tr class="text-white">
                                                <td><?= $this->lang->line('sent_from')?><i class="ion ion-ios-person"></i>
                                                    <a href="<?= site_url('/client/view/'.$package['client_id'])?>"> <?= $package['client_salutation'].' '. $package['client_surname']
                                                        .' '.$package['client_name'].' '.$package['client_company_representative'].', '.$package['client_company']?></a>
                                                </td>
                                                <td><?= $this->lang->line('sent_to')?> :
                                                    <?= $package['contact_name'] ?? $package['client_salutation'].' '. $package['client_surname']
                                                    .' '.$package['client_name'].' '.$package['client_company_representative'].', '.$package['client_company'] ?>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tr>
                                            <td><i class="ion ion-android-call"></i> <?= $package['client_phone']?></td>
                                            <td><?= $package['contact_phone'] ?? $package['client_phone'].' ,'.$package['client_phone_2'].' ,'.$package['client_phone_3'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><i class="ion ion-ios-location"></i> <?= $package['client_address']. '<br> ' . $package['client_city'].', '. $package['client_country']?></td>
                                            <td><?= ($package['contact_address'] ?? '') .', '. $package['contact_country'] ?? '' ?></td>
                                        </tr>
                                    </table>
                                    <p><?= $this->lang->line('garanti').' : '.($package['garantie'] == 0 ? "<b>AUCUN</b>" : "<b>OK<b/>") ?></p>
                                    <div class="table-responsive">
                                        <table class="table table-condensed table-striped table-secondary">
                                            <thead class="bg-active">
                                            <tr>
                                                <td class="text-center"><strong><?= $this->lang->line('package_category') ?></strong></td>
                                                <td class="text-center"><strong><?= $this->lang->line('volume') ?> (m<sup>3</sup>)</strong></td>
                                                <td class="text-center"><strong><?= $this->lang->line('price') ?>/m<sup>3</sup></strong></td>
                                                <td class="text-center"><strong><?= $this->lang->line('price_discount') ?></strong></td>
                                                <td class="text-right"><strong><?= $this->lang->line('total_amount') ?> EUR(XAF)</strong></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $total = 0;$discountedAmount=0; ?>
                                            <?php foreach($items as $item): ?>
                                                <tr>
                                                    <td class="text-center"><?= $item['item_name'] ?></td>
                                                    <td class="text-center"><?= round($item['item_height']* $item['item_width'] *$item['item_length'],2) ?></td>
                                                    <td class="text-center"><?= $item['item_price'] ?></td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right text-danger">
                                                        <?php
                                                        $amountToPay = round($item['item_height']* $item['item_width'] *$item['item_length'] ,2)* $item['item_price'];
                                                        $total += $amountToPay;
                                                        ?>
                                                        <?= round($amountToPay,2) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td class="strong-700">TOTAL</td>
                                                <td>-</td>
                                                <td>-</td>
                                                <td class="text-center">
                                                    <?php
                                                    try{
                                                        $discount = $package['package_discount'];
                                                        $discountedAmount = $discount > 1 ? $total - $discount : $total - ($total* $discount);
                                                        if($total == 0){
                                                            echo $package['package_discount'].'(0';
                                                        }else{
                                                            echo $package['package_discount'].'('. round(($total - $discountedAmount)/$total * 100,2);
                                                        }
                                                    }catch(Exception $ex){}  ?>%)
                                                </td>
                                                <td class="strong-700 text-danger text-right">
                                                    <?= number_format($discountedAmount,2).' EUR <br>'.number_format(round($discountedAmount*655.96, 2),2).'FCFA' ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <p>REMARQUE : <?= $package['package_remark']?> <?= '' ?></p>
                                        <p>
                                            <span style="color: black; text-align: end">Fait le : <?= $package['create_time'] ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <p style="color: white; text-align: center">
                        <span class="strong">Email: </span> info@oussmane-transit.com /
                        <span class="strong">Site: </span> www.oussmane-transit.com
                    </p>
                    <p style="color: white; text-align: center"><span class="strong">Tel: </span>0185101380/
                        <?php if(in_array($package['container_destination'], ['douala','Douala','Yaounde','yaounde','Edea','edea'])): ?>
                            <span class="strong">Douala: </span>(+237) 658 64 24 03 /
                            <span class="strong">Yaound&eacute;: </span>(+237) 658 61 56 08 /
                        <?php else: ?>
                            <span class="strong">Abidjan: </span>(+255)  /
                            <span class="strong">Bouak&eacute;: </span>(+255)
                        <?php endif; ?>
                    </p>
                    <p style="color: black; text-align: center">
                        RCS de Bobigny 222 335 au capital de 7000EUR. TVA intracommunautaire : FR88 840 222 335, EORI FR840 222 335 00019.
                        <br>
                        <?= date('Y')?> &copy; <?= $this->lang->line('copyright') ?> OUSSMANE TRANSIT.
                    </p>
                </div>
                <div class="col-lg-3"></div>
            </div>
        </div>
    </div>
</section>

</body>
</html>