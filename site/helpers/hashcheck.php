<?php
/**
 * @version    1.0
 * @package    MD5 Hash Check
 * @author     info@joomdev.com
 * @copyright  2016 www.joomdev.com
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class HashcheckFrontendHelper
 *
 * @since  1.6
 */
class HashcheckHelpersHashcheck
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_hashcheck/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_hashcheck/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'HashcheckModel');
		}

		return $model;
	}
}
