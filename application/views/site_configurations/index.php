<style>
.input-flex{width:200px;display:inline;}
.field{margin-bottom:15px;clear:both;}
label{display:block;float:left;width:200px;}
.field-note{font-style:italic;padding-left:5px;color:gray;}
h2{font-size:1.2em;font-weight:bold;border-bottom:1px solid gainsboro;padding-bottom:2px;margin-bottom:10px;}
.field-expanded,.always-visible{background-color:#F8F8F8;border:1px solid gainsboro;margin-top:5px;margin-bottom:10px;margin-right:8px;}
.always-visible{padding:10px;}
.field-expanded .field, .always-visible .field {padding:5px;}
.field-expanded legend, .field-collapsed legend, .always-visible legend{background:white;padding-left:5px;padding-right:5px;font-weight:bold; cursor:pointer;}
.field-collapsed{background:none; border:0px;border-top:1px solid gainsboro;margin-top:5px;margin-bottom:5px;}
.field-collapsed legend {background-image:url(images/next.gif); background-position:left top; padding-left:20px;background-repeat:no-repeat;}
.field-collapsed .field{display:none;}
.field-expanded .field label, .always-visible label{font-weight:normal;}

</style>
<div class="content-container">
<h1 class="page-title"><?php echo t('site_configurations');?></h1>

<?php if (validation_errors() ) : ?>
    <div class="error">
	    <?php echo validation_errors(); ?>
    </div>
<?php endif; ?>

<?php $error=$this->session->flashdata('error');?>
<?php echo ($error!="") ? '<div class="error">'.$error.'</div>' : '';?>

<?php $message=$this->session->flashdata('message');?>
<?php echo ($message!="") ? '<div class="success">'.$message.'</div>' : '';?>

<?php if (isset($this->message)):?>
<?php echo ($this->message!="") ? '<div class="success">'.$this->message.'</div>' : '';?>
<?php endif;?>

<form method="post" >

<div style="text-align:right;">
	<input type="submit" value="<?php echo t('update');?>" name="submit"/>
</div>

<fieldset class="field-expanded">
	<legend><?php echo t('general_site_settings');?></legend>
    <div class="field">
            <label for="<?php echo 'website_title'; ?>"><?php echo t('website_title');?></label>
            <input class="input-flex" name="website_title" type="text" id="website_title"  value="<?php echo get_form_value('website_title',isset($website_title) ? $website_title : ''); ?>"/>
    </div>
    <div class="field">
            <label for="<?php echo 'website_footer'; ?>"><?php echo t('website_footer');?></label>
            <textarea rows="5" cols="20" class="input-flex" name="website_footer" type="text" id="website_footer"  ><?php echo get_form_value('website_footer',isset($website_footer) ? $website_footer : ''); ?></textarea>
    </div>        
    <div class="field">
            <label for="<?php echo 'default_home_page'; ?>"><?php echo t('default_home_page');?></label>
            <input class="input-flex" name="default_home_page" type="text" id="default_home_page"  value="<?php echo get_form_value('default_home_page',isset($default_home_page) ? $default_home_page : ''); ?>"/>
            <span class="field-note"><?php echo t('instruction_default_home_page'); ?></span>
    </div>    
    <div class="field">
            <label for="<?php echo 'website_webmaster_name'; ?>"><?php echo t('webmaster_name');?></label>
            <input class="input-flex" name="website_webmaster_name" type="text" id="website_webmaster_name"  value="<?php echo get_form_value('website_webmaster_name',isset($website_webmaster_name) ? $website_webmaster_name : ''); ?>"/>
    </div>    
    <div class="field">
            <label for="<?php echo 'website_webmaster_email'; ?>"><?php echo t('webmaster_email');?></label>
            <input class="input-flex" name="website_webmaster_email" type="text" id="website_webmaster_email"  value="<?php echo get_form_value('website_webmaster_email',isset($website_webmaster_email) ? $website_webmaster_email : ''); ?>"/>
    </div>    
    <div class="field">
            <label for="<?php echo 'cache_path'; ?>"><?php echo t('cache_folder');?></label>
            <input class="input-flex" name="cache_path" type="text" id="cache_path"  value="<?php echo get_form_value('cache_path',isset($cache_path) ? $cache_path : ''); ?>"/>
            <?php echo folder_exists($cache_path);?>
    </div>
    <div class="field">
            <label for="<?php echo 'cache_default_expires'; ?>"><?php echo t('cache_expiry');?></label>
            <input class="input-flex" name="cache_default_expires" type="text" id="cache_default_expires"  value="<?php echo get_form_value('cache_default_expires',isset($cache_default_expires) ? $cache_default_expires : '7200'); ?>"/>
            <span class="field-note"><?php echo t('cache_default_expires_msg');?></span>
    </div>
	<div class="field">
            <label for="<?php echo 'cache_disabled'; ?>"><?php echo t('cache_disabled');?></label>
            <span style="display:inline-block;padding-right:5px;" class="input-flex">
            <input type="radio" value="1" name="cache_disabled" <?php echo (get_form_value('cache_disabled',isset($cache_disabled) ? $cache_disabled : '0')=='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('yes');?> 
        	<input type="radio" value="0" name="cache_disabled" <?php echo (get_form_value('cache_disabled',isset($cache_disabled) ? $cache_disabled : '0')!='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('no');?>
            </span>
            <span class="field-note"><?php echo t('cache_disabled_msg');?></span>
    </div>
</fieldset>

<fieldset class="field-expanded">
	<legend><?php echo t('language');?></legend>
    <div class="field">
            <label for="<?php echo 'language'; ?>"><?php echo t('language');?></label>
            <?php echo form_dropdown('language', get_languages(), get_form_value("language",isset($language) ? $language: '')); ?> 
    </div>
</fieldset>


<fieldset class="field-expanded">
	<legend><?php echo t('use_html_editor_for_html');?></legend>
    <div class="field" >
        <label style="height:50px;" for="use_html_editor"><?php echo t('use_html_editor');?></label>
        <input type="radio" value="yes" name="use_html_editor" <?php echo ($use_html_editor=='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('yes');?> 
        <input type="radio" value="no" name="use_html_editor" <?php echo ($use_html_editor!='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('no');?><br/>
    </div>
</fieldset>

<fieldset class="field-expanded">
	<legend><?php echo t('survey_catalog_settings');?></legend>
	<div class="field">
        <label for="<?php echo 'catalog_root'; ?>"><?php echo t('catalog_folder');?></label>
        <input class="input-flex" name="catalog_root" type="text" id="catalog_root"  value="<?php echo get_form_value('catalog_root',isset($catalog_root) ? $catalog_root : ''); ?>"/>
        <?php echo folder_exists($catalog_root);?>
        <span class="field-note"><?php echo t('instruction_catalog_root'); ?></span>
	</div>
	<div class="field">
        <label for="<?php echo 'ddi_import_folder'; ?>"><?php echo t('ddi_import_folder');?></label>
        <input class="input-flex" name="ddi_import_folder" type="text" id="ddi_import_folder"  value="<?php echo get_form_value('ddi_import_folder',isset($ddi_import_folder) ? $ddi_import_folder : ''); ?>"/>
        <?php echo folder_exists($ddi_import_folder);?>
        <span class="field-note"><?php echo t('instruction_ddi_import_folder'); ?></span>
	</div>
	<div class="field">
        <label for="vocabulary"><?php echo t('select_vocabulary'); ?></label>
        <?php echo form_dropdown('topics_vocab', $this->configurations_model->get_vocabularies_array(), get_form_value("topics_vocab",isset($topics_vocab) ? $topics_vocab : '')); ?>
        <span class="field-note"><?php echo t('instruction_select_vocabulary'); ?></span>
	</div>

	<div class="field">
        <label style="height:50px;" for="<?php echo 'regional_search'; ?>"><?php echo t('regional_search');?></label>
        <div>
        <input type="radio" name="regional_search" value="yes" <?php echo ($regional_search=='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('regional_search_enable');?> <br/>
        <input type="radio" name="regional_search" value="no" <?php echo ($regional_search!='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('regional_search_disable');?>
        </div>
	</div>

	<div class="field">
        <label style="height:50px;" for="<?php echo 'topic_search'; ?>"><?php echo t('topic_search');?></label>
        <div>
        <input type="radio" name="topic_search" value="yes" <?php echo ($topic_search=='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('topic_search_enable');?> <br/>
        <input type="radio" name="topic_search" value="no" <?php echo ($topic_search!='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('topic_search_disable');?>
        </div>
	</div>

	<div class="field">
        <label style="height:50px;" for="<?php echo 'year_search'; ?>"><?php echo t('year_search');?></label>
        <div>
        <input type="radio" name="year_search" value="yes" <?php echo ($year_search=='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('year_search_enable');?> <br/>
        <input type="radio" name="year_search" value="no" <?php echo ($year_search!='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('year_search_disable');?>
        </div>
	</div>

	<div class="field">
        <label for="<?php echo 'catalog_records_per_page'; ?>"><?php echo t('data_catalog_page_size');?></label>
        <input class="input-flex" name="catalog_records_per_page" type="text" id="catalog_records_per_page"  value="<?php echo get_form_value('catalog_records_per_page',isset($catalog_records_per_page) ? $catalog_records_per_page : ''); ?>"/>
        <span class="field-note"><?php echo t('instruction_catalog_records_per_page'); ?></span>        
	</div>
</fieldset>

<fieldset class="field-expanded">
	<legend><?php echo t('site_login');?></legend>
    <div class="field">
            <label style="height:50px;" for="<?php echo 'site_password_protect'; ?>"><?php echo t('password_protect_website');?></label>
            <div>
                <input type="radio"  name="site_password_protect" value="yes" <?php echo ($site_password_protect=='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('require_all_users_to_login');?><br/>
                <input type="radio"  name="site_password_protect" value="no" <?php echo ($site_password_protect!='yes') ? 'checked="checked"' : ''; ?>/> <?php echo t('login_not_required');?>
            </div>
    </div>
    
    <div class="field">
            <label for="<?php echo 'login_timeout'; ?>"><?php echo t('login_timeout_in_min');?></label>
            <input class="input-flex" name="login_timeout" type="text" id="login_timeout"  value="<?php echo get_form_value('login_timeout',isset($login_timeout) ? $login_timeout : ''); ?>"/>
    </div>
    
    <div class="field">
            <label for="<?php echo 'min_password_length'; ?>"><?php echo t('min_password_length');?></label>
            <input class="input-flex" name="min_password_length" type="text" id="min_password_length"  value="<?php echo get_form_value('min_password_length',isset($min_password_length) ? $min_password_length : ''); ?>"/>
    </div>
</fieldset>

<fieldset class="field-expanded">
	<legend><?php echo t('mail_settings');?></legend>
    <div class="field">
            <label style="height:50px;" for="<?php echo 'mail_protocol'; ?>"><?php echo t('select_mail_protocol');?></label>
            <div>
            <input type="radio" value="mail" name="mail_protocol" <?php echo ($mail_protocol=='mail') ? 'checked="checked"' : ''; ?>/> <?php echo t('use_php_mail');?>  <br/>
            <input type="radio" value="smtp" name="mail_protocol" <?php echo ($mail_protocol=='smtp') ? 'checked="checked"' : ''; ?>/> <?php echo t('use_smtp');?><br/>
            </div>
    </div>
    
    <div class="field">
            <label for="<?php echo 'smtp_host'; ?>"><?php echo t('smtp_host');?></label>
            <input class="input-flex" name="smtp_host" type="text" id="smtp_host"  value="<?php echo get_form_value('smtp_host',isset($smtp_host) ? $smtp_host : ''); ?>"/>
    </div>
    
    <div class="field">
            <label for="<?php echo 'smtp_port'; ?>"><?php echo t('smtp_port');?></label>
            <input class="input-flex" name="smtp_port" type="text" id="smtp_port"  value="<?php echo get_form_value('smtp_port',isset($smtp_port) ? $smtp_port : ''); ?>"/>
    </div>
    
    <div class="field">
            <label for="<?php echo 'smtp_user'; ?>"><?php echo t('smtp_user');?></label>
            <input class="input-flex" name="smtp_user" type="text" id="smtp_user"  value="<?php echo get_form_value('smtp_user',isset($smtp_user) ? $smtp_user : ''); ?>"/>
    </div>
    
    <div class="field">
            <label for="<?php echo 'smtp_pass'; ?>"><?php echo t('smtp_password');?></label>
            <input class="input-flex" name="smtp_pass" type="text" id="smtp_pass"  value="<?php echo get_form_value('smtp_pass',isset($smtp_pass) ? $smtp_pass : ''); ?>"/>
    </div>
</fieldset>

<fieldset class="field-expanded">
	<legend><?php echo t('dashboard');?></legend>
    <div class="field">
            <label for="<?php echo 'news_feed_url'; ?>"><?php echo t('news_feed_url');?></label>
            <input class="input-flex" style="width:90%;" name="news_feed_url" type="text" id="news_feed_url"  value="<?php echo get_form_value('news_feed_url',isset($news_feed_url) ? $news_feed_url : ''); ?>"/>
    </div>
</fieldset>

<div style="text-align:right;">
	<input type="submit" value="<?php echo t('update');?>" name="submit"/>
</div>

</form>
</div>
<script type="text/javascript">
	function toggle_file_url(field_show,field_hide){
		$('#'+field_show).show();
		$('#'+field_hide).hide();
	}
	
	$('.field-expanded > legend').click(function(e) {
			e.preventDefault();
			$(this).parent('fieldset').toggleClass("field-collapsed");
			return false;
	});
	
	$(document).ready(function() {
  		$('.field-expanded > legend').parent('fieldset').toggleClass('field-collapsed');
	});
	
</script>
<?php
function folder_exists($folder)
{
	if (is_dir($folder))
	{
		return '<img src="images/tick.png" border="0" title="'.t('folder_exists_on_server').'"/>';
	}
	else
	{
		return '<img src="images/delete.png" border="0" title="'.t('path_not_found').'"/>';
	}
}
function get_languages()
{
	$languages = scandir(APPPATH.'language/');
	foreach($languages as $lang)
	{
		if ($lang!=='.' && $lang!=='..')
		{
			$output[$lang]=$lang;
		}	
	}	
	return $output;
}
?>