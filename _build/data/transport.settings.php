<?php
/**
 * @package tinymcerte
 * @subpackage build
 */
$settings = array();

$settings['tinymcerte.plugins']= $modx->newObject('modSystemSetting');
$settings['tinymcerte.plugins']->fromArray(array(
    'key' => 'tinymcerte.plugins',
    'value' => 'advlist autolink lists link image charmap print preview anchor visualblocks searchreplace code fullscreen insertdatetime media table contextmenu paste',
    'xtype' => 'textfield',
    'namespace' => 'tinymcerte',
    'area' => 'general',
),'',true,true);

$settings['tinymcerte.toolbar1']= $modx->newObject('modSystemSetting');
$settings['tinymcerte.toolbar1']->fromArray(array(
    'key' => 'tinymcerte.toolbar1',
    'value' => 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
    'xtype' => 'textfield',
    'namespace' => 'tinymcerte',
    'area' => 'general',
),'',true,true);

$settings['tinymcerte.toolbar2']= $modx->newObject('modSystemSetting');
$settings['tinymcerte.toolbar2']->fromArray(array(
    'key' => 'tinymcerte.toolbar2',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'tinymcerte',
    'area' => 'general',
),'',true,true);

$settings['tinymcerte.toolbar3']= $modx->newObject('modSystemSetting');
$settings['tinymcerte.toolbar3']->fromArray(array(
    'key' => 'tinymcerte.toolbar3',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'tinymcerte',
    'area' => 'general',
),'',true,true);

$settings['tinymcerte.menubar']= $modx->newObject('modSystemSetting');
$settings['tinymcerte.menubar']->fromArray(array(
    'key' => 'tinymcerte.menubar',
    'value' => 'file edit insert view format table tools',
    'xtype' => 'textfield',
    'namespace' => 'tinymcerte',
    'area' => 'general',
),'',true,true);

$settings['tinymcerte.statusbar']= $modx->newObject('modSystemSetting');
$settings['tinymcerte.statusbar']->fromArray(array(
    'key' => 'tinymcerte.statusbar',
    'value' => 1,
    'xtype' => 'combo-boolean',
    'namespace' => 'tinymcerte',
    'area' => 'general',
),'',true,true);

return $settings;
