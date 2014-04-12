<?php

/**
 * Palettes.
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['pathDownload']  = '{type_legend},type,headline;{source_legend},singlePathSRC;{dwnconfig_legend},linkTitle;{protected_legend:hide},protected;{expert_legend:hide},guests,invisible,cssID,space';
$GLOBALS['TL_DCA']['tl_content']['palettes']['pathDownloads'] = '{type_legend},type,headline;{source_legend},multiPathSRC,sortBy,useHomeDir;{protected_legend:hide},protected;{expert_legend:hide},guests,invisible,cssID,space';

/**
 * Fields.
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['singlePathSRC'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['singlePathSRC'],
    'exclude'   => true,
    'inputType' => 'pathFileTree',
    'sql'       => "varchar(255) NOT NULL default ''",
    'eval'      => array
    (
        'fieldType' => 'radio',
        'files'     => true,
        'mandatory' => true,
        'tl_class'  => 'clr'
    )
);

$GLOBALS['TL_DCA']['tl_content']['fields']['multiPathSRC'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['multiPathSRC'],
    'exclude'   => true,
    'inputType' => 'pathFileTree',
    'sql'       => "blob NULL",
    'eval'      =>array
    (
        'multiple'   => true,
        'fieldType'  => 'checkbox',
        'files'      => false,
        'mandatory'  => true
    )
);
