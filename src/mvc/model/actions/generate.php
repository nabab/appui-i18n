<?php
/*
 * Describe what it does!
 *
 **/


/** @var $this \bbn\mvc\model*/

if (
  isset($model->data['id_option']) &&
  ($o = $model->inc->options->option($model->data['id_option'])) &&
  ($parent = $model->inc->options->parent($o['id'])) &&
  defined($parent['code']  )
){
  //instantiate the class i18n
  $translation = new \bbn\appui\i18n($model->db);
  $success = false;

  /** $to_explore is the directory to explore for strings */
  $to_explore = constant($parent['code']).$o['code'];
  $locale_dir = dirname($to_explore).'/locale';

  $domain = $o['text'];

  $num = (int)file_get_contents($locale_dir.'/index.txt');

  file_put_contents($locale_dir.'/index.txt', ++$num);
  $domain .= $num;
  /** @var (array) $languages based on locale dirs found in the path*/
  $old_langs = array_map(function($a){
    return basename($a);
  }, \bbn\file\dir::get_dirs($locale_dir)) ?: [];

  //creates the array languages
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
    //$old_langs = $data['languages'];

    /** @var (array) $ex_dir languages unchecked in the form */
    if ( !empty($ex_dir = array_diff($old_langs, $languages)) ){
      foreach ( $ex_dir as $ex ){
        /** index of ex lang in $languages */
        $idx = array_search($ex, $old_langs);

        /** removes the $ex (language unchecked) from the final array languages */
        array_splice($old_langs, $idx);

        /** @var $dir the path to delete*/
        $dir = $locale_dir . '/' . $ex;
        \bbn\file\dir::delete($dir);
      }
    }
    $new_dir = [];
    /** @var $new_dir new languages checked in the form */
    if ( !empty( $new_dir = array_diff( $languages, $old_langs ) ) ){
      foreach( $new_dir as $n ){

        /** checks if the language already exists in the array languages */
        if ( empty(in_array($n, $languages) ) ){
          $languages[] = $n;
        }
      }
    }
  }

  /** @var (array) takes all strings found in the files of  this option*/
  if ( !empty($o['language']) ){
    $data = $translation->get_translations_strings($model->data['id_option'],$o['language'], $languages);
  }



  /** @var (boolean) $no_strings case of empty($data['res']), there are no strings in this path . Return true if there are no strings from find_strings*/
  $no_strings = false;


  /** $data['res'] is the array of strings */
  if ( !empty($data['res'] )){
    $translations = [];

    clearstatcache();
    $dir = '';
    foreach ( $languages as $lang ){
      /** @var $dir the path of locale dir for this id_option foreach lang */
      $dir = $locale_dir . '/' . $lang . '/LC_MESSAGES';

      /** creates the path of the dirs */
      \bbn\file\dir::create_path($dir);
      /** @var  $po & $mo files path */
      $files = \bbn\file\dir::get_files($locale_dir.'/'.$lang.'/LC_MESSAGES');

      $po = $mo = null;
      foreach ( $files as $f ){
        $ext = \bbn\str::file_ext($f);
        if ( !empty($ext) && ($ext === 'po') ){
          $po = $f;
        }
        if ( !empty($ext) && ($ext === 'mo') ){
          $mo = $f;
        }
      }
    /** checks if the file po exist for this lang and deletes it*/
      if ( $po ){
        unlink($po);
      }
      // the new file
      $new_po = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.'.po';

     //  $new_mo = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.'.mo';
      //create the file at the given path
      fopen($new_po,'x');

      //instantiate the parser
      $fileHandler = new Sepia\PoParser\SourceHandler\FileSystem($new_po);
      $poParser = new Sepia\PoParser\Parser($fileHandler);
      $Catalog  = Sepia\PoParser\Parser::parseFile($new_po);
      $Compiler = new Sepia\PoParser\PoCompiler();
      $headersClass = new Sepia\PoParser\Catalog\Header();

      if( empty( $Catalog->getHeaders() ) ){
        //headers for new po file
        $headers = [
          "Project-Id-Version: 1",
          "Report-Msgid-Bugs-To: info@bbn.so",
          "last-Translator: BBN Solutions <support@bbn.solutions>",
          "Language-Team: ".strtoupper($lang).' <'.strtoupper($lang).'@li.org>',
          "MIME-Version: 1.0",
          "Content-Type: text/plain; charset=UTF-8",
          "Content-Transfer-Encoding: 8bit",
          "POT-Creation-Date: ".date('Y-m-d H:iO'),
          "POT-Revision-Date: ".date('Y-m-d H:iO'),
          "Language: ".$lang,
          "X-Domain: ".$domain,
          "Plural-Forms: nplurals=2; plural=n != 1;"
        ];
        //set the headers on the Catalog object
        $headersClass->setHeaders($headers);
        $Catalog->addHeaders($headersClass);

      }

      /** @var takes all strings from the cached model of find_strings */
      foreach ( $data['res'] as $r ){
        if ( empty($Catalog->getEntry($r['original_exp']) ) ){
          //prepare the new entry for the Catalog
          $entry = new  Sepia\PoParser\Catalog\Entry($r['original_exp'], $r[$lang]);
          // set the reference for the entry
          if ( !empty($r['path']) ){
            $entry->setReference($r['path']);
          }
          //add the prepared entry to the catalog
          $Catalog->addEntry($entry);
        }
      }
      //compile the catalog
      $file = $Compiler->compile($Catalog);
      //save the catalog in the file
      $fileHandler->save($file);
      clearstatcache();

    }

    clearstatcache();




    $tmp = $translation->get_translations_table($model->data['id_project'], $model->data['id_option']);

    $tmp2 = $translation->cache_set($model->data['id_option'], 'get_translations_table',
      $tmp
    );
    $strings = $translation->cache_get($model->data['id_option'], 'get_translations_table');

    //remake the cache of the widget basing on new data
    $translation->cache_set($model->data['id_option'], 'get_translations_widget',
      $translation->get_translations_widget($model->data['id_project'],$model->data['id_option'])
    );
    $widget = $translation->cache_get($model->data['id_option'], 'get_translations_widget');
    $success = true;
  }


  else {
    $no_strings = true;
  }

  return [
    'widget' => $widget,
    'no_strings' => $no_strings,
    'new_dir' => array_values($new_dir),
    'ex_dir'=> array_values($ex_dir),
    'path' => $to_explore,
    'locale' => $locale_dir,
    'languages' => $languages,
    'success' => $success,
    'strings' => $strings['strings'],

  ];
}
