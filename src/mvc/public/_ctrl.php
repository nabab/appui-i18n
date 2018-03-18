<?php
/** @var $ctrl \bbn\mvc\controller */
if ( !\defined('APPUI_I18N_ROOT') ){
  define('APPUI_I18N_ROOT', $ctrl->plugin_url('appui-i18n').'/');
  $ctrl->data['root'] = APPUI_I18N_ROOT;
}
return 1;