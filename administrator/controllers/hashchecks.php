<?php
/**
 * @version    1.0
 * @package    MD5 Hash Check
 * @author     info@joomdev.com
 * @copyright  2016 www.joomdev.com
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */


// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Hashchecks list controller class.
 *
 * @since  1.6
 */
class HashcheckControllerHashchecks extends JControllerAdmin
{
	/**
	 * Method to clone existing Hashchecks
	 *
	 * @return void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		Jsession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('COM_HASHCHECK_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Jtext::_('COM_HASHCHECK_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_hashcheck&view=hashchecks');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'hashcheck', $prefix = 'HashcheckModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = JFactory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	public function hashfilecheck(){
		$count = JRequest::getvar('count');
		$total = JRequest::getvar('total');
		$t_file = str_replace('\\', '/',JPATH_ROOT);
		$db = JFactory::getDbo();		
		$t='';
		if($count < $total){
			$tpath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator/components/com_hashcheck/dir.txt';
			$tpath = str_replace('\\', '/', $tpath);
			$myfile = fopen($tpath, "r+");
			$session = JFactory::getSession();
			$fileseekpointer = $session->get('fileseekpointer');
			fseek($myfile,$fileseekpointer);
			for($i=0;$i<50;$i++){
				$tempfile = fgets($myfile);
				if($tempfile)
				$file[] =  trim($tempfile);			
			}
			$files = implode("','",$file);
			$sql = "SELECT `file`,`hash` FROM `#__orgfile` WHERE `file` IN ('".$files."')";
			$db->setQuery($sql);
			$results = $db->loadAssocList();
			$sql = "UPDATE #__orgfile SET `status` = 1 WHERE `file` IN ('".$files."')";
			$db->setQuery($sql);
			$db->query();
			$orgFile = array();
			$error = array();
			$missing = array();
			foreach($results as $result){
				$orgFile[$result['file']] = $result['hash'];
			}
			foreach($file as $t){
			if($t){
				$content = file_get_contents($t);
				$fhash = $this->getFileHash($t);
				if((!empty($orgFile[$t]))&&($fhash != $orgFile[$t])){
					$errt = str_replace($t_file,"",$t);
					$error[$errt] = Jtext::_('COM_HASHCHECK_ERROR_CORRUPTED');
				}
				else if(empty($orgFile[$t])){
					 $type = explode('.', $t);
					  if((!preg_match('#JPATH_BASE#', $content) && !preg_match('#_JEXEC#', $content)) && ($type[sizeof($type) - 1] == 'php')){
						if(!preg_match('#<?php#', $content)){ //Ignoring non-php files.
						  continue;
						}

						//check if file contains includes with variables in it
						if(
						  !preg_match('#require(\s)?\((.*)?\$(.*)?\)#', $content) &&
						  !preg_match('#require_once(\s)?\((.*)?\$(.*)?\)#', $content) &&
						  !preg_match('#include(\s)?\((.*)?\$(.*)?\)#', $content) &&
						  !preg_match('#include_once(\s)?\((.*)?\$(.*)?\)#', $content)
						){
						  //if not, skip this one as well
						  continue;
						}
						$errt = str_replace($t_file,"",$t);
						$error[$errt] = Jtext::_('COM_HASHCHECK_ERROR_VALID');
					  }
				}			
			$fileseekpointer = ftell($myfile);
			$session->set('fileseekpointer',$fileseekpointer);
			}
			}
		}
		else if($count + 50 >= $total){
			$sql = "SELECT `file` FROM `#__orgfile` WHERE `status` = 0";
			$db->setQuery($sql);
			$errors = $db->loadColumn();
			foreach($errors as $err){
				$err = str_replace($t_file,"",$err);
				$missing[$err] = Jtext::_('COM_HASHCHECK_ERROR_MISSING');
			}
			$sql = "UPDATE #__orgfile SET `status` = 1";
			$db->setQuery($sql);
			$db->query();
		}
		 $t = '<b>'.Jtext::_('COM_HASHCHECK_CURRENT_FILE').'</b>'.str_replace($t_file,"",$t);
		 
		$output = array('file'=>$t,'error'=>$error,"missing" => $missing);
		echo json_encode($output);
		die();
	}
	
function getFileHash($file){
  $content = file_get_contents($file);
  $content = str_replace(array("\n", "\r"), "", $content);
  return md5($content);
}

}
