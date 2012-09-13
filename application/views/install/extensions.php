<?php
	$this->load->database();

	$extensions=array('xslt','xml');
	if(phpversion() >= 5) {
		$extensions=array(
					'xsl'=>'',
					'xml'=>'',
					'simplexml'=>'',
					"zip"=>'<span class="optional">'.t('optional').'</span>',
					"mbstring"=>'<span class="optional">'.t('optional').'</span>'
					);
	}
	
	$dbextensions=array($this->db->dbdriver);
	
	$yes='<span class="green"><img src="images/tick.png"/></span>';
	$no='<span class="red" style="background:none;color:red;"><img src="images/icon_cancel.gif" alt="X"/></span>';
	
	echo '<h1>'.t('required_php_extensions').'</h1>';
	echo '<table cellpadding="0" cellspacing="0" class="grid-table">';
	echo '<tr class="header">';
	echo '<th>'.t('extensions').'</th>';
	echo '<th>'.t('enabled').'</th>';
	echo '</tr>';
	foreach ($extensions as $ex=>$value){
		echo '<tr>';
		echo '<td>'."$ex $value".'</td>';
		echo '<td>';
		echo extension_loaded($ex) ? $yes: $no;
		echo '</td>';
		echo '</tr>';
	}
	foreach ($dbextensions as $ex){
		echo '<tr>';
		echo '<td>'.$ex.'</td>';
		echo '<td style="width:50px">';
		if ($this->db->dbdriver==$ex){
			if (extension_loaded($ex)!=1){
				echo '<span style="color:red">';
				echo sprintf(t('extension_not_enabled'),$ex);
				echo '</span>';
			}
			else{ echo $yes;}
		}
		else{
			echo extension_loaded($ex) ? $yes: $no;
		}	
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
?>