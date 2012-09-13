<!--<table width="100%" class="catalog-page-title" cellpadding="0" cellspacing="0" border="1">-->
<!--<tr valign="baseline">-->
<!--<td><h1><?php //echo t('title_data_catalog');?></h1></td>-->
<!--<td align="right">-->
<!--<div class="page-links">-->
<!--	<a id="link_export" title="<?php //echo t('link_export_search');?>" href="<?php //echo site_url();?>/catalog/export"><img src="images/export.gif" border="0" alt="Export"/></a>-->
<!--    <a title="<?php //echo t('rss_feed');?>" href="<?php //echo site_url();?>/catalog/rss" target="_blank"><img src="images/rss_icon.png" border="0" alt="RSS"/></a>-->
<!--</div>-->
<!--</td>-->
<!--</tr>-->
<!--</table>-->

<form name="search_form" id="search_form" method="get" autocomplete = "off">
<input type="hidden" id="view" name="view" value="<?php echo (isset($this->view) && $this->view=='v') ? 'v': 's'; ?>"/>
<input type="hidden" id="ps" name="ps" value="<?php echo $this->limit; ?>"/>
<div id="accordion" > 
	<?php if ($this->regional_search=='yes'):?>
	<h3><a href="#"><?php echo t('filter_by_country');?><span id="selected-countries" style="font-size:11px;padding-left:10px;"></span></a></h3> 
	<div id="countries-list" style="height:150px;font-size:11px;"><div style="text-align:right;">
    	<a  href="#" onclick="select_countries('all');return false;"><?php echo t('link_select_all');?></a> | 
        <a  href="#" onclick="select_countries('none');return false;"><?php echo t('link_clear');?></a> | 
        <a  href="#" onclick="select_countries('toggle');return false;"><?php echo t('link_toggle');?></a>
    </div> 
		<?php foreach($countries as $country): ?>
        	<div class="country">
                <input class="chk-country" type="checkbox" name="country[]" 
                	value="<?php echo form_prep($country['nation']); ?>" 
                    id="c-<?php echo form_prep($country['nation']); ?>"
                    <?php if($this->country!='' && in_array($country['nation'],$this->country)):?>
                    checked="checked"
                    <?php endif;?>
                 />
                <label for="c-<?php echo form_prep($country['nation']); ?>">
                	<?php echo substr($country['nation'],0,25); ?> (<?php echo $country['surveys_found']; ?>)
                </label>
            </div>
        <?php endforeach;?>
	</div>
    <?php endif;?>
    <?php if ($this->topic_search=='yes'):?>
    	<!-- topics -->
        <h3><a href="#"><?php echo t('filter_by_topic');?> <span id="selected-topics" style="font-size:11px;padding-left:10px;"></span></a></h3> 
        <div id="topics-list" style="height:150px;font-size:11px;"> 
            <div style="text-align:right;">
            	<a  href="#" onclick="select_topics('all');return false;"><?php echo t('link_select_all');?></a> | 
                <a  href="#" onclick="select_topics('none');return false;"><?php echo t('link_clear');?></a>
            </div>
            <?php echo $topics_formatted;?>
            <br style="clear:both;">
        </div>
        <!-- end topics -->    
    <?php endif;?>
    <?php if (isset($this->collection_search) && $this->collection_search=='yes'):?>
    	<!-- collections -->
        <h3><a href="#"><?php echo t('filter_by_collection');?> <span id="selected-collections" style="font-size:11px;padding-left:10px;"></span></a></h3> 
        <div id="collection-list" style="height:150px;font-size:11px;"> 
            <div style="text-align:right;">
            	<a  href="#" onclick="select_collection('all');return false;"><?php echo t('link_select_all');?></a> | 
                <a  href="#" onclick="select_collection('none');return false;"><?php echo t('link_clear');?></a>
            </div>
            <?php echo $collections_formatted;?>
            <br style="clear:both;">
        </div>
        <!-- end collections -->    
    <?php endif;?>
</div>


	<div  class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" style="padding:5px;">
   	<?php if ($this->config->item("year_search")=='yes'):?>
        <input type="hidden"/>
        <div style="margin-left:35px;font-style:normal;color:black;font-weight:normal;margin-bottom:5px;clear:both;">
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td><?php echo t('show_studies_conducted_between');?>&nbsp;</td>
                <td><input type="hidden"/><?php echo form_dropdown('from', $years, (isset($this->from) ? $this->from : ''), 'id="from"'); ?></td>
                <td>&nbsp;<?php echo t('and');?>&nbsp;</td>
                <td><?php echo form_dropdown('to', $years, (isset($this->to) && $this->to!='') ? $this->to: end($years),'id="to"'); ?></td>
            </tr>
        </table>
        </div>
	<?php endif;?>	
		<div class="variable-search">
    		<?php echo t('find');?> <input maxlength="100" type="text" name="sk" value="<?php echo isset($this->sk) ? $this->sk : '' ; ?>" style="margin-left:10px;margin-right:5px;width:65%;"/> <?php echo t('in_study_description');?>
		</div>    
        
        <div class="variable-search" style="margin-top:5px;">
        	<?php echo t('find');?> <input maxlength="100"  type="text" name="vk" value="<?php echo isset($this->vk) ? $this->vk : '' ; ?>" style="margin-left:10px;margin-right:5px;width:65%;"/> <?php echo t('in_variable_description');?>

			<div style="margin-left:37px;font-size:11px;margin-top:5px;">
            <?php echo t('variable_description_includes');?> 
			<input type="checkbox" name="vf[]" id="name" value="name"  <?php if(isset($this->vf) && $this->vf!='' && in_array('name',$this->vf)){ echo 'checked="checked"';}?>/><label for="label"><?php echo t('name');?> </label>
            <input type="checkbox" name="vf[]" id="label" value="labl" <?php if(isset($this->vf) && $this->vf!='' && in_array('labl',$this->vf)){ echo 'checked="checked"';}?>/><label for="label"><?php echo t('label');?> </label>
            <input type="checkbox" name="vf[]" id="question" value="qstn"  <?php if(isset($this->vf) && $this->vf!='' && in_array('qstn',$this->vf)){ echo 'checked="checked"';}?>/><label for="question"><?php echo t('question');?> </label>
            <input type="checkbox" name="vf[]" id="categories" value="catgry"  <?php if(isset($this->vf) && $this->vf!='' && in_array('catgry',$this->vf)){ echo 'checked="checked"';}?>/><label for="categories"><?php echo t('classification');?> </label>
			</div>                    
        </div>

		<div style="text-align:right;margin-top:-15px;" class="search-buttons">
        	<input class="button" type="submit" id="btnsearch" name="search" value="<?php echo t('search');?>"/>
            <input class="btn-cancel" type="button" id="reset" name="reset" onclick="window.location.href='<?php echo site_url();?>/catalog/?reset=reset'"  value="<?php echo t('reset');?>"/>
		</div> 
    </div>

	<div id="surveys"><?php echo $search_result; ?></div>
    
</form> 
 

<script type="text/javascript">
//translations	
var i18n=
{
'searching':"<?php echo t('js_searching');?>",
'loading':"<?php echo t('js_loading');?>",
'invalid_year_range_selected':"<?php echo t('js_invalid_year_range_selected');?>",
'topic_selected':"<?php echo t('js_topic_selected');?>",
'topics_selected':"<?php echo t('js_topics_selected');?>",
'collection_selected':"<?php echo t('js_collection_selected');?>",
'collections_selected':"<?php echo t('js_collections_selected');?>",
'country_selected':"<?php echo t('js_country_selected');?>",
'countries_selected':"<?php echo t('js_countries_selected');?>",
'cancel':"<?php echo t('cancel');?>"
};

//min/max years
var years = {'from': '<?php reset($years);echo current($years); ?>', 'to': '<?php echo end($years); ?>'}; 
</script>