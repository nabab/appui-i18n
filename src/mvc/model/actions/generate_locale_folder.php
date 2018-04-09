<?php
/*
 * Describe what it does!
 *
 **/
use Gettext\Translations;

/** @var $this \bbn\mvc\model*/

//action of locale_folder_form
$success = false;
if (
  ($id_option = $model->data['id_option']) &&
  ($o = $model->inc->options->option($id_option)) &&
  !empty($o['language']) &&
  ($parent = $model->inc->options->parent($id_option)) &&
  defined($parent['code']) &&
  isset($o['language'])
){
  
	$domain = $o['text'];	
  $to_explore = constant($parent['code']) . $o['code'];
  $locale_dir = dirname($to_explore) . '/locale';
  //takes the model of find_strings
   if ( $data = $model->get_model(APPUI_I18N_ROOT.'actions/find_strings', ['id_option'=> $model->data['id_option'], 'language'=> $o['language']]) ){
    
    //$languages the languages from the post
    $languages = $model->data['languages'];
    // $old_langs the languages existing before the post 
    $old_langs = $data['languages'];
    $ex_dir = [];
    $new_dir = []; 
		if ( !empty($ex_dir = array_diff($old_langs, $languages)) ){
      //deletes unchecked languages dirs
      foreach ( $ex_dir as $ex ){
        $dir = $locale_dir . '/' . $ex;
        $success = \bbn\file\dir::delete($dir);
      }
    }
    else if ( !empty( $new_dir = array_diff($languages, $old_langs )) ){
      clearstatcache();
      //creates or re-creates the file po for all languages sent by the form
      $dir = '';
      foreach ( $languages as $lang){
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
          //$translations[$lang][] = $t;
        }
        $success = Gettext\Generators\Po::toFile($translations[$lang], $po);
        clearstatcache();
        Gettext\Generators\Mo::toFile($translations[$lang], $mo);
      }
    } 
		$translations = [];
	} 
  clearstatcache();
  //deletes cached model of the widget and strings_table
  $model->get_cached_model(APPUI_I18N_ROOT.'page/data/widgets', ['id_option'=> $model->data['id_option']], true);
  
  
  $model->get_cached_model(APPUI_I18N_ROOT.'page/data/strings_table', ['id_option' => $ctrl->data['id_option']], true);
  return [
    'path' => $to_explore,
    'new_dir' => $new_dir,
    'ex_dir' => $ex_dir,
    'success' => $success
  ];
}