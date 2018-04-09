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
  //$to_explore is the directory to explore for strings
  $to_explore = constant($parent['code']).$o['code'];
  $locale_dir = dirname($to_explore).'/locale';
  $domain = $o['text'];
  //take the source lang of id_option

  $model->data['language'] = $model->inc->options->get_prop($model->data['id_option'], 'language');

  //takes the cached_model of strings_table for this path, the third argument of get_cached_model is true to remake the cache
  if ( $data = $model->get_cached_model(APPUI_I18N_ROOT.'actions/find_strings', ['id_option'=> $model->data['id_option'], 'language'=> $model->data['language']], true) ){

    //case generate from table
    if ( !isset($model->data['languages']) ){
      $languages = array_map(function($a){
        return basename($a);
      }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];
      ;
    }
    else {
      //case generate from widget
      $languages = $model->data['languages'];
      $old_langs = $data['languages'];
      if ( !empty($ex_dir = array_diff($old_langs, $languages)) ){
        foreach ( $ex_dir as $ex ){
          //index of ex lang in $languages
          $idx = array_search($ex, $languages);
          array_splice($languages, $idx);
          $dir = $locale_dir . '/' . $ex;
          \bbn\file\dir::delete($dir);
         }
      }
      $new_dir = [];
      if ( !empty($new_dir = array_diff($languages, $old_langs ) ) ){
        //$new_dir = array_diff($languages, $old_langs );
        foreach( $new_dir as $n ){
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
      $dir = $locale_dir . '/' . $lang . '/LC_MESSAGES';
      \bbn\file\dir::create_path($dir);
      $po = $locale_dir.'/'.$lang.'/LC_MESSAGES/'.$domain.'.po';
      $mo = $locale_dir.'/'.$lang.'/LC_MESSAGES/'.$domain.'.mo';
      if ( is_file($po) ){
        $translations[$lang] = Translations::fromPoFile($po);
        @unlink($po);
        @unlink($mo);
      }
      else{
        $translations[$lang] = new Gettext\Translations();
      }
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
      foreach ( $data['res'] as $r ){
        if ( !($t = $translations[$lang]->find('', $r['original_exp'])) ){
          $t = new Gettext\Translation(null, $r['original_exp']);
        }
        $t->setTranslation($r[$lang]);
        foreach ( $r['path'] as $p ){
          $t->addReference($p, 1);
        }
        $translations[$lang][] = $t;
      }
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
    $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option'=> $model->data['id_option']], true);


    $model->get_cached_model(APPUI_I18N_ROOT.'page/data/strings_table', ['id_option' => $ctrl->data['id_option']], true);

    $success = true;
  }

  return [
    'new_dir' => array_values($new_dir),
    'ex_dir'=> array_values($ex_dir),
    'path' => $to_explore,
    'locale' => $locale_dir,
    'languages' => $languages,
    'success' => $success
  ];
}