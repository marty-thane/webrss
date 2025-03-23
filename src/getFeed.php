<?php
require('common.php');

$url = $_GET['url'];

$xml = new DOMDocument;

if (!$xml->load($url)) {
    echo "<script>alert('Error loading feed.');</script>";
    exit;
}

if (!$xml->schemaValidate('static/rss.xsd')) {
    echo "<script>alert('Feed validation failed.');</script>";
    exit;
}

$xsl = new DOMDocument;
$xsl->load('static/rss.xsl');
$xslt = new XSLTProcessor();
$xslt->importStylesheet($xsl);
$transXml = $xslt->transformToXml($xml);
echo $transXml;
?>
