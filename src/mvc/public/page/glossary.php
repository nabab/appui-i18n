<?php
use bbn\Appui\I18n;
use bbn\X;

if (!empty($ctrl->arguments[0]) && !empty($ctrl->arguments[1])) {
  $i18nCls = new I18n($ctrl->db);
  $primary = $i18nCls->getPrimariesLangs();
  $ctrl->addData([
    'source_lang' => $ctrl->arguments[0],
    'translation_lang' => $ctrl->arguments[1],
    'lang_name' => X::getField($primary, ['code' => $ctrl->arguments[1]], 'text'),
    'source_lang_name' => X::getField($primary, ['code' => $ctrl->arguments[0]], 'text'),
    'primary' => $primary,
  ])->combo(X::_('From %s to %s', $ctrl->data['source_lang_name'], $ctrl->data['lang_name']), true);
}
