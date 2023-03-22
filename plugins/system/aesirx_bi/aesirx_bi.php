<?php
/**
 * @package     AesirX
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Aesirx BI Data plugin
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgSystemAesirx_Bi extends CMSPlugin
{
	/**
	 * Method to catch the onBeforeAesirXBiLoaded event.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function onBeforeAesirXBiLoaded()
	{
		$params = $this->params;

		$document = Factory::getApplication()->getDocument();
		$document->addScriptDeclaration('
            window.env = {};
            window.env.REACT_APP_CLIENT_ID="' . $params->get('react_app_client_id') . '"
            window.env.REACT_APP_CLIENT_SECRET="' . $params->get('react_app_client_secret') . '"
            window.env.REACT_APP_LICENSE="' . $params->get('react_app_license') . '"
            window.env.REACT_APP_ENDPOINT_URL="' . $params->get('react_app_endpoint_url') . '"
            window.env.REACT_APP_DATA_STREAM="' . $params->get('react_app_data_stream') . '"
        '
		);
	}

}
