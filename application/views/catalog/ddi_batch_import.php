<div class="content-container">

<?php $error=$this->session->flashdata('error');?>
<?php echo ($error!="") ? '<div class="error">'.$error.'</div>' : '';?>
<?php include 'catalog_page_links.php';?>

<h1 class="page-title"><?php echo t('batch_import_title');?></h1>
<div>

<?php if ( count($files)==0 || $files===false) :?>
    <div class="error">
		<?php echo sprintf(t('import_ddi_no_files_found'),$this->config->item('ddi_import_folder'));?>
    </div>    
    <?php return; ?>
<?php endif; ?>

<div class="note">
	<?php echo sprintf(t('import_ddi_files_found'),count($files));?>
    <div style="margin-top:20px;">
    <label for="overwrite" class="desc"><input type="checkbox" name="overwrite" id="overwrite" checked="checked"  value="yes"/> <?php echo t('ddi_overwrite_exist');?></label>
    </div>
</div>

<div class="note" id="batch-import-box" style="display:none;" >
    <div id="batch-import-processing" style="padding:5px;border-bottom:1px solid gainsboro;margin-bottom:10px;">Processing survey...</div>
    <div id="batch-import-log" ></div>
</div>

<?php echo form_open_multipart('admin/catalog/batch_import', array('class'=>'form')	 );?>
<input type="button" name="import" value="<?php echo t('btn_import');?>" onclick="batch_import.process();"/>

<table class="grid-table" width="100%" cellspacing="0" cellpadding="0"> 
<tr align="left" class="header">
	<th><input type="checkbox" value="-1" id="chk_toggle"/></th>
    <th><?php echo t('name');?></th>
    <th align="right"><?php echo t('size');?></th>
    <th><?php echo t('date');?></th>
</tr>
<?php foreach($files as $file):?>
	<tr>
    	<td><input type="checkbox" class="chk" id="<?php echo base64_encode($file['server_path']);?>" value="<?php echo $file['name'];?>"/></td>
    	<td><?php echo $file['name'];?></td>
        <td align="right"><?php echo format_bytes($file['size']);?></td>
        <td><?php echo date($this->config->item('date_format'),$file['date']);?></td>
    </tr>
<?php endforeach;?>
</table>
<input type="button" name="import" value="<?php echo t('btn_import');?>" onclick="batch_import.process();"/>
<?php echo form_close();?>
</div>

</div>
<script language="javascript">
//translations	
var i18n=
{
'cancel_import_process':"<?php echo t('cancel_import_process');?>",
'import_completed':"<?php echo t('import_completed');?>",
'import_cancelled':"<?php echo t('import_cancelled');?>"
};

$(".log").css({ border: '1px solid gray'});
var batch_import = {
	
	id:null,
	queue:[],
	queue_idx:0,
	xhr:null,
	isprocessing:false,
	
	process : function() {
		
		if (this.isprocessing==true){
			return false;
		}
		
		this.queue_idx=0;
		this.queue=[];
		obj=this;
		var i=0;

		$('.chk').each(function(){ 
		   if (this.checked==true) {
				obj.queue[i++]={id:this.id,name:this.value};
		   }
	    }); 

		html=$("#batch-import-box").html();
		$("#batch-import-log").html("");
		this.process_queue();
	},
	
	//process items in queue
	process_queue: function(){
		if (this.queue_idx<this.queue.length) {			
			
			html='<img src="images/loading.gif" align="absbottom"> Importing '+ (this.queue_idx+1) +' of '+this.queue.length+'... <b>['+this.queue[this.queue_idx].name+']</b>';
			html+=' <a href="#" onclick="batch_import.abort();return false;">' +i18n.cancel_import_process+'</a>';
			$("#batch-import-box").show();
			$("#batch-import-processing").html(html);
			
			this.isprocessing=true;
			this.import_single(this.queue[this.queue_idx++].id);		
		}
		else{
			$("#batch-import-processing").html(i18n.import_completed);
			this.isprocessing=false;
		}
		
	},
	
	import_single: function(id) {
		obj=this;
		//set error hanlder
		$.ajaxSetup({
				error:function(x,e){					
					alert("Error code: " + x.status);
					obj.abort();					
				}
			});		
		
		var overwrite=0;
		if ($("#overwrite").attr("checked")){overwrite=1}
		
		//post	
		this.xhr=$.post(CI.base_url+"/admin/catalog/do_batch_import",{id:id,overwrite:overwrite},func_data, "json");
		
		//handle json returned values
		function func_data(data){
			 if (data.success){
				obj.queue[obj.queue_idx-1].status=data.success;
				$("#batch-import-log").append('<div class="log" style="color:green;">#' + (obj.queue_idx) + ': '  + obj.queue[obj.queue_idx-1].name + ' - ' + data.success+ '</div>');
			 }
			 else{
			 	obj.queue[obj.queue_idx-1].status=data.error;
				$("#batch-import-log").append('<div class="log" style="color:red">#' + (obj.queue_idx) + ': '  +  obj.queue[obj.queue_idx-1].name + ' - ' + data.error+ '</div>');
			 }
			 obj.process_queue();
		}//end-func
	},
	
	abort: function(){
		$("#batch-import-processing").html(i18n.import_cancelled);
		this.xhr.abort();
		this.isprocessing=false;
	}	
};


//checkbox select/deselect
jQuery(document).ready(function(){
	$("#chk_toggle").click(
			function (e) 
			{
				$('.chk').each(function(){ 
                    this.checked = (e.target).checked; 
                }); 
			}
	);
	
});

</script>