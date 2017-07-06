<?php

namespace AppBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Mapping\Annotation\Uploadable;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="ImageRepository")
 * @Gedmo\Uploadable(filenameGenerator="ALPHANUMERIC", allowOverwrite=false, appendNumber=true, allowedTypes="image/png,image/jpeg,image/gif,image/svg+xml", maxSize="5000000")
 */
class Image {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     * @Gedmo\UploadableFilePath
     */
    private $url;

    /**
     * @var string
     *
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your Alt must be at least {{ limit }} characters long",
     *      maxMessage = "Your Alt cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;

    /**
    * @ORM\Column(name="name", type="string")
    * @Gedmo\UploadableFileName
    */
    private $name;

    /**
     * @ORM\Column(name="mime_type", type="string")
     * @Gedmo\UploadableFileMimeType
     */
    private $mimeType;

    /**
     * @ORM\Column(type="decimal")
     * @Gedmo\UploadableFileSize
     */
    private $size;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Image
     */
    public function setUrl($url) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Image
     */
    public function setAlt($alt) {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt() {
        return $this->alt;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Image
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return Image
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return Image
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    public function baseUrl() {
      return basename($this->url);
    }
}
