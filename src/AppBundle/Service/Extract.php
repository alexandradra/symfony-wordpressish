<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Human Booster
 */
class Extract {

    private $session;
    private $max;
    private $following;

    public function __construct(\Symfony\Component\HttpFoundation\Session\SessionInterface $session, $max, $following) {
        $this->session = $session;
        $this->max = $max;
        $this->following = $following;
    }

    public function getExtract($text) {
        $text = strip_tags($text);

        if (strlen($text) >= $this->max) {
            $text = substr($text, 0, $this->max);
            $text = substr($text, 0, strrpos($text, " ")) . $this->following;
        }
//        $this->session->getFlashBag()->add('success', 'Extract made with love 😍');
        return $text;
    }

}
