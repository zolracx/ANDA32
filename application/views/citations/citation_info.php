<style>
.title {}
.sub-title{color:#333333}
.citation-box{margin-top:20px;margin-bottom:20px;padding:10px; background-color:#FEF9ED;border:1px solid gainsboro;}
<?php if (!$this->input->get("print")) :?>
.abstract {overflow:auto; height:100px;}
<?php endif;?>
</style>

<?php if (!$this->input->get("print")) :?>
<div style="text-align:right;">
	<a target="_blank" href="<?php echo site_url();?>/citations/<?php echo $id; ?>/?print=yes" ><img src="images/print.gif" border="0"/> <?php echo t('print');?></a>
</div>
<?php endif;?>

<h1><?php echo t('citation_information'); ?></h1>
<table class="grid-table">
<?php if ($ctype!=''):?>
<tr>
	<td><?php echo t('type');?></td>
    <td>
		<?php echo t($ctype);?>		
		<?php if ($subtitle!=''):?>
        	- <?php echo $subtitle;?>
		<?php endif;?>
</td>
</tr>
<?php endif;?>

<?php if ($title!=''):?>
<tr>
	<td><?php echo t('title');?></td>
    <td><?php echo $title;?></td>
</tr>
<?php endif;?>

<?php if ($authors!=''):?>
	<?php $authors_array=($authors); ?>
	<?php if(is_array($authors_array)):?>    
        <tr>
            <td><?php echo t('authors');?></td>
            <td>
			<?php foreach($authors_array as $author): ?>
				<div><?php 
						$author_full=trim($author['fname']. ' '.$author['initial']). ' '.$author['lname'];
						echo anchor('citations/?keywords='.$author_full.'&field=authors',$author_full, 'class="author" target="_blank"');
					?>
                </div>
            <?php endforeach;?>
            </td>
        </tr>
	<?php endif;?>
<?php endif;?>

<?php if ($edition!=''):?>
<tr>
	<td><?php echo t('edition');?></td>
    <td><?php echo $edition;?></td>
</tr>
<?php endif;?>

<?php if ($volume!=''):?>
<tr>
	<td><?php echo t('volume');?></td>
    <td><?php echo $volume;?></td>
</tr>
<?php endif;?>

<?php if ($issue!=''):?>
<tr>
	<td><?php echo t('issue');?></td>
    <td><?php echo $issue;?></td>
</tr>
<?php endif;?>

<?php if ($pub_year!=0 || $pub_day!=0 || $pub_month!='' ):?>
<tr>
	<td><?php echo t('publication_day_month_year');?></td>
    <td><?php echo $pub_year;?></td>
</tr>
<?php endif;?>

<?php if ($page_from!='' || $page_to!=''):?>
<tr>
	<td><?php echo t('page_numbers');?></td>
    <td>
		<?php 
			if ($page_from!='')
			{
				$page_[]=$page_from;
			}
			if ($page_to!='')
			{
				$page_[]=$page_to;
			}
			echo implode("-",$page_);
		?>
    </td>
</tr>
<?php endif;?>


<?php if ($publisher!=''):?>
<tr>
	<td><?php echo t('publisher');?></td>
    <td><?php echo $publisher;?></td>
</tr>
<?php endif;?>

<?php if ($place_publication!=''):?>
<tr>
	<td><?php echo t('publication_city');?></td>
    <td><?php echo $place_publication;?></td>
</tr>
<?php endif;?>

<?php if ($place_state!=''):?>
<tr>
	<td><?php echo t('publication_state_country');?></td>
    <td><?php echo $place_state;?></td>
</tr>
<?php endif;?>

<?php if ($url!=''):?>
<tr>
	<td><?php echo t('url');?></td>
    <td><?php echo anchor(prep_url($url),wordwrap($url,100,"&#8203;",TRUE),'target="_blank"');?></td>
</tr>
<?php endif;?>

<?php if (isset($abstract)):?>
<?php if ($abstract!=''):?>
<tr valign="top">
	<td><?php echo t('abstract');?></td>
    <td><div "class="abstract"><?php echo nl2br($abstract);?></div></td>
</tr>
<?php endif;?>
<?php endif;?>

</table>

<?php if( isset($related_surveys) ):?>
	<?php if( count($related_surveys) >0):?>
        <h2 style="margin-top:20px;"><?php echo t('related_studies'); ?></h2>
		<table class="grid-table">
        <?php foreach($related_surveys as $survey):?>
            <tr valign="top">
            <td>&raquo;</td>
            <td class="related-study"><?php echo anchor('catalog/'.$survey['id'],$survey['nation'].' - '.$survey['titl']);?>,
                <span><?php echo $survey['authenty'];?></span>
                </td>
            </tr>    
        <?php endforeach;?>
        </table>
    <?php endif;?>
<?php endif;?>

<?php 
//turn all links into ajax links if page was accessed using the ajax parameter
if ($this->input->get("ajax")): ?>
<script type="text/javascript">
$(function() {	
	$(".related-study a, .citation-box a").each(function(index) {
		$(this).attr("href",$(this).attr("href")+"?ajax=true");
  });	
});
</script>
<?php endif; ?>