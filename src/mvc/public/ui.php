<?php
use bbn\Str;

/** @var bbn\Mvc\Controller $ctrl */
$ctrl->add('page');
die();
if ($ctrl->getConstant('baseURL') && defined('APPUI_I18N_ROOT') && (Str::pos($ctrl->getConstant('baseURL'), APPUI_I18N_ROOT.'ui/') !== 0)) {
  $ctrl->setUrl($ctrl->pluginPath().'ui')
       ->setIcon('nf nf-fa-flag')
       ->setColor('orange', '#FFF')
       ->combo('i18n', true);
}
