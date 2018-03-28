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


  $languages = [];

  /** @var  $languages array of dirs name in locale folder*/
  $languages = $model->get_cached_model(APPUI_I18N_ROOT.'languages_tabs/data/widgets', ['id_option' => $id_option])['locale_dirs'];


  /** @var  $translation instantiate the class appui\i18n*/
  $translation = new \bbn\appui\i18n($model->db);
  /** @var  $expressions found in the path*/

  $expressions = $translation->analyze_folder($to_explore, true);
  $new = 0;

  //$r is the string, $val is the array of files in which this string is contained
  $i = 0;
  $res = [];

  if ( !empty($languages) ){
    $po_file = [];
    foreach ( $languages as $lng ){
      //create a property indexed to the code of $lng containing the string $r from 'bbn_i18n_exp' in this $lng
      $po = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.po';
      $mo = $locale_dir.'/'.$lng.'/LC_MESSAGES/'.$o['text'].'.mo';
      if (file_exists($po)){
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

          };
        }
      }
    }
    $strings = [];

    //die(var_dump(array_values($po_file), $languages));
  }

  return [
    //'po' => $po_file,
    'path_source_lang' => $path_source_lang,
    'path' => $o['text'],
    'success' => $success,
    'new' => $new,
    'languages' => $languages,
    'total' => count($res),
    'strings' => array_values($po_file),
    'id_option' => $model->data['id_option'],
    'time' => $timer->measure()
  ];
}


