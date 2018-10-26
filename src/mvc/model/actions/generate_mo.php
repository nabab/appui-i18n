<?php

// Generates the mo files called from the method generate of the table

use Gettext\Translations;
/** @var string The id_option of the path */
$id_option = $model->data['id_option'];
/** @var array The full option */
$o = $model->inc->options->option($id_option);
$success = false;

if ( $parent =  $model->inc->options->option($o['id_parent']) ){

  /** @var string $to_explore The directory to explore for strings */
  $to_explore = constant($parent['code']);

  /** @var string $locale_dir Directory containing the locale files */
  if( $parent['code'] !== 'BBN_LIB_PATH'){
    $locale_dir = $to_explore.'locale';
  }
  else{
    $locale_dir = mb_substr(constant($parent['code']).$o['code'], 0, -4).'locale';
  }
  
  if ( !empty($locale_dir) ){
    /** @var array The dirs contained in locale_dir */
    $tmp = scandir($locale_dir);
    foreach( $tmp as $i => $t){
      //remove dirs beginning with ., .., and the file index.txt to explore languages dirs 
      if ( (strpos('.', $t ) !== 0 ) && ( strpos('index', $t ) !== 0 ) && ( strpos('..', $t ) !== 0 ) ){
        
        /** @var string The directory containing translations file */
        $files = \bbn\file\dir::get_files($locale_dir . '/' . $tmp[$i] . '/LC_MESSAGES');
       
        if ( is_array($files) ){
          foreach ( $files as $idx => $f ){
            if ( file_exists($files[$idx]) ){
              /** @var string The extension of files found in the dir $files */
              $ext = \bbn\str::file_ext($files[$idx]);
              if ( $ext === 'po' ){
                $po = $files[$idx];
              }
            
              if ( $ext === 'mo' ){
                $mo = $files[$idx];
               
              }

              
            }
          }
        }
        
        // if a po file exists it take the name of the file to generate the new mo file
        if ( $po ){
          $new_mo = str_replace('.po', '.mo', $po);
          /** @var array The content of po file */
          if ( $translations = Translations::fromPoFile($po) ){
            // put the content of po file in mo file
            Gettext\Generators\Mo::toFile($translations, $new_mo);
          };
          $success = true;
          
          
          
        }  
        
      }
    }
    
  }
  return [
    'success' => $success
  ];
}
