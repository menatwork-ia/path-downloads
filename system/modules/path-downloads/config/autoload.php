<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Path-downloads
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'PathDownload',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'PathDownload\ContentDownload'  => 'system/modules/path-downloads/ContentDownload.php',
    'PathDownload\ContentDownloads' => 'system/modules/path-downloads/ContentDownloads.php',
    'PathDownload\FileTree'         => 'system/modules/path-downloads/FileTree.php',
));
