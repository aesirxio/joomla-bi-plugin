<?php
/**
 * @package     AesirX
 * @subpackage  BI
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\Registry\Registry;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * Custom installation of AesirX
 *
 * @package     AesirX
 * @subpackage  Install
 * @since       1.0
 */
class Com_Aesirx_BiInstallerScript
{
  /**
   * Status of the installation
   *
   * @var  stdClass
   */
  public $status;

  /**
   * Show component info after install / update
   *
   * @var  boolean
   */
  public $showComponentInfo = true;

  /**
   * Installer instance
   *
   * @var  JInstaller
   */
  public $installer;

  /**
   * Extension element name
   *
   * @var  string
   */
  protected $extensionElement = '';

  /**
   * Manifest of the extension being processed
   *
   * @var  SimpleXMLElement
   */
  protected $manifest;

  /**
   * Old version according to manifest
   *
   * @var  string
   */
  protected $oldVersion = '0.0.0';

  /**
   * Get the common JInstaller instance used to install all the extensions
   *
   * @return JInstaller The JInstaller object
   */
  public function getInstaller()
  {
    if (null === $this->installer) {
      $this->installer = new Installer();
    }

    return $this->installer;
  }

  /**
   * Getter with manifest cache support
   *
   * @param   JInstallerAdapter  $parent  Parent object
   *
   * @return  SimpleXMLElement
   */
  protected function getManifest($parent)
  {
    if (null === $this->manifest) {
      $this->loadManifest($parent);
    }

    return $this->manifest;
  }

  /**
   * Method to install the component
   *
   * @param   JInstallerAdapter  $parent  Class calling this method
   *
   * @return  boolean          True on success
   */
  public function install($parent)
  {
    // Common tasks for install or update
    $this->installPlugins($parent);

    return true;
  }

  /**
   * Install the package libraries
   *
   * @param   JInstallerAdapter  $parent  class calling this method
   *
   * @return  void
   */
  protected function installPlugins($parent)
  {
    // Required objects
    $installer = $this->getInstaller();
    $manifest = $parent->getManifest();
    $src = $parent->getParent()->getPath('source');
    $nodes = $manifest->plugins->plugin;

    if (empty($nodes)) {
      return;
    }

    foreach ($nodes as $node) {
      $extName = $node->attributes()->name;
      $extGroup = $node->attributes()->group;
      $disabled = !empty($node->attributes()->disabled) ? true : false;
      $extPath = $src . '/plugins/' . $extGroup . '/' . $extName;
      $result = 0;

      // Standard install
      if (is_dir($extPath)) {
        $installer->setAdapter('plugin');
        $result = $installer->install($extPath);
      } elseif ($extId = $this->searchExtension($extName, 'plugin', '-1', $extGroup)) {
        // Discover install
        $result = $installer->discover_install($extId);
      }

      // Store the result to show install summary later
      $this->_storeStatus('plugins', [
        'name' => $extName,
        'group' => $extGroup,
        'result' => $result,
      ]);

      // Enable the installed plugin
      if ($result && !$disabled) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'));
        $query->set($db->qn('enabled') . ' = 1');
        $query->set($db->qn('state') . ' = 1');
        $query->where($db->qn('type') . ' = ' . $db->q('plugin'));
        $query->where($db->qn('element') . ' = ' . $db->q($extName));
        $query->where($db->qn('folder') . ' = ' . $db->q($extGroup));
        $db->setQuery($query)->execute();
      }
    }
  }

  /**
   * method to update the component
   *
   * @param   JInstallerAdapter  $parent  class calling this method
   *
   * @return void
   */
  public function update($parent)
  {
    $this->installPlugins($parent);
  }

  /**
   * method to uninstall the component
   *
   * @param   JInstallerAdapter  $parent  class calling this method
   *
   * @return  void
   *
   * @throws  Exception
   */
  public function uninstall($parent)
  {
    $this->uninstallPlugins($parent);
  }

  /**
   * Uninstall the package plugins
   *
   * @param   JInstallerAdapter  $parent  class calling this method
   *
   * @return  void
   */
  protected function uninstallPlugins($parent)
  {
    // Required objects
    $installer = $this->getInstaller();
    $manifest = $this->getManifest($parent);
    $nodes = $manifest->plugins->plugin;

    if (empty($nodes)) {
      return;
    }

    foreach ($nodes as $node) {
      $extName = $node->attributes()->name;
      $extGroup = $node->attributes()->group;
      $result = 0;
      $extId = $this->searchExtension($extName, 'plugin', null, $extGroup);

      if ($extId) {
        $result = $installer->uninstall('plugin', $extId);
      }

      // Store the result to show install summary later
      $this->_storeStatus('plugins', [
        'name' => $extName,
        'group' => $extGroup,
        'result' => $result,
      ]);
    }
  }

  /**
   * Search a extension in the database
   *
   * @param   string  $element  Extension technical name/alias
   * @param   string  $type     Type of extension (component, file, language, library, module, plugin)
   * @param   string  $state    State of the searched extension
   * @param   string  $folder   Folder name used mainly in plugins
   *
   * @return  integer           Extension identifier
   */
  protected function searchExtension($element, $type, $state = null, $folder = null)
  {
    $db = JFactory::getDBO();
    $query = $db
      ->getQuery(true)
      ->select($db->qn('extension_id'))
      ->from($db->qn('#__extensions'))
      ->where($db->qn('type') . ' = ' . $db->q($type))
      ->where($db->qn('element') . ' = ' . $db->q($element));

    if (null !== $state) {
      $query->where($db->qn('state') . ' = ' . (int) $state);
    }

    if (null !== $folder) {
      $query->where($db->qn('folder') . ' = ' . $db->q($folder));
    }

    return $db->setQuery($query)->loadResult();
  }

  /**
   * Store the result of trying to install an extension
   *
   * @param   string  $type    Type of extension (libraries, modules, plugins)
   * @param   array   $status  The status info
   *
   * @return void
   */
  private function _storeStatus($type, $status)
  {
    // Initialise status object if needed
    if (null === $this->status) {
      $this->status = new stdClass();
    }

    // Initialise current status type if needed
    if (!isset($this->status->{$type})) {
      $this->status->{$type} = [];
    }

    // Insert the status
    $this->status->{$type}[] = $status;
  }

  /**
   * Shit happens. Patched function to bypass bug in package uninstaller
   *
   * @param   JInstallerAdapter  $parent  Parent object
   *
   * @return  void
   */
  protected function loadManifest($parent)
  {
    $element = strtolower(str_replace('InstallerScript', '', get_called_class()));
    $elementParts = explode('_', $element);

    // Type not properly detected or not a package
    if (count($elementParts) !== 2 || strtolower($elementParts[0]) !== 'pkg') {
      $this->manifest = $parent->getManifest();

      return;
    }

    $rootPath = $parent->getParent()->getPath('extension_root');
    $manifestPath = dirname($rootPath);
    $manifestFile = $manifestPath . '/' . $element . '.xml';

    // Package manifest found
    if (file_exists($manifestFile)) {
      $this->manifest = new SimpleXMLElement($manifestFile);

      return;
    }

    $this->manifest = $parent->getManifest();
  }

  /**
   * Method to run after an install/update/uninstall method
   *
   * @param   string             $type    type of change (install, update or discover_install)
   * @param   JInstallerAdapter  $parent  class calling this method
   *
   * @return  boolean
   */
  public function postflight($type, $parent)
  {
    if ($type === 'uninstall') {
      return true;
    }

    Factory::getApplication()->redirect(
      'index.php?option=com_config&view=component&component=com_aesirx_bi'
    );

    return true;
  }
}
