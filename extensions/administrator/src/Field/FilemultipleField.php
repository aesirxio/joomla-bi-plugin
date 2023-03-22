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

/**
 * Supports an HTML select list of categories
 *
 * @since  __DEPLOY_VERSION__
 */
class FileMultipleField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $type = 'filemultiple';

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
		$html = '<input type="file" name="' . $this->name . '[]" multiple>';

		return $html;
	}
}
