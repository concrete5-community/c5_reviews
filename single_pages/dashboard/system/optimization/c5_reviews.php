<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/** @var $token \Concrete\Core\Validation\CSRF\Token */
/** @var $form \Concrete\Core\Form\Service\Form */
/** @var $urls string */
?>

<div class="ccm-dashboard-content-inner">
    <form method="post" action="<?php echo $this->action('save'); ?>">
        <?php
        echo $token->output('a3020.c5_reviews.settings');
        ?>

        <div class="form-group">
            <?php
            echo $form->label('urls', t('Marketplace URLs'), [
                'class' => 'control-label launch-tooltip',
                'title' => t("One url per line"),
            ]);

            echo $form->textarea('urls', $urls, [
                'rows' => 8,
                'style' => 'width: 100%',
                'placeholder' => t('E.g. https://www.concrete5.org/marketplace/addons/image-optimizer/'),
            ]);
            ?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?php echo Url::to('/index.php/dashboard/system/optimization'); ?>" class="btn btn-default pull-left">
                    <?php echo t('Cancel'); ?>
                </a>
                <?php
                echo $form->submit('submit', t('Save settings'), [
                    'class' => 'btn-primary pull-right'
                ]); ?>
            </div>
        </div>
    </form>
</div>
