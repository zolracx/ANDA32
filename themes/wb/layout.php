<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>
<?php
$menu_horizontal=TRUE;

//side menu
$data['menus']= $this->Menu_model->select_all();		
$sidebar=$this->load->view('default_menu', $data,true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo t($title);?></title>
<base href="<?php echo js_base_url(); ?>">

<link rel="stylesheet" type="text/css" href="themes/<?php echo $this->template->theme();?>/reset-fonts-grids.css" />
<link rel="stylesheet" type="text/css" href="themes/<?php echo $this->template->theme();?>/styles.css" />
<link rel="stylesheet" type="text/css" href="themes/<?php echo $this->template->theme();?>/forms.css" />

<script type="text/javascript" src="javascript/jquery.js"></script>

<?php if (isset($_styles) ){ echo $_styles;} ?>
<?php if (isset($_scripts) ){ echo $_scripts;} ?>

<script type="text/javascript"> 
   var CI = {'base_url': '<?php echo site_url(); ?>'}; 
</script>

<style>
.cuerpo
{
filter:progid:DXImageTransform.Microsoft.Gradient(GradientType=0, StartColorStr='#D7E1EE', EndColorStr='#F1F5FA');
background: -moz-linear-gradient(top, #d7e1ee 0%, #f1f5fa 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#d7e1ee), color-stop(100%,#f1f5fa)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, #d7e1ee 0%,#f1f5fa 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, #d7e1ee 0%,#f1f5fa 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top, #d7e1ee 0%,#f1f5fa 100%); /* IE10+ */
background: linear-gradient(top, #d7e1ee 0%,#f1f5fa 100%); /* W3C */
}



.pie
{
background: #a0aaaf; /* Old browsers */
background: -moz-linear-gradient(top, #a0aaaf 0%, #4f626c 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#a0aaaf), color-stop(100%,#4f626c)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top, #a0aaaf 0%,#4f626c 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top, #a0aaaf 0%,#4f626c 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top, #a0aaaf 0%,#4f626c 100%); /* IE10+ */
background: linear-gradient(top, #a0aaaf 0%,#4f626c 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a0aaaf', endColorstr='#4f626c',GradientType=0 ); /* IE6-9 */
}

</style>

</head>
<body>
<div id="custom-doc" class="yui-t7" > 
	<!--login information bar-->
    <span id="user-container">
    <?php $this->load->view('user_bar');?>
    </span>
    
    <!-- header -->
    <div id="hd">
       	<!-- logo -->
        <div class="site-logo">
        	<a title="<?php echo $this->config->item("website_title");?> - Home Page"  href="<?php echo site_url();?>">
            <img src="themes/<?php echo $this->template->theme();?>/cabecera.png"  border="0" alt="Logo"/>
            </a>
        </div>
    </div>
    
    <div id="bd" >
    	<!-- banner-->
        <div id="banner"></div>
        
        <div id="inner-body">
            <!-- menu -->
            <?php if ($menu_horizontal===TRUE):?>
            <div class="menu-horizontal">
                    <?php echo isset($sidebar) ? $sidebar : '';?>
                    <br style="clear:both;"/>
             </div>
            <?php endif;?>
        
            <?php if ($menu_horizontal===TRUE):?>
                <div id="content" class="yui-b" >
                
                	<!--share-bar -->
                    <div id="page-tools">
					<?php include 'share.php';?>
                	</div>
                
                    <!--breadcrumbs -->
                    <?php $breadcrumbs_str= $this->breadcrumb->to_string();?>
                    <?php if ($breadcrumbs_str!=''):?>
                        <div id="breadcrumb">
                        <?php echo $breadcrumbs_str;?>
                        </div>
                    <?php endif;?>
                    
					<?php echo isset($content) ? $content : '';?>
                </div>
            <?php else:?>
            <div id="yui-main">
             <div id="content" class="yui-b"><?php echo isset($content) ? $content : '';?></div>
          </div>
          <!-- side bar -->
          <div id="sidebar" class="yui-b">
                <div class="sidebar"><?php echo isset($sidebar) ? $sidebar : '';?></div>
          </div>
          <?php endif; ?>
		</div>

    <!-- footer -->
    <div id="ft" class="pie" STYLE="padding:10px; font:bold 9pt verdana; color: #EBECBD;"><?php echo $this->config->item("website_footer");?> </div>
	<!--end bd-->
    </div>

</div>
</div>
<div style="padding-bottom:100px;">&nbsp;</div>
<?php $this->load->view('tracker/js_tracker');?>
</body>
</html>