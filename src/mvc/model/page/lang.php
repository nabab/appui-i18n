<?php
/**
 * Created by BBN Solutions.
 * User: Loredana Bruno
 * Date: 15/01/18
 * Time: 10.18
 */



//statistic for specific language after the post of $lang and $source_lang by the selection of dropdown in statistic's tab

if ( $lang = $model->data['lang'] ){
  $source_lang = $model->data['source_lang'];
  $model->data['success'] = false;

  //source of the dropdown of source languages
  $dropdown_langs = $model->get_model('internationalization/page/statistic')['dropdown_langs'];
  //source of dropdown of translation languages
  $source_dd_langs = $model->get_model('internationalization/page/statistic')['source_dd_langs'];

  $lang_name = '';
  if ( !empty($lang) || !empty($source_lang) ){

    //name of source language
    foreach ( $dropdown_langs as $d => $val){
      if ( !empty($dropdown_langs[$d]['code'] === $lang )){
        $lang_name = $dropdown_langs[$d]['text'];
      }
    }
    //name of translation language
    foreach ( $source_dd_langs as $dd => $value ){
      if ( !empty($source_dd_langs[$dd]['code'] === $source_lang )){
        $source_lang_name = $source_dd_langs[$dd]['text'];
      }
    }

    //total number of strings in the selected source_lang
    $source_total_strings = $model->db->count('bbn_i18n', ['lang' => $source_lang]);

    //an array with all id_exp for the selected source_language translated in $lang
    $translated_nr = count( $model->db->get_rows("
      SELECT bbn_i18n.id FROM `bbn_i18n` 
        JOIN bbn_i18n_exp 
          ON bbn_i18n_exp.id_exp = bbn_i18n.id
          AND bbn_i18n_exp.bbn_h = 1
         AND bbn_i18n_exp.lang LIKE ?
         AND bbn_i18n_exp.bbn_h = 1
       WHERE bbn_i18n.lang LIKE ? 
       AND bbn_i18n.bbn_h = 1
       ", $lang, $source_lang));

    $translated_percentage = round($translated_nr / $source_total_strings * 100, 2).' %';

    $langs_in_db = $model->db->get_col_array("SELECT DISTINCT lang FROM bbn_i18n_exp WHERE bbn_h = 1");

   /* array_map(function($v)use($source_lang){

      return  $v['exps'] = count( $model->db->get_rows("
      SELECT bbn_i18n.id FROM `bbn_i18n` 
        JOIN bbn_i18n_exp 
          ON bbn_i18n_exp.id_exp = bbn_i18n.id
         AND bbn_i18n_exp.lang LIKE ?
         AND bbn_i18n_exp.actif = 1
       WHERE bbn_i18n.lang LIKE ? 
       ", $v, $source_lang));

      }, $langs_in_db );


    die(var_dump($langs_in_db));*/


  }

  return [
    'lang_statistic' => [
      'langs_nr' => $langs_in_db,
      'translated_nr' => $translated_nr,
      'translatedPercentage' => $translated_percentage,
      'source_total_strings' => $source_total_strings,
      'lang_name' => $lang_name,
      'source_lang' => $source_lang_name,
    ],
    'success' => true,

  ];
}