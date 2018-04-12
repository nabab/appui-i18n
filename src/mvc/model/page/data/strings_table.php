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

$projects = $model->get_model(APPUI_I18N_ROOT.'page')['projects'];

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


  $languages = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];


  /** @var  $languages array of dirs name in locale folder*/
  $widget = $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option' => $id_option ], true);

  /** @var  $translation instantiate the class appui\i18n*/
  $translation = new \bbn\appui\i18n($model->db);
  /** @var  OLD $expressions found in the path*/
  //$expressions = $translation->analyze_folder($to_explore, true);

  //gets the strings from the action find_strings
//  $expressions = $model->get_model(APPUI_I18N_ROOT.'actions/find_strings', ['id_option' => $id_option, 'language' => $path_source_lang])['res'];
  $new = 0;

  //$r is the string, $val is the array of files in which this string is contained
  $i = 0;
  $res = [];

  $path = $model->plugin_path('appui-ide');
  $model->register_plugin_classes($path);
  $ide = new \appui\ide($model->inc->options, $model->data['routes'], $model->inc->pref);

  if ( !empty($languages) ){
    $po_file = [];
    foreach ( $languages as $lng ){
      //create a property indexed to the code of $lng containing the string $r from 'bbn_i18n_exp' in this $lng
      $po = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.po';
      $mo = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.mo';
      if (file_exists($po)){
        $success = true;
        $translations = Gettext\Translations::fromPoFile($po);

        foreach ($translations as $i => $t ){
          $original = $t->getOriginal();
          $po_file[$i][$lng]['original'] = $original;
          $po_file[$i][$lng]['translations_po'] = $t->getTranslation();
          if ( $id = $model->db->select_one('bbn_i18n',
            'id',
            [
              'exp' => $original,
              'lang' => $path_source_lang
            ]) ){

            $po_file[$i][$lng]['translations_db'] = $model->db->select_one('bbn_i18n_exp', 'expression', ['id_exp' => $id, 'lang' => $lng]);
            $po_file[$i][$lng]['id_exp'] = $id;
            //gets the paths in which the original string was found
            $paths = $t->getReferences();
            foreach ( $paths as $p ){
              $po_file[$i][$lng]['paths'][] = $ide->real_to_url($p[0]);
            }
          };
        }
      }
    }



  }

  return [
    //'po' => $po_file,
    'path_source_lang' => $path_source_lang,
    'path' => $o['text'],
    'success' => $success,
    'new' => $new,
    'languages' => $languages,
    'total' => count(array_values($po_file)),
    'strings' => array_values($po_file),
    'id_option' => $model->data['id_option'],
    'time' => $timer->measure()
  ];
}