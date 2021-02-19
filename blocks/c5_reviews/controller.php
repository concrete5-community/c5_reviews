<?php

namespace Concrete\Package\C5Reviews\Block\C5Reviews;

use A3020\C5Reviews\Entity\Review;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Config\Repository\Repository;
use Doctrine\ORM\EntityManager;

class Controller extends BlockController
{
    protected $btTable = 'btC5Reviews';
    protected $btDefaultSet = "social";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;

    protected $helpers = [
        'date',
        'form',
    ];

    /** @var string */
    public $packageUrl;

    public function getBlockTypeName()
    {
        return t('C5 Reviews');
    }

    public function getBlockTypeDescription()
    {
        return t('Show concrete5 reviews');
    }

    public function view()
    {
        $this->set('reviews', $this->getReviews());
    }

    public function add()
    {
        $this->set('packageOptions', $this->getPackageOptions());
    }

    public function edit()
    {
        $this->set('packageOptions', $this->getPackageOptions());
    }

    /**
     * We don't get the values from the C5Reviews table on purpose.
     *
     * Sometimes an add-on doesn't have a review yet,
     * but we want to add the block to the page already.
     *
     * We don't have the package handle, so we use the URL as a unique identifier.
     *
     * @return array
     */
    protected function getPackageOptions()
    {
        $config = $this->app->make(Repository::class);

        $urls = $config->get('c5_reviews.urls', []);

        $options = [];
        foreach ($urls as $url) {
            $handle = str_replace([
                'http://www.concrete5.org/marketplace/addons/',
                'http://www.concrete5.org/marketplace/themes/',
                'https://www.concrete5.org/marketplace/addons/',
                'https://www.concrete5.org/marketplace/themes/',
            ], '', $url);

            $name = str_replace(['_', '-'], ' ', $handle);
            $name = ucwords($name);

            $options[$url] = $name;
        }

        return $options;
    }

    /**
     * @return Review[]
     */
    private function getReviews()
    {
        /** @var EntityManager $em */
        $em = $this->app->make(EntityManager::class);
        $repo = $em->getRepository(Review::class);

        if ($this->packageUrl) {
            return $repo->findBy([
                'url' => (string) $this->packageUrl,
            ]);
        }

        return $repo->findAll();
    }
}
