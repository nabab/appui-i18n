<?php
/*
 * Describe what it does!
 *
 **/
use Gettext\Translations;

/** @var $this \bbn\mvc\model*/

if (
  isset($model->data['id_option']) &&
  ($o = $model->inc->options->option($model->data['id_option'])) &&
  ($parent = $model->inc->options->parent($o['id'])) &&
  defined($parent['code'])
){
  $success = false;

  /** $to_explore is the directory to explore for strings */
  $to_explore = constant($parent['code']).$o['code'];
  $locale_dir = dirname($to_explore).'/locale';
  $domain = $o['text'];
  /** property language of the path */
  $model->data['language'] = $model->inc->options->get_prop($model->data['id_option'], 'language');

  /** @var (array)$data creates a cached model of the strings found in the files using the action find_strings */
  $data = $model->get_cached_model(APPUI_I18N_ROOT.'actions/find_strings', ['id_option'=> $model->data['id_option'], 'language'=> $model->data['language']], true);
  /*$data = $model->get_model(APPUI_I18N_ROOT.'actions/find_strings', ['id_option'=> $model->data['id_option'], 'language'=> $model->data['language']]);*/

  /** @var (boolean) $no_strings case of empty($data['res']), there are no strings in this path . Return true if there are no strings from find_strings*/
  $no_strings = false;
  /** $data['res'] is the array of strings */
  if ( !empty($data['res'] )){
    /**  case generate called from strings table */
    if ( !isset($model->data['languages']) ){
      $languages = array_map(function($a){
        return basename($a);
      }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];
    }
    else {
      /** case generate from widget */
      $languages = $model->data['languages'];
      /** @var  (array) $old_langs languages in locale folder before of this call */
      $old_langs = $data['languages'];

      /** @var (array) $ex_dir languages unchecked in the form */
      if ( !empty($ex_dir = array_diff($old_langs, $languages)) ){
        foreach ( $ex_dir as $ex ){
          /** index of ex lang in $languages */
          $idx = array_search($ex, $languages);
          /** removes the $ex (language unchecked) from the final array languages */
          array_splice($languages, $idx);
          /** @var $dir the path to delete*/
          $dir = $locale_dir . '/' . $ex;
          \bbn\file\dir::delete($dir);
        }
      }
      $new_dir = [];
      /** @var $new_dir new languages checked in the form */
      if ( !empty($new_dir = array_diff($languages, $old_langs ) ) ){
        foreach( $new_dir as $n ){
          /** checks if the language already exists in the array languages */
          if ( empty(in_array($n, $languages) ) ){
            $languages[] = $n;
          }
        }
      }
    }
    $translations = [];

    clearstatcache();
    $dir = '';
    foreach ( $languages as $lang ){
      /** @var $dir the path of locale dir for this id_option foreach lang */
      $dir = $locale_dir . '/' . $lang . '/LC_MESSAGES';
      /** creates the path of the dirs */
      \bbn\file\dir::create_path($dir);
      /** @var  $po & $mo files path */
      $po = $locale_dir.'/'.$lang.'/LC_MESSAGES/'.$domain.'.po';
      $mo = $locale_dir.'/'.$lang.'/LC_MESSAGES/'.$domain.'.mo';
      /** checks if the file po exist for this lang */
      if ( is_file($po) ){
        //die(var_dump($po));
        /** $translations[$lang] takes the content from the existing file */
        $translations[$lang] = Translations::fromPoFile($po);
        /** deletes po and mo files */
        if ( \bbn\file\dir::delete($po) ){
          $translations[$lang] = new Gettext\Translations();
        }
        //@unlink($po);
        //@unlink($mo);
      }
      else{
        /** if the po files doesn't exist instantiate the object $translations[$lang] to the class Gettext\Translations()*/
        $translations[$lang] = new Gettext\Translations();
      }
      /** configuration of po file */
      $translations[$lang]->setHeader('Project-Id-Version', 1);
      $translations[$lang]->setHeader('Last-Translator', 'BBN Solutions <support@bbn.solutions>');
//    $translations[$lang]->setHeader('Report-Msgid-Bugs-To', 'BBN Solutions <support@bbn.solutions>');
      $translations[$lang]->setHeader('POT-Creation-Date', date('Y-m-d H:iO'));
      $translations[$lang]->setHeader('PO-Revision-Date', date('Y-m-d H:iO'));
      $translations[$lang]->setHeader('Language-Team', strtoupper($lang).' <'.strtoupper($lang).'@li.org>');
      $translations[$lang]->setHeader('MIME-Version', '1.0');
      $translations[$lang]->setHeader('Content-Type', 'text/plain; charset=UTF-8');
      //$translations[$lang]->setHeader('Content-Transfer-Encoding', '8bit');
      $translations[$lang]->setDomain($o['text']);
      $translations[$lang]->setPluralForms(0, '');
      $translations[$lang]->setLanguage($lang);
      /** @var takes all strings from the cached model of find_strings */
      foreach ( $data['res'] as $r ){
        //die(var_dump($data['res']));
        if ( !($t = $translations[$lang]->find('', $r['original_exp'])) ){
          /** @var $t if the original expression doesn't exist in the po file it creates $t  */
          $t = new Gettext\Translation(null, $r['original_exp']);
        }
        /** set the translation taking it from db $r[$lang] */
        $t->setTranslation($r[$lang]);
        /** @var set the path $p */
        foreach ( $r['path'] as $p ){
          $t->addReference($p, 1);
        }
        $translations[$lang][] = $t;
      }
      /** create the file po and mo with the content of  $translations[$lang]*/
      Gettext\Generators\Po::toFile($translations[$lang], $po);
      clearstatcache();
      Gettext\Generators\Mo::toFile($translations[$lang], $mo);
    }

    clearstatcache();


    /*
    $pp = $translation->get_parser();
    foreach ( $languages as $lang ){
      $path = $locale_dir.'/'.$lang.'/LC_MESSAGES';
      if ( \bbn\file\dir::create_path($path) ){
        Gettext\Generators\Po::toFile($pp, $path.'/zzzappui-styles.po');
        Gettext\Generators\Mo::toFile($pp, $path.'/appui-styles.mo');
      }
    }
    */

    /** remakes the cached_model of the widget to show changes */
    $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option'=> $model->data['id_option']], true);
    /** remakes the cached_model of the table to show changes */
    $model->get_cached_model(APPUI_I18N_ROOT.'page/data/strings_table', [
      'id_option' => $model->data['id_option'],
      'routes' => $model->data['routes'],
      ], true);

    $success = true;
  }
  else {
    $no_strings = true;
  }

  return [
    'no_strings' => $no_strings,
    'new_dir' => array_values($new_dir),
    'ex_dir'=> array_values($ex_dir),
    'path' => $to_explore,
    'locale' => $locale_dir,
    'languages' => $languages,
    'success' => $success
  ];
}