<?php
/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die();

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Aesirxbi\Component\Aesirx_bi\Administrator\Extension\Aesirx_biComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The Aesirx_bi service provider.
 *
 * @since  __DEPLOY_VERSION__
 */
return new class implements ServiceProviderInterface {
  /**
   * Registers the service provider with a DI container.
   *
   * @param   Container  $container  The DI container.
   *
   * @return  void
   *
   * @since   __DEPLOY_VERSION__
   */
  public function register(Container $container)
  {
    $container->registerServiceProvider(new CategoryFactory('\\Aesirxbi\\Component\\Aesirx_bi'));
    $container->registerServiceProvider(new MVCFactory('\\Aesirxbi\\Component\\Aesirx_bi'));
    $container->registerServiceProvider(
      new ComponentDispatcherFactory('\\Aesirxbi\\Component\\Aesirx_bi')
    );
    $container->registerServiceProvider(new RouterFactory('\\Aesirxbi\\Component\\Aesirx_bi'));

    $container->set(ComponentInterface::class, function (Container $container) {
      $component = new Aesirx_biComponent(
        $container->get(ComponentDispatcherFactoryInterface::class)
      );

      $component->setRegistry($container->get(Registry::class));
      $component->setMVCFactory($container->get(MVCFactoryInterface::class));
      $component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
      $component->setRouterFactory($container->get(RouterFactoryInterface::class));

      return $component;
    });
  }
};
