<?php
/**
 * Created by PhpStorm.
 * User: bbn
 * Date: 21/03/18
 * Time: 10.09
 */
use Gettext\Translations;
$timer = new \bbn\util\timer();
$timer->start();

$projects = $model->get_model(APPUI_I18N_ROOT.'languages_tabs')['projects'];

if ( !empty( $id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) ){

  /** @var  $path_source_lang the property language of the id_option (the path) */
  $path_source_lang = $model->inc->options->get_prop($id_option, 'language');

  /** @var  $to_explore the path to explore */
  $to_explore = constant($parent['code']).$o['code'];

  /** @var  $locale_dir locale dir in the path*/
  $locale_dir = dirname($to_explore).'/locale';

  /** @var  $result  takes the languages of locale dirs from widget cached_model*/
  $result = $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets', ['id_option' => $id_option])['result'];
  $languages = [];
  /*foreach ( $result as $r => $val){
    $languages[] = $r;
  }*/


  $languages = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];


  /** @var  $translation instantiate the class appui\i18n*/
  $translation = new \bbn\appui\i18n($model->db);
  /** @var  $expressions found in the path*/
  $expressions = $translation->analyze_folder($to_explore, true);

  $new = 0;

  //$r is the string, $val is the array of files in which this string is contained
  $i = 0;
  $res = [];
  foreach ( $expressions as $r => $val ){
    //for each string create a property 'path' containing the files' name in which the string is contained

    $res[$i] = ['path' => $val];

    //check if the table bbn_i18n of db already contains the string $r for this $project_lang

    if ( !($id = $model->db->select_one('bbn_i18n', 'id', [
      'exp' => $r,
      'lang' => $path_source_lang
    ])) ){

      //if the string $r is not in 'bbn_i18n' inserts the string
      $model->db->insert('bbn_i18n', [
        'exp' => $r,
        'last_modified' => date('Y-m-d H:i:s'),
        'id_user' => $model->inc->user->get_id(),
        'lang' => $path_source_lang,
      ]);
      $id = $model->db->last_id();
    }
    //create the property 'id_exp' for the string $r
    $res[$i]['id_exp'] = $id;

    //put the string $r into the property 'original_exp' (I'll use only array_values at the end)
    $res[$i]['original_exp'] = $r;

    //check in 'bbn_i18n_exp' table of db if the string $r exist for this $project_lang
    if( !( $id_exp = $model->db->select_one('bbn_i18n_exp', 'id_exp', [
      'id_exp' => $id,
      'lang' => $path_source_lang
    ]) ) ){
      //if the string $r is not in 'bbn_i18n_exp' inserts the string
      //$new will be the number of strings found in the folder $to_explore that has not been found in the table
      // 'bbn_i18n_exp' of db, so $new is the number of new strings inserted in in 'bbn_i18n_exp'
      $new += (int)$model->db->insert_ignore('bbn_i18n_exp', [
        'id_exp' => $id,
        'lang' => $path_source_lang,
        'expression' => $r
      ]);
    }

    /**var (array) the languages found in locale dir*/
    if ( !empty($languages) ){

      foreach ( $languages as $lng ){
        //create a property indexed to the code of $lng containing the string $r from 'bbn_i18n_exp' in this $lng
        $po = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.po';
        $mo = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.mo';
        if (file_exists($po)){
          $translations = Gettext\Translations::fromPoFile($po);

          $po_file = [];
          foreach ($translations as $t ){
            $po_file[$lng]['original'][] = $t->getOriginal();
            $po_file[$lng]['translations'][] = $t->getTranslation();
          }

          $res[$i]['translation'][$lng] = (string)$model->db->select_one(
            'bbn_i18n_exp',
            'expression',
            [
              'id_exp' => $id,
              'lang' => $lng
            ]
          );
        }
      }
    }

    $i++;
    $success = true;
  }

  return [
    'po' => $po_file,
    'path_source_lang' => $path_source_lang,
    'path' => $o['text'],
    'success' => $success,
    'new' => $new,
    'languages' => $languages,
    'total' => count($res),
    'strings' => $res,
    'id_option' => $model->data['id_option'],
    'time' => $timer->measure()
  ];
}


