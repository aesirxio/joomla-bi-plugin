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

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

/**
 * Dashboards list controller class.
 *
 * @since  __DEPLOY_VERSION__
 */
class DashboardsController extends AdminController
{
  /**
   * Method to clone existing Dashboards
   *
   * @return  void
   *
   * @throws  Exception
   */
  public function duplicate()
  {
    // Check for request forgeries
    $this->checkToken();

    // Get id(s)
    $pks = $this->input->post->get('cid', [], 'array');

    try {
      if (empty($pks)) {
        throw new \Exception(Text::_('COM_AESIRX_BI_NO_ELEMENT_SELECTED'));
      }

      ArrayHelper::toInteger($pks);
      $model = $this->getModel();
      $model->duplicate($pks);
      $this->setMessage(Text::_('COM_AESIRX_BI_ITEMS_SUCCESS_DUPLICATED'));
    } catch (\Exception $e) {
      Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
    }

    $this->setRedirect('index.php?option=com_aesirx_bi&view=dashboards');
  }

  /**
   * Proxy for getModel.
   *
   * @param   string  $name    Optional. Model name
   * @param   string  $prefix  Optional. Class prefix
   * @param   array   $config  Optional. Configuration array for model
   *
   * @return  object	The Model
   *
   * @since   __DEPLOY_VERSION__
   */
  public function getModel($name = 'Dashboard', $prefix = 'Administrator', $config = [])
  {
    return parent::getModel($name, $prefix, ['ignore_request' => true]);
  }

  /**
   * Method to save the submitted ordering values for records via AJAX.
   *
   * @return  void
   *
   * @since   __DEPLOY_VERSION__
   *
   * @throws  Exception
   */
  public function saveOrderAjax()
  {
    // Get the input
    $pks = $this->input->post->get('cid', [], 'array');
    $order = $this->input->post->get('order', [], 'array');

    // Sanitize the input
    ArrayHelper::toInteger($pks);
    ArrayHelper::toInteger($order);

    // Get the model
    $model = $this->getModel();

    // Save the ordering
    $return = $model->saveorder($pks, $order);

    if ($return) {
      echo '1';
    }

    // Close the application
    Factory::getApplication()->close();
  }
}
