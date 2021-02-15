<?php
$ctrl->add('page');
die();
if (strpos($ctrl->baseURL, APPUI_I18N_ROOT.'ui/') !== 0) {
  $ctrl->setUrl($ctrl->pluginPath().'ui')
       ->setIcon('nf nf-fa-flag')
       ->setColor('orange', '#FFF')
       ->combo('i18n', true);
}
