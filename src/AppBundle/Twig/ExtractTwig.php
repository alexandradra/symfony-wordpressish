<?php

namespace AppBundle\Twig;

class ExtractTwig extends \Twig_Extension {

    private $max;
    private $following;

    public function __construct($max, $following) {
        $this->max = $max;
        $this->following = $following;
    }

    public function getExtractTwig($text) {
        $text = strip_tags($text);

        if (strlen($text) >= $this->max) {
            $text = substr($text, 0, $this->max);
            $text = substr($text, 0, strrpos($text, " ")) . $this->following;
        }
        return $text;
    }

    public function getFunctions() {
        return array(new \Twig_SimpleFunction('extract', array($this, 'getExtractTwig')));
    }

    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('extract', array($this, 'getExtractTwig')),
        );
    }

}
