<?php
/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Aesirxbi\Component\Aesirx_bi\Administrator\Helper;
// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;

/**
 * Aesirx_bi helper.
 *
 * @since  __DEPLOY_VERSION__
 */
class Aesirx_biHelper
{
  /**
   * Gets the files attached to an item
   *
   * @param   int     $pk     The item's id
   *
   * @param   string  $table  The table's name
   *
   * @param   string  $field  The field's name
   *
   * @return  array  The files
   */
  public static function getFiles($pk, $table, $field)
  {
    $db = Factory::getContainer()->get('DatabaseDriver');
    $query = $db->getQuery(true);

    $query
      ->select($field)
      ->from($table)
      ->where('id = ' . (int) $pk);

    $db->setQuery($query);

    return explode(',', $db->loadResult());
  }

  /**
   * Gets a list of the actions that can be performed.
   *
   * @return  CMSObject
   *
   * @since   __DEPLOY_VERSION__
   */
  public static function getActions()
  {
    $user = Factory::getApplication()->getIdentity();
    $result = new CMSObject();

    $assetName = 'com_aesirx_bi';

    $actions = [
      'core.admin',
      'core.manage',
      'core.create',
      'core.edit',
      'core.edit.own',
      'core.edit.state',
      'core.delete',
    ];

    foreach ($actions as $action) {
      $result->set($action, $user->authorise($action, $assetName));
    }

    return $result;
  }
}
