<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 11/01/18
 * Time: 14.43
 */
$ctrl->obj->url = APPUI_I18N_ROOT.'languages/complete_history';
$is_dev = $ctrl->inc->user->is_dev();

//transfer to $model
$source_langs = $ctrl->db->get_rows("
  SELECT DISTINCT (lang) FROM bbn_i18n
");


$ctrl->
  add_data([
    'is_dev' => $is_dev,
    'source_langs' => $source_langs
  ])
  ->combo('', true);
