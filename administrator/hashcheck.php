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

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_hashcheck'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Hashcheck', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('Hashcheck');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
