<?php
/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Aesirxbi\Component\Aesirx_bi\Administrator\Field;

defined('JPATH_BASE') or die;

use \Joomla\CMS\Form\FormField;
use \Joomla\CMS\Factory;

/**
 * Supports an HTML select list of categories
 *
 * @since  __DEPLOY_VERSION__
 */
class ModifiedbyField extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'modifiedby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string    The field input markup.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html   = array();
		$user = Factory::getApplication()->getIdentity();
		$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $user->id . '" />';
		if (!$this->hidden)
		{
			$html[] = "<div>" . $user->name . " (" . $user->username . ")</div>";
		}

		return implode($html);
	}
}
