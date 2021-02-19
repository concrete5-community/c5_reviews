<?php

namespace Concrete\Package\C5Reviews;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Support\Facade\Package as PackageFacade;
use Concrete\Core\Job\Job;
use Concrete\Core\Package\Package;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;

class Controller extends Package
{
    protected $pkgHandle = 'c5_reviews';
    protected $appVersionRequired = '8.2';
    protected $pkgVersion = '1.0.3';
    protected $pkgAutoloaderRegistries = [
        'src/A3020/C5Reviews' => '\A3020\C5Reviews',
    ];

    public function getPackageName()
    {
        return t('C5 Reviews');
    }

    public function getPackageDescription()
    {
        return t('Scrapes and shows reviews from concrete5.org.');
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installEverything($pkg);
    }

    public function upgrade()
    {
        $pkg = PackageFacade::getByHandle($this->pkgHandle);
        $this->installEverything($pkg);
    }

    public function installEverything($pkg)
    {
        $this->installJob($pkg);
        $this->installBlockTypes($pkg);
        $this->installDashboardPage($pkg);
    }

    private function installJob($pkg)
    {
        $handle = 'review_scraper';

        /** @var Job $job */
        $job = Job::getByHandle($handle);
        if (!$job) {
            Job::installByPackage($handle, $pkg);
        }
    }

    private function installBlockTypes($pkg)
    {
        $handles = [
            'c5_reviews',
        ];

        foreach ($handles as $handle) {
            if (!BlockType::getByHandle($handle)) {
                BlockType::installBlockType($handle, $pkg);
            }
        }
    }

    private function installDashboardPage($pkg)
    {
        $path = '/dashboard/system/optimization/c5_reviews';

        /** @var Page $page */
        $page = Page::getByPath($path);
        if ($page && !$page->isError()) {
            return;
        }

        $singlePage = Single::add($path, $pkg);
        $singlePage->update($this->getPackageName());
    }

    public function uninstall()
    {
        parent::uninstall();

        $db = $this->app->make('database')->connection();
        $db->executeQuery("DROP TABLE IF EXISTS btC5Reviews");
        $db->executeQuery("DROP TABLE IF EXISTS C5Reviews");
    }
}
