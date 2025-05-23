<?php
use bbn\X;
use bbn\Appui\Grid;
if ($model->hasData('limit', true)
  && X::hasProps($model->data['data'], ['source_lang', 'translation_lang'], true)
) {
  $transLangCode = $model->data['data']['translation_lang'];
  $sourceCode = $model->data['data']['source_lang'];
  $grid = new Grid($model->db, $model->data, [
    'table' => 'bbn_i18n',
    'fields' => [
      'bbn_i18n.exp',
      'idExp' => 'bbn_i18n.id',
      'translation' => 'expression'
    ],
    'join' => [[
      'type' => 'left',
      'table' => 'bbn_i18n_exp',
      'on' => [
        'conditions' => [[
          'field' => 'bbn_i18n_exp.id_exp',
          'exp' => 'bbn_i18n.id'
        ],[
          'field' => 'bbn_i18n_exp.lang',
          'value' => $transLangCode
        ]]
      ],
    ]],
    'filters' => [[
      'field' => 'bbn_i18n.lang',
      'value' => $sourceCode
    ]],
    'group_by' => ['bbn_i18n.id'],
  ]);
  if ( $grid->check() ){
    return $grid->getDatatable();
  }
}
