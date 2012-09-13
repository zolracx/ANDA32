<?php 
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
//attach a stylesheet so browsers with no support for RSS can still display it
echo '<?xml-stylesheet type="text/xsl" href="'.base_url().'/xslt/rss.xslt"?>';

//from config
$language=$this->config->item("language");
$admin_email=$this->config->item("admin_email");

if ($language=='')
{
	$language='EN';
}

?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:nada="http://ihsn.org/nada/"
>

    <channel>    
        <title><?php echo $this->config->item("website_title"); ?></title>
		<link><?php echo site_url(); ?></link>
        <description></description>
        <dc:language><?php echo $language; ?></dc:language>
        <dc:creator><?php echo $admin_email; ?></dc:creator>
        <dc:rights>Copyright <?php echo gmdate("Y", time()); ?></dc:rights>    	
        <?php foreach($records->result() as $entry): ?>
        <item>
          <title><![CDATA[<?php echo ($entry->titl); ?>]]></title>
          <link><?php echo site_url('catalog/' . $entry->id) ?></link>
          <guid><?php echo $entry->surveyid ?></guid>
          <description><![CDATA[<?php echo (strip_tags($entry->titlstmt.', ' . $entry->authenty.  ' - ' . $entry->nation)); ?>]]></description>
          <pubDate><?php echo date ('r', $entry->changed);?></pubDate>
          <nada:surveyid><?php echo $entry->surveyid; ?></nada:surveyid>
          <nada:country><?php echo $entry->nation; ?></nada:country>
          <nada:colldate><?php echo $entry->proddate; ?></nada:colldate>
          <nada:accesspolicy><?php echo $entry->model; ?></nada:accesspolicy>
        </item>
        <?php endforeach; ?>   
    </channel>
</rss> 