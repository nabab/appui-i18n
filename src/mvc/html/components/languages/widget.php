<div ref="widget">
  <!--if the source language of the path is set -->
  <div v-if="language">


    <div>
      <span><?=_("Source language of this path:")?></span>
      <i class="fa fa-remove"
         @click="remove_language"
         title="<?=("Reset source language for this path")?>"
         style="float:right; margin-left:6px; cursor: pointer; margin-top: 4px"
      ></i>
      <span class="bbn-l"
            v-text="get_field(primary, 'code', language, 'text')"
            style="float:right"
      ></span>
    </div>


    <!--<div class="bbn-grid-full bbn-c bbn-orange" v-else style="padding-top:10px">
      <span><?/*=_("No translation files found for this path, configure it using the button ")*/?><i class="fa fa-flag"></i> <?/*=_("of the widget")*/?></span>

    </div>-->
    <div class="bbn-grid-fields" v-if="data_widget && locale_dirs.length " style="padding-top: 8px">
      <span>Files of translations found: </span>
      <div>
        <div v-for="w in locale_dirs"
             v-text="w" style="display: inline; padding-left: 6px; float: right;"
             class="bbn-r"
        ></div>
      </div>
    </div>

    <div v-if="data_widget && data_widget[language]" class="bbn-grid-fields" style="padding-top:8px; width:100%">

      <span v-if="data_widget[language] && (data_widget[language].num > 0)">
        Total number of strings:
      </span>
      <span v-if="data_widget[language]"
            v-text="(data_widget[language].num > 0) ? data_widget[language].num : 'There are no strings in this path'"
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
        ></span>


        <bbn-progressbar :value="(w.num_translations > 0 ) ? (w.num_translations/w.num*100) : 0"
                         style="padding-top:6px"
                         type="percent"
                         v-if="i !== language"
        ></bbn-progressbar>

        <div class="bbn-grid-full bbn-c bbn-green"
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
      <span>Select a source language for this path</span>
      <bbn-dropdown :source="dd_primary"
                    v-model="language"
                    @change="set_language"
                    placeholder="<?=_('Select a language')?>"
      ></bbn-dropdown>
    </div>
  </div>

</div>