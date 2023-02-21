<?php
$did = 0;
$did2 = 0;
if ($trans = $ctrl->db->rselectAll('bbn_i18n')) {
  foreach ($trans as $t) {
    $t['exp'] = trim(normalizer_normalize($t['exp']));
    if ($ctrl->db->select([
      'table' => 'bbn_i18n',
      'fields' => [],
      'where' => [
        'conditions' => [[
          'field' => 'exp',
          'value' => $t['exp']
        ], [
          'field' => 'lang',
          'value' => $t['lang']
        ], [
          'field' => 'id',
          'operator' => '!=',
          'value' => $t['id']
        ]]
      ]
    ])) {
      $did += $ctrl->db->delete('bbn_history_uids', ['bbn_uid' => $t['id']]);
    }
    else {
      $did += $ctrl->db->update('bbn_i18n', $t, ['id' => $t['id']]);
    }
  }
}
if ($trans2 = $ctrl->db->rselectAll('bbn_i18n_exp')) {
  foreach ($trans2 as $t) {
    $t['expression'] = trim(normalizer_normalize($t['expression']));
    if ($ctrl->db->select([
      'table' => 'bbn_i18n_exp',
      'fields' => [],
      'where' => [
        'conditions' => [[
          'field' => 'expression',
          'value' => $t['expression']
        ], [
          'field' => 'lang',
          'value' => $t['lang']
        ], [
          'field' => 'id_exp',
          'value' => $t['id_exp']
        ], [
          'field' => 'id',
          'operator' => '!=',
          'value' => $t['id']
        ]]
      ]
    ])) {
      $did2 += $ctrl->db->delete('bbn_history_uids', ['bbn_uid' => $t['id']]);
    }
    else {
      $did2 += $ctrl->db->update('bbn_i18n_exp', $t, ['id' => $t['id']]);
    }
  }
}
\bbn\X::adump($did . '/' . count($trans));
\bbn\X::adump($did2 . '/' . count($trans2));
