<?php
if (!defined('BBN_BASEURL')
  || (strpos(constant('BBN_BASEURL'), APPUI_I18N_ROOT.'page/') !== 0)
){
  $ctrl->setUrl(APPUI_I18N_ROOT.'page')
        ->setIcon('nf nf-fa-flag')
       ->setColor('orange', '#FFF')
       ->combo('i18n', true);
}