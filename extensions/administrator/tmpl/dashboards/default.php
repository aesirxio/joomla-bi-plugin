<?php
/**
 * @version    __DEPLOY_VERSION__
 * @package    Aesirx BI
 * @author     Aesirx <info@aesirx.io>
 * @copyright  Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;

$params = ComponentHelper::getComponent('com_aesirx_bi')->getParams();
$dataStreams = $params->get('react_app_data_stream', null);

if (empty($dataStreams) || !is_object($dataStreams)) {
  return;
}
?>
<%= htmlWebpackPlugin.tags.headTags %>

<div id="biapp"></div>
