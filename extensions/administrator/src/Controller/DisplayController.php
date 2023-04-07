<?php

/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Aesirxbi\Component\Aesirx_bi\Administrator\Controller;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Aesirx_bi master display controller.
 *
 * @since  __DEPLOY_VERSION__
 */
class DisplayController extends BaseController
{
  /**
   * The default view.
   *
   * @var    string
   * @since  __DEPLOY_VERSION__
   */
  protected $default_view = 'dashboards';

  /**
   * Method to display a view.
   *
   * @param   boolean  $cachable   If true, the view output will be cached
   * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
   *
   * @return  BaseController|boolean  This object to support chaining.
   *
   * @since   __DEPLOY_VERSION__
   */
  public function display($cachable = false, $urlparams = [])
  {
    return parent::display();
  }
}
