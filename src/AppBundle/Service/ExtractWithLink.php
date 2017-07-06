<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @author Human Booster
 */
class ExtractWithLink {

    private $router;
    private $max;

    public function __construct(\Symfony\Component\Routing\Generator\UrlGeneratorInterface $router, $max) {
        $this->router = $router;
        $this->max = $max;
    }

    public function getExtractWithLink(Article $article) {

        $text = strip_tags($article->getContent());

        if (strlen($text) >= $this->max) {
            $text = substr($text, 0, $this->max);
            $text = substr($text, 0, strrpos($text, " "));
            $url = $this->router->generate('detail_blog', ['id' => $article->getId()]);
            $text .= '... <i><a href="' . $url . '" class="read-more hvr-underline-from-left"> Read more</a></i>';
        }
        return $text;
    }

}
