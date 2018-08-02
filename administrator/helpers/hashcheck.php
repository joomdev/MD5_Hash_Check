<?php
/**
 * @version    1.0
 * @package    MD5 Hash Check
 * @author     info@joomdev.com
 * @copyright  2016 www.joomdev.com
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Hashcheck helper.
 *
 * @since  1.6
 */
class HashcheckHelpersHashcheck
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
				JHtmlSidebar::addEntry(
			JText::_('COM_HASHCHECK_TITLE_HASHCHECKS'),
			'index.php?option=com_hashcheck&view=hashchecks',
			$vName == 'hashchecks'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_hashcheck';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}


class HashcheckHelper extends HashcheckHelpersHashcheck
{

}
