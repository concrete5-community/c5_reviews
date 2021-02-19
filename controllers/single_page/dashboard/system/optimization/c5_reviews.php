<?php

namespace Concrete\Package\C5Reviews\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;

final class C5Reviews extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make(Repository::class);

        $urls = implode("\n", $config->get('c5_reviews.urls', []));

        $this->set('urls', $urls);
    }

    public function save()
    {
        if (!$this->token->validate('a3020.c5_reviews.settings')) {
            $this->error->add($this->token->getErrorMessage());
            return $this->view();
        }

        $config = $this->app->make(Repository::class);

        $urls = explode("\n", $this->post('urls'));
        $urls = array_map('trim', $urls);
        $urls = array_filter($urls);

        // Make sure we have valid main marketplace pages.
        $urls = array_map(function($url) {
            $url = rtrim($url, '/');
            return str_replace('/reviews', '', $url);
        }, $urls);

        $config->save('c5_reviews.urls', $urls);

        $this->flash('success', t('Your settings have been saved.'));

        return $this->redirect('/dashboard/system/optimization/c5_reviews');
    }
}
