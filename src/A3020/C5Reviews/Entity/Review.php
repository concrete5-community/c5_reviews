<?php

namespace A3020\C5Reviews\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="C5Reviews",
 *   indexes={
 *     @ORM\Index(name="id", columns={"id"})
 *   }
 * )
 */
class Review
{
    /**
     * @ORM\Id @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $package;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $url;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $author;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $review;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param string $review
     */
    public function setReview($review)
    {
        $this->review = $review;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getMarketplaceReviewUrl()
    {
        return rtrim('/', $this->url) .'/reviews';
    }
}
