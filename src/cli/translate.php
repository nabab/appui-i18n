<?php

Use bbn\X;
use Gettext\Translations;

/** @var  $projects array of projects*/
$projects = [];
//$id_project = $ctrl->data['id_project'] ?? $ctrl->inc->options->fromCode('my-project', 'project', 'appui');
if (($opt_projects = $ctrl->inc->options->fromCode('list', 'project', 'appui'))
    && ($uid_languages = $ctrl->inc->options->fromCode('languages', 'core', 'appui'))
    && ($ids = $ctrl->inc->options->items($opt_projects))
    && ($languages = $ctrl->inc->options->fullOptions($uid_languages))
) {
  $filter    = array_filter(
    $languages, function ($v) {
      return !empty($v['primary']);
    }
  );
  $opt       =& $ctrl->inc->options;
  $primaries = array_values($filter);
  foreach ($ids as $id_project) {
    $project = new \bbn\Appui\Project($ctrl->db, $id_project);
    $info    = $project->getProjectInfo();
    //X::log("project  ".$info['name'], 'languages');
    if (!empty($info['path'])) {
      $translation = new \bbn\Appui\I18n($ctrl->db, $info['id']);
      foreach ($info['path'] as $idx => $o) {
        //X::log("path ------------------ $o[code]", 'languages');
        /** for every project takes the full option of each path */
        if ($res_idx = $opt->option($info['path'][$idx]['id_option'])) {
          $res[$idx] = $res_idx;
        }

        /** if language is set takes the cached_model_of the widget */
        if (X::hasProps($res[$idx], ['language', 'id'], true)) {
          //the id_option of the widget
          $id_option = $info['path'][$idx]['id_option'];
          $parent    = $opt->parent($id_option);
          $root      = $ctrl->{$parent['code'].'Path'}();
          $domain    = $o['code'];
          $orig_lang = $opt->getProp($id_option, 'language');
          $lng_codes = array_map(
            function ($a) use (&$opt) {
              return $opt->code($a);
            },
            $info['langs']
          );
          if ($root
              && $orig_lang
              && ($data = $translation->getTranslationsStrings($id_option, $orig_lang, $lng_codes))
              && !empty($data['res'])
          ) {
            if ($domain === 'appui-option') {
              //X::log($data);
            }
            //X::log("path in ".$orig_lang, 'languages');
            $index_file = $translation->getIndexPath($id_option);
            //X::log("index_file: $index_file", 'languages');
            //X::log("NUM res: ".count($data['res']), 'languages');

            $num        = is_file($index_file) ? (int)file_get_contents($index_file) : 0;
            $num        = (string)($num + 1);
            file_put_contents($index_file, $num);
            /** @var string $to_explore The directory to explore for strings */
            $to_explore = $translation->getPathToExplore($id_option);
            //the position of locale dir
            $locale_dir = $translation->getLocaleDirPath($id_option);
            clearstatcache();

            $dir = '';
            $js_files = [];
            foreach ($lng_codes as $lang){
              //X::log("LANG $lang", 'languages');
              //X::log("LOCALE DIR: $locale_dir", 'languages');
              $js_files[$lang] = [];

              /** @var string $dir The path of locale dir for this id_option foreach lang */
              $dir = $locale_dir . '/' . $lang . '/LC_MESSAGES';

              /** creates the path of the dirs */
              \bbn\File\Dir::createPath($dir);
              /** @var  $po & $mo files path */
              $files = \bbn\File\Dir::getFiles($locale_dir.'/'.$lang.'/LC_MESSAGES');
              $po    = $mo = null;
              foreach ($files as $f){
                $ext = \bbn\Str::fileExt($f);
                if (!empty($ext) && ($ext === 'po')) {
                  $po = $f;
                  unlink($f);
                }

                if (!empty($ext) && ($ext === 'mo')) {
                  $mo = $f;
                  unlink($f);
                }
              }

              // the new files
              $new_po = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.$num.'.po';
              $new_mo = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.$num.'.mo';

              //create the file at the given path
              fopen($new_po, 'x');

              //instantiate the parser
              $fileHandler  = new Sepia\PoParser\SourceHandler\FileSystem($new_po);
              $poParser     = new Sepia\PoParser\Parser($fileHandler);
              $Catalog      = Sepia\PoParser\Parser::parseFile($new_po);
              $Compiler     = new Sepia\PoParser\PoCompiler();
              $headersClass = new Sepia\PoParser\Catalog\Header();

              if (empty($Catalog->getHeaders())) {
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
              foreach ($data['res'] as $index => $r){
                if (empty($Catalog->getEntry($r['original_exp']))) {
                  //prepare the new entry for the Catalog
                  $entry = new Sepia\PoParser\Catalog\Entry($r['original_exp'], $r[$lang]);

                  // set the reference for the entry
                  if (!empty($r['path'])) {
                    $entry->setReference($r['path']);

                    foreach($r['path'] as $idx => $path){
                      $name = '';

                      $ext = pathinfo($path, PATHINFO_EXTENSION);


                      if ($ext === 'js') {
                        $tmp = substr($r['path'][$idx], strlen($root), -3);
                        if (strpos($tmp, 'components/') === 0) {
                          $bits = X::split($tmp, '/');
                          // double name (dir/file)
                          array_pop($bits);
                          $name = X::join($bits, '/');
                        }
                        elseif (strpos($tmp, 'mvc/') === 0) {
                          if (strpos($tmp, 'mvc/js/') === 0) {
                            $tmp = 'mvc/'.substr($tmp, 7);
                          }

                          $name = $tmp;
                        }
                        //case of plugins inside current app, temporary we decided to don't take it inside the json file of the app
                        elseif ((strpos($tmp, 'plugins/') === 0)) {//} && ($parent['code'] === 'app')) {
                          continue;
                        }
                        elseif (strpos($tmp, 'bbn/') === 0) {
                          $bits = X::split($tmp, '/');
                          // bbn
                          array_shift($bits);
                          // plugin name
                          array_shift($bits);
                          // src
                          array_shift($bits);
                          if ($bits[0] === 'components') {
                            // double name (dir/file)
                            array_pop($bits);
                            $name = X::join($bits, '/');
                          }
                          elseif (($bits[0] === 'mvc') && ($bits[1] === 'js')) {
                            // removing js folder
                            array_splice($bits, 1, 1);
                            $name = X::join($bits, '/');
                          }
                          elseif ($bits[0] === 'bbn') {
                            array_shift($bits);
                            $name = X::join($bits, '/');
                          }
                          else {
                            //X::log($tmp, 'lost');
                          }
                        }

                        if (!empty($name)) {
                          if (empty($js_files[$lang][$name])) {
                            $js_files[$lang][$name] = [];
                          }

                          //array of all js files found in po file
                          $js_files[$lang][$name][$r['original_exp']] = $r[$lang];
                        }
                        else {
                          //X::log($tmp, 'lost');
                        }
                      }
                    }
                  }

                  //add the prepared entry to the catalog
                  $Catalog->addEntry($entry);
                }
              }

              //compile the catalog
              $file = $Compiler->compile($Catalog);

              //save the catalog in the file
              $fileHandler->save($file);
              if ($fromPo = Translations::fromPoFile($new_po)) {
                // put the content of po file in mo file
                Gettext\Generators\Mo::toFile($fromPo, $new_mo);
              };
              clearstatcache();

              if (!empty($js_files[$lang])) {
                $file_name = $locale_dir.'/'.$lang.'/'.$lang.'.json';
                \bbn\File\Dir::createPath(dirname($file_name));
                // put the content of the array js_files in a json file
                //X::log("PUTTING JS IN $file_name WITH ".count($js_files[$lang])." translations", 'languages');
                $json = (boolean)file_put_contents($file_name, json_encode($js_files[$lang], JSON_PRETTY_PRINT));
              }
            }

            clearstatcache();



            /** @var array The data for the strings table */
            $tmp = $translation->getTranslationsTable($id_project, $id_option);

            //Set the cache of the table
            $translation->cacheSet(
              $id_option, 'get_translations_table',
              $tmp
            );
            /** @var array The data of the table in cache */
            $strings = $translation->cacheGet($id_option, 'get_translations_table');

            /*
            //set the cache of the widget
            $translation->cacheSet(
              $id_option,
              'get_translations_widget',
              $translation->getTranslationsWidget$id_option)
            );

            /** @var array The data of the widget in the cache
            $widget  = $translation->cacheGet($id_option, 'get_translations_widget');
            */
            $success = true;
          }
          else {
            $no_strings = true;
          }





          //if the widget has not cache for this method creates the cache
          //IF THE CACHE IS ACTIVE WHEN THE PROJECT IS CHANGED BY THE DROPDOWN IT RETURNS THE WIDGETS OF THE DEFAULT PROJECT
          /*if ( empty($translation->cacheHas($id_option, 'get_translations_widget')) ){
            //set data in cache $translation->cacheSet($id_option, (string)method name, (array)data)
            $translation->cacheSet($id_option, 'get_translations_widget',
              $translation->getTranslationsWidget($id_option)
            );
          }
          $res[$idx]['data_widget'] = $translation->cacheGet($id_option, 'get_translations_widget');*/
          $res[$idx]['data_widget'] = $translation->getTranslationsWidget($id_option);
        }
        else {
          /** if language is not set returns the array data_widget with locale_dirs and an empty array for result */
          $res[$idx]['data_widget']                = [];
          $res[$idx]['data_widget']['locale_dirs'] = [];
          $res[$idx]['data_widget']['result']      = [];
        }

        $res[$idx]['title'] = $ctrl->inc->options->text($res[$idx]['id_parent']).'/'.$res[$idx]['text'];
      }

      $success = true;
    }
  }
}
