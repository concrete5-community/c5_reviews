<?php

defined('C5_EXECUTE') or die("Access Denied.");

/** @var $form \Concrete\Core\Form\Service\Form */
/** @var $packageOptions array */
/** @var $packageUrl string */
?>

<div class="form-group">
    <?php
    echo $form->label('packageUrl', t('Package'));
    ?>
    <div class="input">
        <?php
        echo $form->select('packageUrl', $packageOptions, $packageUrl);
        ?>
    </div>
</div>
