<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 19/04/18
 * Time: 14.58
 */

if ( !empty($model->data['id_option']) ){
  $res = [];
  $translation = new \bbn\Appui\I18n($model->db);

  /** @var $strings takes all the text of this option's children*/
  $strings = array_values($model->inc->options->options($model->data['id_option']));
  /** push into the array of strings the text of the option parent */
  $strings[] = $model->inc->options->text($model->data['id_option']);
  $primaries = $translation->getPrimariesLangs();
  foreach ($primaries as $p ){
    $configured_langs[] = $p['code'];
  }
  $new = 0;
  foreach ($strings as $idx => $i ){
    $row = $model->db->rselect('bbn_i18n',['id'], [
        'exp' => $i,
        'lang' => $model->data['language']
      ]
    );
    /** check if the opt text is in bbn_i18n and takes translations from db */
    if ( !$row){
      if ( $model->db->insert('bbn_i18n', [
        'exp' => $i,
        'lang' =>  $model->data['language'],
       // 'id_user'=> $model->inc->user->getId(),
       // 'last_modified' => date('H-m-d H:i:s')
      ])){
        $new ++;
        $id = $model->db->lastId();
        $model->db->insertIgnore(
          'bbn_i18n_exp', [
            'id_exp' => $id,
            'expression'=> $i,
            'lang' => $model->data['language']
          ]
        );
      }
    }
    $res[$idx] = [
      'id_exp' => $row['id'],
      'exp'=> $i
    ];
    foreach( $configured_langs as $lang ){
      if ( $exp = $model->db->selectOne('bbn_i18n_exp', 'expression', [
        'id_exp' => $row['id'],
        'lang' => $lang
      ]) ){
        $res[$idx][$lang.'_db'] = $exp;
      }
      else {
        $res[$idx][$lang.'_db'] = '';
      }
    }
  }
  return [
    'languages' => $configured_langs,
    //i prefer to call this property in the same  way of other projects to avoid problem with strings table
    'path_source_lang' => $model->data['language'],
    'new' => $new,
    'success' => true,
    'strings' => $res
  ];
}
