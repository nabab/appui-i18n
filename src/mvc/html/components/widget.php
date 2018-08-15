
<div ref="widget" :key="id_option">
  <!--if the source language of the path is set -->
  <div v-if="language">


    <div>
      <span v-text="(id_project === 'options') ? '<?=_("Source language of this option:")?>' : '<?=_("Source language of this path:")?>'"></span>
      <i class="fa fa-remove"
         v-if="id_project === 'options'"
         @click="remove_cfg"
         title="<?=_("Reset source language for this option")?>"
         style="float:right; margin-left:6px; cursor: pointer; margin-top: 4px"
      ></i>
      <i class="fa fa-remove"
         v-else
         @click="remove_language"
         title="<?=_("Reset source language for this path")?>"
         style="float:right; margin-left:6px; cursor: pointer; margin-top: 4px"
      ></i>

     <span class="bbn-l"
            v-text="get_field(primary, 'code', language, 'text')"
            style="float:right"
      ></span>
    </div>



    <div class="bbn-grid-fields" v-if="data_widget && locale_dirs.length && !no_strings" style="padding-top: 8px">
      <span v-if="id_project !== 'options'"><?=_("Files of translations found: ")?></span>
      <span v-else><?=_("Languages of translation allowed for this option: ")?></span>
      <div>
        <div v-for="w in locale_dirs"
             v-text="w" style="display: inline; padding-left: 6px; float: right;"
             class="bbn-r"
        ></div>
      </div>
    </div>

    <div v-if="no_strings" class="bbn-grid-full bbn-orange bbn-c"><?=_("There are no strings in this path")?></div>

    <div v-if="data_widget && data_widget[language]" class="bbn-grid-fields" style="padding-top:8px; width:100%">

      <span v-if="data_widget[language] && (data_widget[language].num > 0)">
        Total number of strings:
      </span>
      <span v-if="data_widget[language]"
            v-text="(data_widget[language].num > 0) ? data_widget[language].num : (!no_strings) ? 'There are no strings in this path' : ''"
            :class="(data_widget[language].num > 0) ? 'bbn-r' : 'bbn-grid-full bbn-orange bbn-c' "
      ></span>
      <div v-if="(data_widget[language]) && (data_widget[language].num > 0) "
           v-for="(w, i) in data_widget"
           class="bbn-grid-full">

        <span v-text="get_field(primary, 'code', i, 'text')"
              class="bbn-b bbn-i"
              v-if="i !== language"
        ></span>
        <span v-text=" w.num_translations + ' / '+ w.num"
              v-if="i !== language"
              style="padding-loeft:6px"
        ></span>


        <bbn-progressbar :value="w.val"
                         style="padding-top:6px;"
                         type="percent"
												 :class="w.class"	
                         v-if="i !== language"
        ></bbn-progressbar>

        <div class="bbn-grid-full bbn-c green-text"
             v-if="( w.num !== 0 ) && ( w.num_translations === w.num ) && ( i !== language )"
        >
          <i class="fa fa-check bbn-large"></i><?=_("Translation completed")?>
        </div>


      </div>


    </div>


    <div v-else-if="!locale_dirs.length"
         style="padding-top:10px"
         class="bbn-c bbn-full-grid">
      <?=_("No translation files found for this path, to start translation configure at least one language using the  ")?> <i class="fa fa-flag bbn-large"></i> <?=_("button")?>
    </div>





  </div>

  <!--if the source language of the path is not set -->
  <div v-else>
    <div class="bbn-padded bbn-grid-fields">
      <span v-text="(id_project === 'options') ? '<?=_("Select a source language for this option")?>' : '<?=_("Select a source language for this option")?>'"></span>
      <bbn-dropdown :source="dd_primary"
                    v-model="language"
                    v-if="id_project !== 'options' "
                    @change="set_language"
                    placeholder="<?=_('Select a language')?>"
      ></bbn-dropdown>

      <bbn-dropdown :source="dd_primary"
                    v-model="language"
                    v-else
                    @change="set_cfg"
                    placeholder="<?=_('Select a language')?>"
      ></bbn-dropdown>
    </div>
  </div>

</div>