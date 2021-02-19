<?php

defined('C5_EXECUTE') or die('Access Denied');

/** @var $date \Concrete\Core\Localization\Service\Date */
/** @var $reviews array */
/** @var $review \A3020\C5Reviews\Entity\Review */

if (count($reviews) === 0) {
    return;
}

echo '<h3>' . t('Reviews') . '</h3>';

foreach ($reviews as $review) {
    ?>
    <blockquote>
        <strong>"<?php echo h($review->getTitle()) ?>"</strong>

        <p>
            <?php
            echo strip_tags($review->getReview(), '<br>');
            ?>
        </p>

        <a
            title="<?php echo t('Visit the marketplace review page'); ?>"
            href="<?php echo $review->getMarketplaceReviewUrl() ?>"
            target="_blank"
        >
            <strong>
                <?php
                echo ucfirst(h($review->getAuthor()));
                ?>
            </strong>

            on <?php echo $date->formatDateTime($review->getDate()); ?>
        </a>
    </blockquote>
    <?php
}
