<?php
/**
 * @version    1.0
 * @package    MD5 Hash Check
 * @author     info@joomdev.com
 * @copyright  2016 www.joomdev.com
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Hashcheck', JPATH_COMPONENT);
JLoader::register('HashcheckController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Hashcheck');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
