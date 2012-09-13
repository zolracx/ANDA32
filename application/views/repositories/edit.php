<h1><?php echo $this->page_title;?></h1>
	
<?php if (validation_errors() ) : ?>
    <div class="error">
	    <?php echo validation_errors(); ?>
    </div>
<?php endif; ?>

<?php $error=$this->session->flashdata('error');?>
<?php echo ($error!="") ? '<div class="error">'.$error.'</div>' : '';?>

<?php $message=$this->session->flashdata('message');?>
<?php echo ($message!="") ? '<div class="success">'.$message.'</div>' : '';?>

<?php echo form_open(current_url(), array('class'=>'form') ); ?>
	<input type="hidden" name="id" value="<?php echo get_form_value('id',isset($id) ? $id : ''); ?>"/>

    <div class="field">
        <label for="repositoryid"><?php echo t('repositoryid');?><span class="required">*</span></label>
        <?php echo form_input($this->data['repositoryid']);?>        
    </div>

    <div class="field">
        <label for="title"><?php echo t('title');?><span class="required">*</span></label>
        <?php echo form_input($this->data['title']);?>        
    </div>
    
    <div class="field">
        <label for="url"><?php echo t('url');?><span class="required">*</span></label>
        <?php echo form_input($this->data['url']);?>        
    </div>

    <div class="field">
        <label for="organization"><?php echo t('organization');?><span class="required">*</span></label>
        <?php echo form_input($this->data['organization']);?>        
    </div>

    <div class="field">
        <label for="country"><?php echo t('country');?><span class="required">*</span></label>
        <?php echo form_input($this->data['country']);?>        
    </div>

    <div class="field">
        <label for="scan_interval"><?php echo t('scan_interval_in_days');?><span class="required">*</span></label>
        <?php echo form_input($this->data['scan_interval']);?>        
    </div>

    <p>
		<?php echo form_submit('submit', 'Submit');?>
     	<?php echo anchor('admin/repositories',t('cancel'),array('class'=>'button') );?>
    </p>
<?php echo form_close();?>
