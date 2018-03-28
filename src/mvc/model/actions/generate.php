<?php
/*
 * Describe what it does!
 *
 **/
use Gettext\Translations;

/** @var $this \bbn\mvc\model*/


if (
  isset($model->data['id']) &&
  ($o = $model->inc->options->option($model->data['id'])) &&
  ($parent = $model->inc->options->parent($o['id'])) &&
  defined($parent['code'])
){
  $success = false;
  //$to_explore is the directory to explore for strings
  $to_explore = constant($parent['code']).$o['code'];
  $locale_dir = dirname($to_explore).'/locale';
  $domain = $o['text'];
  //take the source lang of id_option

  $model->data['language'] = $model->inc->options->get_prop($model->data['id'], 'language');


  //takes the cached_model of strings_table for this path, the third argument of get_cached_model is true to remake the cache
  if ( $data = $model->get_model(APPUI_I18N_ROOT.'actions/find_strings', ['id_option'=> $model->data['id'], 'language'=> $model->data['language']]) ){

    $languages = $data['languages'];


    $translations = [];

    clearstatcache();
    foreach ( $languages as $lang ){
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
        die(var_dump($r['translation']));
        $t->setTranslation($r['translation'][$lang]);
        //die(var_dump($t->setTranslation($r['translation'][$lang])));
        /*if ( $r['translation'][$lang] === 'Référence' ){
          //die(var_dump($t));
        }*/
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
    //die(var_dump($translations));

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
    //die(var_dump(is_dir($to_explore), $to_explore, $res, $pp));
    $success = true;
  }

  return [
    'path' => $to_explore,
    'locale' => $locale_dir,
    'languages' => $languages,
    'success' => $success
  ];
}