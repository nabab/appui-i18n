<?php
use bbn\Str;
$ctrl->add('page');
die();
if (defined('BBN_BASEURL') && defined('APPUI_I18N_ROOT') && (Str::pos(BBN_BASEURL, APPUI_I18N_ROOT.'ui/') !== 0)) {
  $ctrl->setUrl($ctrl->pluginPath().'ui')
       ->setIcon('nf nf-fa-flag')
       ->setColor('orange', '#FFF')
       ->combo('i18n', true);
}
