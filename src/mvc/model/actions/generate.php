<?php
/*
 * Describe what it does!
 *
 **/


/**
 * @var \bbn\mvc\model $model
 * @var array $o The option for the repository to explore
 * @var array $parent The root of the repo where the code is the root constant
 */
use Gettext\Translations;
if (
  ( $o = $model->data['id_option'] ) &&
  ( $id_project = $model->data['id_project'] ) &&
  ( $language = $model->data['language'] )
){

  
  /** @var bool $json Will be true if some translations are put into a JSON file */
  $json = false;
  /** @var array $js_files An array with files as index and an array expressions to put into the JSON file */
  $js_files = [];
  //instantiate the class i18n
  $translation = new \bbn\appui\i18n($model->db, $id_project);
  //die(var_dump($translation->get_po_files($model->data['id_option'])));
  $success = false;


  /** @var string $to_explore The directory to explore for strings */
  $to_explore = $translation->get_path_to_explore($o);
  //the position of locale dir 
  $locale_dir = $translation->get_locale_dir_path($o);

  /** @var string $domain The domain on which will be bound gettext */
  $domain = $model->inc->options->text($o);
  
  //the number contained in the txt file inside the folder locale
  $num = is_file($translation->get_index_path($o)) ? (int)file_get_contents($translation->get_index_path($o)) : 0;

  //if the file txt does not exist it creates the file
  if( !is_file($translation->get_index_path($o)) || !is_dir($locale_dir) ){
    \bbn\file\dir::create_path($locale_dir);
  }
  file_put_contents($translation->get_index_path($o), ++$num);
  
  $domain .= $num;

  /** @var (array) $languages based on locale dirs found in the path */
  $old_langs = array_map('basename', \bbn\file\dir::get_dirs($locale_dir));

  //creates the array languages
  // case when generation is called from the strings table
  if ( !isset($model->data['languages']) ){
    $languages = $old_langs;
  }
  else {
    /** @var array $languages Is set in the case the generation comes from a widget */
    $languages = $model->data['languages'];
    $ex_dir = [];
    /** @var array $ex_dir Languages unchecked in the form */
    if ( !empty($ex_dir = array_diff($old_langs, $languages)) ){
      foreach ( $ex_dir as $ex ){
        // index of ex lang in $languages
        $idx = array_search($ex, $old_langs, true);

        // removes the $ex (language unchecked) from the final array languages
        array_splice($old_langs, $idx, 1);
        \bbn\file\dir::delete($locale_dir . '/' . $ex);
      }
    }
    /** @var array $new_dir New languages checked in the form */
    $new_dir = [];
  
    if ( !empty( $new_dir = array_diff( $languages, $old_langs ) ) ){
      foreach( $new_dir as $n ){
        // checks if the language already exists in the array languages
        if ( !in_array($n, $languages, true) ){
          $languages[] = $n;
        }
      }
    }
  }

  /** @var array $data Takes all strings found in the files of  this option */
  $data = empty($language) ? [] : $translation->get_translations_strings($model->data['id_option'], $language, $languages);

  /** @var bool $no_strings Will be true if $data[res] is empty, i.e. if find_strings returns no result. */
  $no_strings = false;

  // $data['res'] is the array of strings
  if ( !empty($data['res'] )){
		clearstatcache();
    $dir = '';
    foreach ( $languages as $lang ){
      /** @var string $dir The path of locale dir for this id_option foreach lang */
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
      if ( $mo ){
        unlink($mo);
      }
      // the new files 
      $new_po = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.'.po';
      $new_mo = $locale_dir . '/' . $lang . '/LC_MESSAGES/'.$domain.'.mo';
    
      //create the file at the given path
      fopen($new_po,'x');

      //instantiate the parser
      $fileHandler = new Sepia\PoParser\SourceHandler\FileSystem($new_po);
      $poParser = new Sepia\PoParser\Parser($fileHandler);
      $Catalog  = Sepia\PoParser\Parser::parseFile($new_po);

      $Compiler = new Sepia\PoParser\PoCompiler();
      $headersClass = new Sepia\PoParser\Catalog\Header();

      if ( empty( $Catalog->getHeaders() ) ){
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
      foreach ( $data['res'] as $index => $r ){

        if ( empty($Catalog->getEntry($r['original_exp']) ) ){

          //prepare the new entry for the Catalog
          $entry = new  Sepia\PoParser\Catalog\Entry($r['original_exp'], $r[$lang]);
          
          // set the reference for the entry
          if ( !empty($r['path']) ){
            $entry->setReference($r['path']);
            
            foreach( $r['path'] as $idx => $path ){
              $name = '';

              $ext = pathinfo($path, PATHINFO_EXTENSION);
              
              if( $ext === 'js' ){
                $tmp = substr($r['path'][$idx], strlen(constant($parent['code'])), -3);
                
                if ( strpos($tmp, 'components') === 0 ){
                  $name = dirname($tmp);
                }
                else if ( strpos($tmp, 'mvc') === 0 ){
                  if ( !empty($idx = strpos($tmp, 'js/')) ){
                    $name = str_replace('js/', '', $tmp);
                  }
                }
                //case of plugins inside current (apst-app), temporary we decided to don't take it inside the json file of apst-app
                else if ( ( strpos($tmp, 'plugins') === 0 ) && ($parent['code'] === 'BBN_APP_PATH') ) {
                  continue;
                }

                else if ( strpos($tmp, 'bbn/') === 0 ){
                  //removing mvc from $o['code'] of appui plugins
					
                  $code = str_replace(substr($o['code'], -4), '', $o['code']);
									$tmp = str_replace($code, '', $tmp);
							    if ( strpos($tmp, 'components') === 4 ){
              			$final = str_replace(substr($tmp, 0,4),'',$tmp);
										$name = dirname($final);
                  }
                  else if ( strpos($tmp, 'mvc') === 4 ){
										$final = str_replace(substr($tmp, 0,4),'',$tmp);
										$name = str_replace('js/','',$final);
									}
                }
              
                if ( empty($js_files[$lang][$name]) ){
                  $js_files[$lang][$name] = [];
                }

                //array of all js files found in po file
                $js_files[$lang][$name][$data['res'][$index]['original_exp']] = $data['res'][$index][$lang];
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
      clearstatcache();

      if ( !empty($js_files[$lang]) ){
				$file_name = $locale_dir.'/'.$lang.'/'.$lang.'.json';
        
        \bbn\file\dir::create_path(dirname($file_name));
        // put the content of the array js_files in a json file
        $json = (boolean)file_put_contents($file_name, json_encode($js_files[$lang]));
      }
    }

    clearstatcache();



    /** @var array The data for the strings table */
    $tmp = $translation->get_translations_table($model->data['id_project'], $model->data['id_option']);

    //Set the cache of the table
    $translation->cache_set($model->data['id_option'], 'get_translations_table',
      $tmp
    );
    /** @var array The data of the table in cache */
    $strings = $translation->cache_get($model->data['id_option'], 'get_translations_table');

    //set the cache of the widget
    $translation->cache_set($model->data['id_option'], 'get_translations_widget',
      $translation->get_translations_widget($model->data['id_project'],$model->data['id_option'])
    );
    
    /** @var array The data of the widget in the cache*/
    $widget = $translation->cache_get($model->data['id_option'], 'get_translations_widget');
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
    'ex_dir'=>  $ex_dir,
    'path' => $to_explore,
    'locale' => $locale_dir,
    'languages' => $languages,
    'success' => $success,
    'strings' => empty($strings) ? [] : $strings['strings']
  ];
}
