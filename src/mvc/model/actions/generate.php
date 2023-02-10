<?php
/*
 * Describe what it does!
 *
 **/


/**
 * @var \bbn\Mvc\Model $model
 * @var array $o The option for the repository to explore
 * @var array $parent The root of the repo where the code is the root constant
 */
use Gettext\Translations;

if (($o = $model->data['id_option'])
  && ($idProject = $model->data['id_project'])
  && ($language = $model->data['language'])
) {
  $parent = $model->inc->options->parent($o);
  /** @var bool $json Will be true if some translations are put into a JSON file */
  $json = false;
  /** @var array $js_files An array with files as index and an array expressions to put into the JSON file */
  $js_files = [];
  //instantiate the class i18n
  $translation = new \bbn\Appui\I18n($model->db, $idProject);
  //die(var_dump($translation->getPoFiles($model->data['id_option'])));
  $success = false;
  /** @var string $to_explore The directory to explore for strings */
  $to_explore = $translation->getPathToExplore($o);
  //the position of locale dir
  $locale_dir = $translation->getLocaleDirPath($o);
  /** @var string $domain The domain on which will be bound gettext */
  $domain = $model->inc->options->text($o);
  //the number contained in the txt file inside the folder locale
  $num = is_file($translation->getIndexPath($o)) ? (int)file_get_contents($translation->getIndexPath($o)) : 0;
  //if the file txt does not exist it creates the file
  if (!is_file($translation->getIndexPath($o)) || !is_dir($locale_dir)) {
    \bbn\File\Dir::createPath($locale_dir);
  }
  file_put_contents($translation->getIndexPath($o), ++$num);

  $domain .= $num;

  /** @var (array) $languages based on locale dirs found in the path */
  $old_langs = array_map('basename', \bbn\File\Dir::getDirs($locale_dir));

  //creates the array languages
  // case when generation is called from the strings table
  if (!isset($model->data['languages'])) {
    $languages = $old_langs;
  }
  else {
    /** @var array $languages Is set in the case the generation comes from a widget */
    $languages = $model->data['languages'];
    $ex_dir    = [];
    /** @var array $ex_dir Languages unchecked in the form */
    if (!empty($ex_dir = array_diff($old_langs, $languages))) {
      foreach ($ex_dir as $ex) {
        // index of ex lang in $languages
        $idx = array_search($ex, $old_langs, true);

        // removes the $ex (language unchecked) from the final array languages
        array_splice($old_langs, $idx, 1);
        \bbn\File\Dir::delete($locale_dir . '/' . $ex);
      }
    }

    /** @var array $new_dir New languages checked in the form */
    $new_dir = [];

    if (!empty($new_dir = array_diff($languages, $old_langs))) {
      foreach($new_dir as $n){
        // checks if the language already exists in the array languages
        if (!in_array($n, $languages, true)) {
          $languages[] = $n;
        }
      }
    }
  }

  /** @var array $data Takes all strings found in the files of  this option */
  $data = empty($language) ? [] : $translation->getTranslationsStrings($model->data['id_option'], $language, $languages);

  /** @var bool $no_strings Will be true if $data[res] is empty, i.e. if find_strings returns no result. */
  $no_strings = false;

  // $data['res'] is the array of strings
  if (!empty($data['res'])) {
    clearstatcache();
    $dir = '';
    foreach ($languages as $lang){
      /** @var string $dir The path of locale dir for this id_option foreach lang */
      $dir = $locale_dir . '/' . $lang . '/LC_MESSAGES';

      /** creates the path of the dirs */
      \bbn\File\Dir::createPath($dir);
      /** @var  $po & $mo files path */
      $files = \bbn\File\Dir::getFiles($locale_dir.'/'.$lang.'/LC_MESSAGES');

      $po = $mo = null;
      foreach ($files as $f){
        $ext = \bbn\Str::fileExt($f);
        if (!empty($ext) && ($ext === 'po')) {
          $po = $f;
        }
        if (!empty($ext) && ($ext === 'mo')) {
          $mo = $f;
        }
      }

      /** checks if the file po exist for this lang and deletes it*/
      if ($po) {
        unlink($po);
      }

      if ($mo) {
        unlink($mo);
      }

      // the new files
      $new_po = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.'.po';
      $new_mo = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.'.mo';

      //create the file at the given path
      fopen($new_po,'x');

      //instantiate the parser
      $fileHandler = new Sepia\PoParser\SourceHandler\FileSystem($new_po);
      $poParser    = new Sepia\PoParser\Parser($fileHandler);
      $catalog     = Sepia\PoParser\Parser::parseFile($new_po);

      $compiler     = new Sepia\PoParser\PoCompiler();
      $headersClass = new Sepia\PoParser\Catalog\Header();

      if (empty($catalog->getHeaders())) {
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
        $catalog->addHeaders($headersClass);
      }

      $constroot = 'BBN_'.strtoupper($parent['code']).'_PATH';
      if (!defined($constroot)) {
        X::log($model->inc->option->option($o));
        throw new \Exception("Impossible to find the root for option, see Misc log");
      }

      $root = constant($constroot);

      /** @var takes all strings from the cached model of find_strings */
      foreach ($data['res'] as $index => $r){
        if (empty($catalog->getEntry($r['original_exp']))) {
          //prepare the new entry for the Catalog
          $entry = new Sepia\PoParser\Catalog\Entry($r['original_exp'], $r[$lang]);

          // set the reference for the entry
          if (!empty($r['path'])) {
            $entry->setReference($r['path']);

            foreach($r['path'] as $idx => $path){
              $name = '';

              $ext = pathinfo($path, PATHINFO_EXTENSION);

              if (($ext === 'js')
                || ($ext === 'php')
                || ($ext === 'html')
              ) {
                $tmp = substr($r['path'][$idx], strlen($root), -3);

                if (strpos($tmp, 'components') === 0) {
                  $name = dirname($tmp);
                }
                elseif (strpos($tmp, 'mvc') === 0) {
                  if (strpos($tmp, 'js/')) {
                    $name = str_replace('js/', '', $tmp);
                  }
                  else if (strpos($tmp, 'html/')) {
                    $name = str_replace('html/', '', $tmp);
                  }
                }
                //case of plugins inside current (apst-app), temporary we decided to don't take it inside the json file of apst-app
                elseif ((strpos($tmp, 'plugins') === 0) && ($root === BBN_APP_PATH)) {
                  continue;
                }

                elseif (strpos($tmp, 'bbn/') === 0) {
                  $optCode = $model->inc->options->code($o);
                  $tmp  = str_replace($optCode.'/', '', substr($tmp, 4));
                  if (strpos($tmp, 'components') === 4) {
                    $final = str_replace(substr($tmp, 0,4), '', $tmp);
                    $name = dirname($final);
                  }
                  elseif (strpos($tmp, 'mvc') === 4) {
                    if ((strpos($tmp, 'js/') !== 8)
                      && (strpos($tmp, 'html/') !== 8)
                    ) {
                      continue;
                    }
                    $final = str_replace(substr($tmp, 0, 4), '', $tmp);
                    $name  = preg_replace(['/js\//', '/html\//'], '', $final, 1);
                  }
                }

                if (empty($js_files[$lang][$name])) {
                  $js_files[$lang][$name] = [];
                }
                //array of all js files found in po file
                $js_files[$lang][$name][$data['res'][$index]['original_exp']] = $data['res'][$index][$lang];
              }
            }
          }

          //add the prepared entry to the catalog
          $catalog->addEntry($entry);
        }
      }

      //compile the catalog
      $file = $compiler->compile($catalog);

      //save the catalog in the file
      $fileHandler->save($file);
      clearstatcache();

      if (!empty($js_files[$lang])) {
                $file_name = $locale_dir.'/'.$lang.'/'.$lang.'.json';

        \bbn\File\Dir::createPath(dirname($file_name));
        // put the content of the array js_files in a json file
        $json = (boolean)file_put_contents($file_name, json_encode($js_files[$lang]));
      }
    }

    clearstatcache();



    /** @var array The data for the strings table */
    $tmp = $translation->getTranslationsTable($model->data['id_project'], $model->data['id_option']);

    //Set the cache of the table
    $translation->cacheSet(
      $model->data['id_option'], 'get_translations_table',
      $tmp
    );
    /** @var array The data of the table in cache */
    $strings = $translation->cacheGet($model->data['id_option'], 'get_translations_table');

    //set the cache of the widget
    $translation->cacheSet(
      $model->data['id_option'], 'get_translations_widget',
      $translation->getTranslationsWidget($model->data['id_project'],$model->data['id_option'])
    );

    /** @var array The data of the widget in the cache*/
    $widget  = $translation->cacheGet($model->data['id_option'], 'get_translations_widget');
    $success = true;
  }
  else {
    $no_strings = true;
  }

  return [
    'json' => $json,
    'widget' => $widget ?? null,
    'no_strings' => $no_strings,
    'new_dir' => $new_dir,
    'ex_dir' => $ex_dir,
    'path' => $to_explore,
    'locale' => $locale_dir,
    'languages' => $languages,
    'success' => $success,
    'strings' => empty($strings) ? [] : $strings['strings']
  ];
}
