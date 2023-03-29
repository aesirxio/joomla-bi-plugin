<?php
/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Aesirxbi\Component\Aesirx_bi\Administrator\View\Dashboards;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Aesirxbi\Component\Aesirx_bi\Administrator\Helper\Aesirx_biHelper;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\HTML\Helpers\Sidebar;
/**
 * View class for a list of Dashboards.
 *
 * @since  __DEPLOY_VERSION__
 */
class HtmlView extends BaseHtmlView
{
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');

		$this->beforeDisplay();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors));
		}

		$this->addToolbar();

		$this->sidebar = Sidebar::render();
		parent::display($tpl);
	}

	/**
	 * Method to handle data before display
	 *
	 * @return void
	 * @throws \Exception
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function beforeDisplay()
	{
		$params      = ComponentHelper::getComponent('com_aesirx_bi')->getParams();
		$dataStreams = $params->get('react_app_data_stream');

		if (empty($dataStreams) || !is_object($dataStreams))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_AESIRX_BI_ERROR_CONFIGURATION_WARNING'), 'warning');

			return;
		}

		$streams = [];

		$scripts = '
          window.env = {};
			window.env.REACT_APP_CLIENT_ID = "app";
			window.env.REACT_APP_CLIENT_SECRET = "secret";
			window.env.REACT_APP_LICENSE = "LICENSE";
            window.env.REACT_APP_ENDPOINT_URL="' . $params->get('react_app_endpoint_url') . '"
			window.env.PUBLIC_URL="/administrator/components/com_aesirx_bi";
        ';

		foreach ($dataStreams as $row)
		{
			$stream         = new \stdClass();
			$stream->name   = $row->data_stream_name;
			$stream->domain = $row->data_stream_domain;

			$streams[] = $stream;
		}

		$scripts .= 'window.env.REACT_APP_DATA_STREAM=\'' . json_encode($streams) . '\'';

		$document = Factory::getApplication()->getDocument();
		$document->addScriptDeclaration($scripts);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function addToolbar()
	{
		ToolbarHelper::title(Text::_('COM_AESIRX_BI_TITLE_DASHBOARDS'), "generic");

		$toolbar = Toolbar::getInstance('toolbar');
		$canDo   = Aesirx_biHelper::getActions();

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_aesirx_bi');
		}

		// Set sidebar action
		Sidebar::setAction('index.php?option=com_aesirx_bi&view=dashboards');
	}
}
