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
/** @var (array) $projects from db*/
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


  $new = 0;


  $i = 0;
  $res = [];

  /** operation to instantiate the class ide */
  $path = $model->plugin_path('appui-ide');
  $model->register_plugin_classes($path);
  $ide = new \appui\ide($model->inc->options, $model->data['routes'], $model->inc->pref);

  if ( !empty($languages) ){
    $po_file = [];

    foreach ( $languages as $lng ){

      /** the path of po and mo files */
      $po = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.po';
      $mo = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.mo';
      /** if the file po exist takes its content */
      if (file_exists($po)){
        $success = true;
        $translations = Gettext\Translations::fromPoFile($po);
        foreach ($translations as $i => $t ){
          /** @var  $original the original expression */
          $original = $t->getOriginal();
          $po_file[$i][$lng]['original'] = $original;
          /** the translation of the string found in the po file */
          $po_file[$i][$lng]['translations_po'] = $t->getTranslation();
          /** @var  $id takes the id of the original expression in db */
          if ( $id = $model->db->select_one('bbn_i18n',
            'id',
            [
              'exp' => $original,
              'lang' => $path_source_lang
            ]) ){
            /** the translation of the string found in db */
            $po_file[$i][$lng]['translations_db'] = $model->db->select_one('bbn_i18n_exp', 'expression', ['id_exp' => $id, 'lang' => $lng]);
            /** the id of the string */
            $po_file[$i][$lng]['id_exp'] = $id;
            /** @var (array) takes $paths of files in which the string was found from the file po */
            $paths = $t->getReferences();
            /** get the url to use it for the link to ide from the table */
            foreach ( $paths as $p ){
              $po_file[$i][$lng]['paths'][] = $ide->real_to_url($p[0]);
            }
            /** the number of times the strings is found in the files of the path  */
            $po_file[$i][$lng]['occurrence'] = !empty($po_file[$i][$path_source_lang]) ? count($po_file[$i][$path_source_lang]['paths']) : 0;
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