<?php if (isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs)>0):?>
	<?php 
        $total=count($breadcrumbs);
        $k=1;
    ?>
    <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
    <?php foreach($breadcrumbs as $url=>$title):?>
       <span typeof="v:Breadcrumb">
        <?php if ($k!==$total):?>
        	<?php if (!is_numeric($url)):?>
	         <a href="<?php echo site_url().'/'.$url;?>" rel="v:url" property="v:title">
    	      	<?php echo $title;?>
        	</a> ›
            <?php else:?>
    	      	<?php echo $title;?> ›
            <?php endif;?>
        <?php else:?>
	         <?php if (!is_numeric($url)):?>
	         <a class="active" href="<?php echo site_url().'/'.$url;?>" rel="v:url" property="v:title">
    	      	<?php echo $title;?>
        	</a>
            <?php else:?>
    	      	<?php echo $title;?>
            <?php endif;?>
        <?php endif;?>    
       </span>
       <?php $k++;?>
    <?php endforeach;?>
	
	
		<!--<table width="" class="catalog-page-title" cellpadding="0" cellspacing="0" border="1">-->
		<!--<tr valign="baseline">-->
		<!--<td><h1><?php //echo t('title_data_catalog');?></h1></td>-->
		<!--<td align="right">-->
		<!--<div class="page-links">-->
		<div style="float: right">
		<span>
			<a id="link_export" title="<?php echo t('link_export_search');?>" href="<?php echo site_url();?>/catalog/export"><img src="images/export.gif" border="0" alt="Export"/></a>
			<a title="<?php echo t('rss_feed');?>" href="<?php echo site_url();?>/catalog/rss" target="_blank"><img src="images/rss_icon.png" border="0" alt="RSS"/></a>
		</span>
		</div>		
		<!--</div>-->
		<!--</td>-->
		<!--</tr>-->
		<!--</table>-->
    </div>
<?php endif;?>