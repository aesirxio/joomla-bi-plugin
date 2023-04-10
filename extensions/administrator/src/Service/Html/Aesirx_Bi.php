<?php
/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Aesirxbi\Component\Aesirx_bi\Administrator\Service\Html;

// No direct access
defined('_JEXEC') or die();

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;

/**
 * Aesirx_bi HTML Helper.
 *
 * @since  __DEPLOY_VERSION__
 */
class AesirxBi
{
  use DatabaseAwareTrait;

  /**
   * Public constructor.
   *
   * @param   DatabaseDriver  $db  The Joomla DB driver object for the site's database.
   */
  public function __construct(DatabaseDriver $db)
  {
    $this->setDbo($db);
  }

  public function toggle($value = 0, $view = '', $field = '', $i = '')
  {
    $states = [
      0 => ['icon-unpublish', Text::_('Toggle'), ''],
      1 => ['icon-publish', Text::_('Toggle'), ''],
    ];

    $state = ArrayHelper::getValue($states, (int) $value, $states[0]);
    $text = '<span aria-hidden="true" class="' . $state[0] . '"></span>';
    $html = '<a href="javascript:void(0);" class="tbody-icon ' . $state[2] . '"';
    $html .=
      'onclick="return Joomla.toggleField(\'cb' .
      $i .
      '\',\'' .
      $view .
      '.toggle\',\'' .
      $field .
      '\')" title="' .
      Text::_($state[1]) .
      '">' .
      $text .
      '</a>';

    return $html;
  }
}
