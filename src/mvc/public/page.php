<?php
use bbn\Str;

/** @var bbn\Mvc\Controller $ctrl */
if (($ctrl->getConstant('baseURL') === null)
  || (Str::pos($ctrl->getConstant('baseURL'), APPUI_I18N_ROOT.'page/') !== 0)
){
  $ctrl->setUrl(APPUI_I18N_ROOT.'page')
        ->setIcon('nf nf-fa-flag')
       ->setColor('orange', '#FFF')
       ->combo('i18n', true);
}