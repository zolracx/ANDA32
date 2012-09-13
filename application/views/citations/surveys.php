<?php
	/*
	* A list of all surveys to be attached to a citation
	*
	*/
?>
<style>
.custom-short-font{font-size:11px;}
</style>
<?php if ($surveys): ?>

<table class="grid-table custom-short-font" cellpadding="0" cellspacing="0">
	<tr class="header">        
	    <th>&nbsp;</th>
		<th>Title</th>
		<th>Country</th>
	</tr>
<?php foreach ($surveys as $survey):?>
	<tr align="left">
    	<td><input class="chk" type="checkbox" name="sid[]" value="<?php echo $survey['id'];?>" <?php echo (in_array($survey['id'],$related_surveys) ? 'checked="checked"' : ''); ?>/></td>
		<td><?php echo $survey['titl'];?></td>
		<td><?php echo $survey['nation'];?></td>
	</tr>
<?php endforeach; ?>
</table>    

<?php else:?>
No records found.
<?php endif;?>