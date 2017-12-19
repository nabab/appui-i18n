<?php
/** @var $ctrl \bbn\mvc\controller */
if ( !\defined('APPUI_I18N_ROOT') ){
  define('APPUI_I18N_ROOT', $ctrl->plugin_url('appui-i18n').'/');
}
bindtextdomain('appui_i18n', BBN_LIB_PATH.'bbn/appui-i18n/src/locale');
setlocale(LC_ALL, "en_EN.utf8");
textdomain('appui_i18n');

return 1;