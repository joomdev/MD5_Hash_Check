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
$path = JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR;
$path = str_replace('\\', '/', $path);
$myfile = '';
$count = getDirFile();
$db = JFactory::getDbo();
$sql = "UPDATE #__orgfile SET `status` = 0";
$db->setQuery($sql);
$db->query();
$sql = "SELECT * FROM #__orgfile LIMIT 0,5";
$session = JFactory::getSession();
$session->set('fileseekpointer','0');
$db->setQuery($sql);
$isHashfile = 1;
if(!$db->loadResult())	
	$isHashfile = getOrgFile();


?>
<div class="pregress_container" style="display:none";>		
	<p><?php echo '<b>'.Jtext::_('COM_HASHCHECK_IN_PROGRESS').'</b> <img src="components/com_hashcheck/assets/images/loading_bubble.svg"><br><br>';?></p>
	<p class="file_name"><?php echo '<b>'.Jtext::_('COM_HASHCHECK_CURRENT_FILE').'</b>'.'index.php';?></p>
	<div id="progress" class="progress" style="display:block">
		<h3 class="message_count">1/<?php echo $count;?></h3>
		<div class="progress-bar progress-bar-success"></div>
	</div>
</div>
<div style="text-align: center;">
	<?php
	if($isHashfile == 0){?>
	<div class="alert alert-warning">
		<div><p style="width: 80%;margin: auto;padding-bottom: 20px;"><?php echo JText::_("COM_HASHCHECK_FILE_MISSING");?></p></div>
	</div>

	<?php }
	else{
	?>
	<div><p style="width: 80%;margin: auto;padding-bottom: 20px;"><?php echo JText::_("COM_HASHCHECKS_TEXT");?></p></div>
	<button class="btn btn-success start-btn"><?php echo JText::_('COM_HASHCHECK_START_BUTTON');?></button>
		<?php } ?>
</div>
<table width="100%" class="table table-striped table-preview" style="display:none;" >
	 <thead>
		  <tr>
			<th style="text-align: left;"><?php echo JText::_('COM_HASHCHECK_COL_ERROR');?></th>
			<th style="text-align: left;"><?php echo JText::_('COM_HASHCHECK_COL_FILENAME');?></th>
			<th style="text-align: left;"><?php echo JText::_('COM_HASHCHECK_COL_MESSAGE');?></th>
		  </tr>
	  </thead>
	  <tbody class="response">
	  </tbody>
</table>	
	  
<script>
total = '<?php echo $count;?>';
url ='<?php echo JURI::root();?>administrator/index.php?option=com_hashcheck&task=hashchecks.hashfilecheck&tmpl=component';
//checkajax(0,total);
window.error_count = 0;
jQuery(".start-btn").on('click',function(){
	jQuery(this).parent("div").hide();	
	jQuery(".pregress_container").show();
	checkajax(0,total);
});
function checkajax(count,total){
	jQuery.ajax({method:'POST', url:url, data:{'count':count,'total':total}, success:function(response){
		newcount = count + 50;
		response = JSON.parse(response);
		error = response.error;
		missing = response.missing;
		var restr = '';
		for( var fname in response.missing ){
			jQuery(".table-preview").show();
			window.error_count = 1;
			restr = '<tr><td class="error">File Missing</td><td>'+ fname +'</td><td>' + missing[fname] + '</td></tr>';
			jQuery(".response").prepend(restr);
		}
		
		for( var fname in response.error ){
			jQuery(".table-preview").show();
			window.error_count = 1;
			restr = '<tr><td class="warning">Warning</td><td>'+ fname +'</td><td>' + error[fname] + '</td></tr>';
			jQuery(".response").prepend(restr);
		}
		
		
		jQuery(".file_name").html(response.file);
		if(newcount < total){
			percent = ( newcount / total ) * 100;
			jQuery(".progress-bar").css('width',percent+'%');
			jQuery(".message_count").html(newcount + '/' + total);			
			checkajax( newcount , total ) 
		}
		else if(count < total){
			jQuery(".progress-bar").css('width','100%');
			count = count + 50;
			jQuery(".message_count").html(total + '/' + total);
			checkajax( total , total );
			jQuery(".pregress_container").hide();
			
		}
		else if(count >= total){
			if( window.error_count <= 0){
				jQuery(".table-preview").hide();
				jQuery(".table-preview").after('<div class="alert alert-success"><?php echo JText::_('COM_HASHCHECK_SUCCESS');?></div>');
				
			}
		}
	
	}})
}
		
</script>
<?php

function getDirFile(){
	$tpath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator/components/com_hashcheck/dir.txt';
	$tpath = str_replace('\\', '/', $tpath);
	$myfile = fopen($tpath, "w");	
	fwrite($myfile, "\n");
	$path = JPATH_ROOT;
	$path = str_replace('\\', '/', $path);
	$count = listFolderFiles($path,$myfile);
	fclose($myfile);
	return $count;
}
function listFolderFiles($dir,$myfile){
    $ffs = scandir($dir);
	static $count = 0;
    foreach($ffs as $ff){
        if($ff != '.' && $ff != '..' ){
			if(!is_dir($dir.'/'.$ff)){
				$count++;				
				fwrite($myfile, $dir.'/'.$ff."\n");
			}
			else{
				listFolderFiles($dir.'/'.$ff,$myfile);
			}
        }
    }
	return $count;
}
function getOrgFile(){
	$path = JPATH_ROOT . DIRECTORY_SEPARATOR;
	$path = str_replace('\\', '/', $path);
	$db = JFactory::getDbo();
	$sql = "TRUNCATE TABLE #__orgfile";
	$db->setQuery($sql);
	$db->query();
	$orig = array();
	$txt = "INSERT INTO #__orgfile(`id`, `file`, `hash`, `status`) VALUES ";
	$orig_c = file($path.'administrator/components/com_hashcheck/hash/'.JVERSION.'/hash.txt');
	for($i=0,$n=count($orig_c);$i<$n;$i++){
		$line = explode("\t", $orig_c[$i]);
		if($i>0)
		$txt .= ",('',".$db->quote($path . $line[0]).",".$db->quote(trim($line[1])).",'0')";
		else{
			$txt .= "('',".$db->quote($path . $line[0]).",".$db->quote(trim($line[1])).",'0')";
		}		
	}	
	$db->setQuery($txt);
	$db->query();
	if(count($orig_c)>1){
		return true;
	}
	else{
		return false;
	}
	
}
function dirfile(){
	
}	

?>
<style>
.warning {color: orange;font-style: italic;}
.message_count{position: absolute;display: inline-block;width: 96%;text-align: center;margin-top: auto;z-index: 99;}
.progress {height: 25px;margin-bottom: 20px;overflow: hidden;background-color: #f5f5f5;border-radius: 4px;-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);box-shadow: inset 0 1px 2px rgba(0,0,0,.1);}
.progress-bar-success {background-color: #5cb85c;}
.progress-bar {float: left;width: 0;height: 23px;position: absolute;font-size: 12px;line-height: 20px;color: #fff;text-align: center;background-color: #428bca;-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition: width .6s ease;-o-transition: width .6s ease;transition: width .6s ease;max-width: 98%;}
.error {color: red;font-weight: bold;}
</style>