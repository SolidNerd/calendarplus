<?php
//Prerendering for iCalendar file
$bFile = false;
if($_['isDragged'] === 'true'){
	$file = explode(',',  $_['filename']);
	$file = end($file);
	$file = base64_decode($file);
	$bFile = true;
}elseif($_['isSub'] === 'true'){
	$bFile = false;
}else{
	if($_['filename']!=''){	
		$file = \OC\Files\Filesystem::file_get_contents($_['path'] . '/' . $_['filename']);
		$bFile = true;
	}	
}

if(!$bFile && $_['isSub'] === 'false') {
	OCP\JSON::error(array('error'=>'404'));
}
if($bFile){
	$import = new OCA\CalendarPlus\Import($file);
	$import->setUserID(OCP\User::getUser());
	$newcalendarname = OCP\Util::sanitizeHTML($import->createCalendarName());
	$guessedcalendarname = OCP\Util::sanitizeHTML($import->guessCalendarName());
	$calendarcolor = OCP\Util::sanitizeHTML($import->createCalendarColor());
}

//loading calendars for select box
$calendar_options = OCA\CalendarPlus\Calendar::allCalendars(OCP\USER::getUser());


?>
<div id="calendar_import_dialog" title="<?php p($l->t("Import a calendar file"));?>">
<div class="importdiv">
	<input id="calendar_import_url"  type="text" placeholder="<?php p($l->t('Url to subscribed calendar')); ?>" value="">
	<button id="checkurl" class="icon-checkmark"></button>
	<div id="importmsg"></div>
	</div>
<div id="calendar_import_form">

	<form>
		<input type="hidden" id="calendar_import_filename" value="<?php p($_['filename']);?>">
		<input type="hidden" id="calendar_import_path" value="<?php p($_['path']);?>">
		<input type="hidden" id="calendar_import_progresskey" value="<?php p(rand()) ?>">
		<input type="hidden" id="calendar_import_availablename" value="<?php p($newcalendarname) ?>">
		<div class="import-select-cal">
		<div id="calendar_import_form_message"><?php p($l->t('Please choose a calendar')); ?></div>
		<select style="width:98%;" id="calendar_import_calendar" name="calendar_import_calendar">
		<?php
		
		for($i = 0;$i<count($calendar_options);$i++) {
			if(!$calendar_options[$i]['issubscribe']){	
				$calendarChoose[]= array(
						'id' => $calendar_options[$i]['id'],
						'displayname' => $calendar_options[$i]['displayname']
						);
			}
		}
		$calendarChoose[] = array('id'=>'newcal', 'displayname'=>$l->t('create a new calendar'));
		
		print_unescaped(OCP\html_select_options($calendarChoose, $calendarChoose[0]['id'], array('value'=>'id', 'label'=>'displayname')));
		?>
		</select>
		</div>
		<div id="calendar_import_newcalform">
	
			<input id="calendar_import_newcalendar_color" class="color-picker" type="hidden" size="6" value="<?php p(substr($calendarcolor,1)); ?>">
			<input id="calendar_import_newcalendar"  type="text" placeholder="<?php p($l->t('Name of new calendar')); ?>" value="<?php p($guessedcalendarname) ?>"><br>
			
			<!--<input id="calendar_import_generatename" type="button" class="button" value="<?php p($l->t('Take an available name!')); ?>"><br>-->
			<div  id="calendar_import_mergewarning" class="hint"><?php p($l->t('A Calendar with this name already exists. If you continue anyhow, these calendars will be merged.')); ?></div>
		<br style="clear:both;" />
		</div>
		<div id="calendar_import_checkbox">
		<input type="checkbox" id="calendar_import_overwrite" value="1">
		<label for="calendar_import_overwrite"><?php p($l->t('Remove all events from the selected calendar')); ?></label>
		<br>
		</div>
		<input id="calendar_import_submit" type="button" class="button" value="&raquo; <?php p($l->t('Import')); ?> &raquo;" id="startimport">
	<form>
</div>
<div id="calendar_import_process">
	<div id="calendar_import_process_message"></div>
	<div  id="calendar_import_progressbar"></div>
	<br>
	<div id="calendar_import_status" class="hint"></div>
	<br>
	<input id="calendar_import_done" type="button" value="<?php p($l->t('Close Dialog')); ?>">
</div>
</div>
