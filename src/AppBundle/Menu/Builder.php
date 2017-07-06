<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $translator = $this->container->get('translator');

        $home = $translator->trans('menu.home');
        $catalog = $translator->trans('menu.catalog');

        $menu->addChild('Home', array('route' => 'homepage', 'label' => $home));
        $menu->addChild('Blog', array('route' => 'homepage_blog'));
        $menu->addChild('Catalog', array('route' => 'homepage_catalog', 'label' => $catalog));
        $menu->addChild('Add article', array('route' => 'create_blog'));
        $menu->addChild('Translation', array('route' => 'translation_blog'));
        $menu->addChild('Login', array('route' => 'fos_user_security_login'));
        $menu->addChild('Register', array('route' => 'fos_user_registration_register'));

        return $menu;
    }
}
