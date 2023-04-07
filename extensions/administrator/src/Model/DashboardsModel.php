<?php
/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Aesirxbi\Component\Aesirx_bi\Administrator\Model;
// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\Database\ParameterType;
use Joomla\Utilities\ArrayHelper;
use Aesirxbi\Component\Aesirx_bi\Administrator\Helper\Aesirx_biHelper;

/**
 * Methods supporting a list of Dashboards records.
 *
 * @since  __DEPLOY_VERSION__
 */
class DashboardsModel extends ListModel
{
  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @param   string  $ordering   Elements order
   * @param   string  $direction  Order direction
   *
   * @return void
   *
   * @throws Exception
   */
  protected function populateState($ordering = null, $direction = null)
  {
    // List state information.
    parent::populateState('a.id', 'ASC');

    $context = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $context);

    // Split context into component and optional section
    if (!empty($context)) {
      $parts = FieldsHelper::extract($context);

      if ($parts) {
        $this->setState('filter.component', $parts[0]);
        $this->setState('filter.section', $parts[1]);
      }
    }
  }

  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param   string  $id  A prefix for the store id.
   *
   * @return  string A store id.
   *
   * @since   __DEPLOY_VERSION__
   */
  protected function getStoreId($id = '')
  {
    // Compile the store id.
    $id .= ':' . $this->getState('filter.search');
    $id .= ':' . $this->getState('filter.state');

    return parent::getStoreId($id);
  }

  /**
   * Build an SQL query to load the list data.
   *
   * @return  DatabaseQuery
   *
   * @since   __DEPLOY_VERSION__
   */
  protected function getListQuery()
  {
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    return $query;
  }

  /**
   * Get an array of data items
   *
   * @return mixed Array of data items on success, false on failure.
   */
  public function getItems()
  {
    $items = parent::getItems();

    return $items;
  }
}
