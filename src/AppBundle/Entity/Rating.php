<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rating
 *
 * @ORM\Table(name="rating")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RatingRepository")
 */
class Rating {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @Assert\Range(
     *      min = 0,
     *      max = 5,
     *      minMessage = "Your minimum rating must be {{ limit }}",
     *      maxMessage = "Your maximum rating must be {{ limit }}"
     * )
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Rating
     */
    public function setRating($rating) {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating() {
        return $this->rating;
    }

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="ratings")
     *
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return Rating
     */
    public function setProduct(\AppBundle\Entity\Product $product) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product
     */
    public function getProduct() {
        return $this->product;
    }

}
