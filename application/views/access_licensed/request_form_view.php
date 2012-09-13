<?php
/**
* Form for collecting data for - Licensed Data Requests
*
*/
?>
<style>
.public-use td{border:1px solid gainsboro;padding:5px;}
.required{color:red;}
</style>

<?php
//options for the org_type
$options_org_type=array(
	t('Line ministry/public administration')=>t('Line ministry/public administration'),
	t('University')=>t('University'),
	t('Research centre')=>t('Research centre'),
	t('Private company')=>t('Private company'),
	t('International organization')=>t('International organization'),
	t('Non-governmental agency (national)')=>t('Non-governmental agency (national)'),
	t('Non-governmental agency (international)')=>t('Non-governmental agency (international)'),
	t('Other')=>t('Other')
	);	
	
$options_datamatching=array(
	0=>'No',
	1=>'Yes'
	);
?>

<p>
    <p><?php echo t('dear').' '. $fname. ' '. $lname; ?>,</p>
    <p><?php echo sprintf(t('received_licensed_request'),$this->config->item('website_title'));?></p>
    <p><?php echo t('to_view_request_status');?><br/>
    <b><?php echo site_url(); ?>/access_licensed/track/<?php echo $id; ?></b></p>
    <p><?php echo t('for_further_information');?> <?php echo $this->config->item('website_webmaster_email'); ?></p>
    <p>--<br/><?php echo $this->config->item('website_title');?></p>
    <br/>
    <br/>
    <br/>
    <br/>
</p>

<h2 class="page-title"><?php echo t('application_access_licensed_dataset');?></h2>
  <table class="public-use" border="0" width="100%" style="border-collapse:collapse;border:1px solid gainsboro;">
  	<tr>
    	<td colspan="2" class="note">
        <div><?php echo t('info_kept_confidential');?></div>
        </td>
    </tr>
    <tr class="border">
      <td width="200px"><?php echo t('first_name');?></td>
      <td><?php echo get_form_value('fname',isset($fname) ? $fname : ''); ?></td>
    </tr>
    <tr class="border">
      <td><?php echo t('last_name');?></td>
      <td><?php echo get_form_value('lname',isset($lname) ? $lname: ''); ?></td>
    </tr>
    <tr  class="border">
      <td><?php echo t('organization');?></td>
      <td><?php echo get_form_value('organization',isset($organization) ? $organization : ''); ?></td>
    </tr>
    <tr class="border">
      <td><?php echo t('email');?></td>
      <td><?php echo get_form_value('email',isset($email) ? $email : ''); ?></td>
    </tr>
    <tr class="border">
      <td><?php echo t('dataset_requested');?>:</td>
      <td><div style="color:maroon;font-size:12px;"><?php echo get_form_value('survey_id',isset($survey_id) ? $survey_id : ''); ?> - <?php echo get_form_value('survey_title',isset($survey_title) ? $survey_title : ''); ?> <br/><?php echo get_form_value('proddate',isset($proddate) ? $proddate : ''); ?></div></td>
    </tr>
    <tr class="border">
      <td colspan="2">
				<?php echo t('filled_lead_research');?><br /><br />
		      	<span class="required">*</span> <?php echo t('receiving_org');?>
      			<input type="text" id="org_rec" name="org_rec"   value="<?php //echo $data['org_rec']; ?>" style="width:200px" maxlength="100" /> <br />
		      	<?php echo t('rec_org_refers');?></p>
      </td>      
    </tr>
    <tr class="border" valign="top">
    <td><?php echo t('org_type');?></td>
    <td><?php echo form_dropdown('org_type', $options_org_type, isset($org_type) ? $org_type : '');?>
      <br />
      <br />
<?php print t('other');?><br/>
<input type="text" id="orgtype_other" name="orgtype_other"   value="<?php echo get_form_value('orgtype_other',isset($orgtype_other) ? $orgtype_other : ''); ?>" style="width:200px" maxlength="100" /></td>
  </tr>
  <tr class="border">
    <td><span class="required">*</span> <?php echo t('post_add');?></td>
    <td><input type="text" id="address" name="address"   value="<?php echo get_form_value('address',isset($address) ? $address : ''); ?>" style="width:200px" maxlength="100" /></td>
  </tr>
  <tr class="border">
    <td><p>      <span class="required">*</span> <?php echo t('telephone');?><br />
    </p></td>
    <td><input type="text" id="tel" name="tel"   value="<?php echo get_form_value('tel',isset($tel) ? $tel : ''); ?>" style="width:200px" maxlength="100" /></td>
  </tr>
  <tr class="border">
    <td><?php echo t('fax');?></td>
    <td><input type="text" id="fax" name="fax"   value="<?php echo get_form_value('fax',isset($fax) ? $fax : ''); ?>" style="width:200px" maxlength="100" /></td>
  </tr>
  <tr class="border">
    <td colspan="2"><div style="font-weight:bold;"><span class="required">*</span> <?php print t('intended_use');?><br /> <br />
    </div>
      <div style="font-style:italic"> <?php print t('provide_short_desc');?> </div>
    <textarea id="datause" name="datause" style="width:98%" rows="10"><?php echo get_form_value('datause',isset($datause) ? $datause : ''); ?></textarea></td>
  </tr>
  <tr class="border">
    <td colspan="2"><div style="font-weight:bold;"><?php print t('expected_output');?></div> 
    <textarea id="outputs" name="outputs" style="width:98%" rows="10"><?php echo get_form_value('outputs',isset($outputs) ? $outputs : ''); ?></textarea>     </td>
  </tr>
  <tr class="border">
    <td><strong><?php print t('expected_completion');?></strong> </td>
    <td><input type="text" id="compdate" name="compdate"   value="<?php echo get_form_value('compdate',isset($compdate) ? $compdate : ''); ?>" style="width:200px" maxlength="100" /></td>
  </tr>
  <tr class="border">
    <td colspan="2">
    	<div style="font-weight:bold;"><?php print t('data_matching');?></div>
        <?php print t('merge_dataset');?>    
        <?php echo form_dropdown('datamatching', $options_datamatching, isset($datamaching) ? $datamatching : '');?>
      </td>
  </tr>
  <tr class="border">
    <td colspan="2"><?php print t('other_data_merge');?><br />
    <textarea id="mergedatasets" name="mergedatasets" style="width:98%" rows="10"><?php echo get_form_value('mergedatasets',isset($mergedatasets) ? $mergedatasets : ''); ?></textarea></td>
  </tr>
  <tr class="border">
    <td colspan="2"><strong><?php print t('research_team');?></strong><br />
      <br />
      <?php print t('provide_names');?><br/>
    <textarea id="team" name="team" style="width:98%" rows="10"><?php echo get_form_value('team',isset($team) ? $team : ''); ?></textarea></td>
  </tr>
  <tr class="border">
    <td colspan="2"><strong><?php print t('ident_needed');?></strong><br />
      <br/>
	<?php print t('da_website');?>
    <br/><br/>

<?php print t('this_request');?><span class="required">*</span> <br/>
<input type="radio" name="dataset_access" id="access_whole" value="whole" <?php echo get_form_value('dataset_access',isset($dataset_access) ? $dataset_access: '')=='whole' ? 'checked="checked"' : ''; ?> />
<label for="access_whole"><?php print t('whole_dataset');?></label><br/>
<input type="radio" name="dataset_access" id="access_subset" value="subset" <?php echo get_form_value('dataset_access',isset($dataset_access) ? $dataset_access: '')=='subset' ? 'checked="checked"' : ''; ?>/>
<label for="access_subset"><?php print t('subset_data');?></label></td>
  </tr>
  <tr class="border">
    <td colspan="2">
        <div>
          <div style="margin-top:5px;font-weight:bold;"><?php print t('data_access_agreement');?></div>
          <?php print t('agreement_text');?>
        </div></td>
  </tr>
  </table>