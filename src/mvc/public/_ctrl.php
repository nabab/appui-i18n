<?php
/** @var $ctrl \bbn\Mvc\Controller */
if ( !\defined('APPUI_I18N_ROOT') ){
  define('APPUI_I18N_ROOT', $ctrl->pluginUrl('appui-i18n').'/');
  $ctrl->data['root'] = APPUI_I18N_ROOT;
}
return 1;