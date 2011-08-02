<?php

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

set_time_limit(240);

require_once __DIR__.'/../lib/PHPPdf/Autoloader.php';

PHPPdf\Autoloader::register();
PHPPdf\Autoloader::register(dirname(__FILE__).'/../lib/vendor');

// set different way of configuration
//$facade = PHPPdf\Parser\FacadeBuilder::create(new PHPPdf\Configuration\DependencyInjection\LoaderImpl())->setCache('File', array('cache_dir' => __DIR__.'/cache/'))
$facade = PHPPdf\Parser\FacadeBuilder::create()
// set cache
//                                               ->setCache('File', array('cache_dir' => __DIR__.'/cache/'))
//                                               ->setUseCacheForStylesheetConstraint(false)
//                                               ->setUseCacheForStylesheetConstraint(true)
                                               ->build();

if($_SERVER['argc'] < 3) 
{
    die('Passe example name and destination file path, for example `cli.php example-name \some\destination\file.pdf`');
}

$name = basename($_SERVER['argv'][1]);
$destinationPath = $_SERVER['argv'][2];

$documentFilename = './'.$name.'.xml';
$stylesheetFilename = './'.$name.'-style.xml';

if(!is_readable($documentFilename) || !is_readable($stylesheetFilename))
{
    die(sprintf('Example "%s" dosn\'t exist.', $name));
}

if(!is_writable(dirname($destinationPath)))
{
    die(sprintf('"%s" isn\'t writable.', $destinationPath));
}

$xml = str_replace('dir:', __DIR__.'/', file_get_contents($documentFilename));
$stylesheetXml = str_replace('dir:', __DIR__.'/', file_get_contents($stylesheetFilename));
$stylesheet = PHPPdf\Util\DataSource::fromString($stylesheetXml);

$start = microtime(true);

$content = $facade->render($xml, $stylesheet);

echo 'time: '.(microtime(true) - $start).'s';

file_put_contents($destinationPath, $content);
