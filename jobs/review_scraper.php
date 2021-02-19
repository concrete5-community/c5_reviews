<?php

namespace Concrete\Package\C5Reviews\Job;

use A3020\C5Reviews\Entity\Review;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Client\Client;
use Concrete\Core\Job\QueueableJob;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use DOMXPath;
use ZendQueue\Message as ZendQueueMessage;
use ZendQueue\Queue as ZendQueue;

final class ReviewScraper extends QueueableJob
{
    /** @var Application */
    private $appInstance;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Repository */
    protected $config;

    public function __construct(
        Application $appInstance,
        EntityManagerInterface $entityManager,
        Repository $repository
    )
    {
        $this->appInstance = $appInstance;
        $this->entityManager = $entityManager;
        $this->config = $repository;

        $this->jQueueBatchSize = $this->config->get('c5_reviews.batch_size', 3);
    }

    public function getJobName()
    {
        return t('Review Scraper');
    }

    public function getJobDescription()
    {
        return t('Scrapes reviews from concrete5.org.');
    }

    /**
     * @param ZendQueue $q
     * @throws \Exception
     * @return mixed|void
     */
    public function start(ZendQueue $q)
    {
        $this->flushReviews();

        foreach ($this->config->get('c5_reviews.urls', []) as $url) {
            $url = trim($url);
            if (!$url) {
                continue;
            }

            $payload = json_encode([
                'url' => $url,
            ]);

            $q->send($payload);
        }
    }

    /**
     * @param ZendQueueMessage $msg
     */
    public function processQueueItem(ZendQueueMessage $msg)
    {
        $body = json_decode($msg->body, true);
        $this->scrape($body['url']);
    }

    /**
     * Finish processing a queue.
     *
     * @param ZendQueue $q
     *
     * @return mixed
     */
    public function finish(ZendQueue $q)
    {
        return t('All reviews have been synchronized.');
    }

    /**
     * @param string $url The URL to the main marketplace page.
     */
    public function scrape($url)
    {
        $body = $this->getPage($url . '/reviews');

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($body);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);

        // Get name of the package from H1 tag.
        $package = $xpath->query('//h1');
        $package = $package->item(0)->nodeValue;

        $reviews = $xpath->query('//*[@class="discussion-comment-level-0"]');
        if ($reviews->length === 0) {
            return;
        }

        /** @var \DOMNodeList $reviews */
        foreach ($reviews as $review) {
            $review = $this->extractReview($xpath, $review);
            $review->setPackage($package);
            $review->setUrl($url);
            $this->saveReview($review);
        }
    }

    /**
     * @param DOMXPath $xpath
     * @param \DOMElement $review
     * @return Review
     */
    private function extractReview($xpath, $review)
    {
        $details = $xpath->query('.//*[@class="details"]', $review);
        $details = $details->item(0);
        $author = $xpath->query('.//strong', $details)
            ->item(0)
            ->nodeValue;

        $date = $xpath->query('.//time', $details)
            ->item(0)
            ->nodeValue;
        $time = $xpath->query('.//time', $details)
            ->item(1)
            ->nodeValue;

        $date = DateTimeImmutable::createFromFormat('M j, Y g:i a', $date.' '.$time);

        $title = $xpath->query('.//h1', $review);
        $title = $title->item(0)->nodeValue;

        $reviewText = $xpath->query('.//*[@class="formatted-text"]', $review);
        $reviewText = $reviewText->item(0)->nodeValue;

        // Get rid of tab characters
        $reviewText = str_replace("\t", "", $reviewText);

        // Replace newlines with br's
        $reviewText = str_replace("\n", "<br>", $reviewText);

        // Remove first br
        $reviewText = ltrim($reviewText, '<br>');

        // Create a new entity
        $review = new Review();
        $review->setAuthor($author);
        $review->setDate($date);
        $review->setTitle($title);
        $review->setReview($reviewText);

        return $review;
    }

    private function saveReview($review)
    {
        $this->entityManager->persist($review);
        $this->entityManager->flush();
    }

    /**
     * Remove all existing reviews from C5Reviews table.
     */
    private function flushReviews()
    {
        $entities = $this->entityManager->getRepository(Review::class)
            ->findAll();
        array_walk($entities, function($entity) {
             $this->entityManager->remove($entity);
        });

        $this->entityManager->flush();
    }

    /**
     * @param string $url
     * @return string
     */
    private function getPage($url)
    {
        /** @var Client $client */
        $client = $this->appInstance->make('http/client/curl');
        $client->setUri($url);
        return $client->send()->getBody();
    }
}
