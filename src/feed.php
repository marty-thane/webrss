<?php
require('common.php');

if (isset($_GET['url'])) {
  $url = $_GET['url'];

  // Load the RSS feed as XML
  $rss = simplexml_load_file($url);
  
  if ($rss === false) {
    echo 'Error loading RSS feed.';
  } else {
    // Load the XSLT stylesheet
    $xsl = new DOMDocument;
    $xsl->load('rss.xsl'); // Assuming the XSLT file is named 'rss.xsl'

    // Set up the XSLT processor
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl);

    // Convert the RSS feed to a DOMDocument
    $rssDom = dom_import_simplexml($rss);
    
    // Apply the XSLT transformation
    $htmlOutput = $proc->transformToXML($rssDom);

    // Output the transformed HTML
    echo $htmlOutput;
  }
} else {
  echo 'No URL specified.';
}
?>


