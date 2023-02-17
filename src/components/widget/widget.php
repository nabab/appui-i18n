
<div :key="source.id"
     class="bbn-spadded">
  <!--if the source language of the path is set -->
  <div v-if="source.language">
    <div v-if="!dashboard.isOptionsProject">
      <span v-text="_('Source language of this path') + ':'"/>
      <i class="nf nf-fa-times"
         @click="remove_language"
         title="<?=_("Reset source language for this path")?>"
         style="float:right; margin-left:6px; cursor: pointer; margin-top: 4px"/>
      <appui-i18n-lang :code="source.language"
                       style="float:right"/>
    </div>
    <div class="bbn-grid-fields"
         v-if="data_widget && locale_dirs.length && !no_strings"
         style="padding-top: 8px">
      <span><?=_("Files of translations found")?>: </span>
      <div>
        <div v-for="w in locale_dirs"
             v-text="w" style="display: inline; padding-left: 6px; float: right;"
             class="bbn-r"/>
      </div>
    </div>
    <div v-if="no_strings"
         class="bbn-grid-full bbn-orange bbn-c">
      <?=_("There are no strings in this path")?>
    </div>
    <div v-if="data_widget && data_widget[source.language]"
         class="bbn-grid-fields"
         style="padding-top:8px;">
      <span v-if="data_widget[source.language].num > 0">
        <?=_('Total number of expressions')?>:
      </span>
      <span v-text="(data_widget[source.language].num > 0) ? data_widget[source.language].num : (!no_strings ? _('Regenerate the translation files') : '')"
            :class="(data_widget[source.language].num > 0) ? '' : 'bbn-grid-full bbn-orange bbn-c'"
            :style="(data_widget[source.language].num > 0) ? 'text-align:right!important' : ''"
      ></span>
      <div v-if="data_widget[source.language].num > 0"
           v-for="(w, i) in data_widget"
           class="bbn-grid-full">
        <template v-if="!!dashboard.isOptionsProject || ((i !== source.language) && configured_langs.includes(getField(primary, 'id', 'code', i)))">
          <appui-i18n-lang :code="i"
                           class="bbn-b bbn-i"/>
          <span v-text=" w.num_translations + ' / '+ w.num"
                style="padding-left:6px"/>
          <bbn-progressbar :value="normalize(w.val)"
                           type="percent"
                           :class="w.class"
                           :width="250"/>
          <div class="bbn-grid-full bbn-c"
               v-if="w.num_translations_db && (w.num !== 0) && (w.num_translations_db !== w.num_translations)">
            <i class="nf nf-fa-exclamation_triangle bbn-large bbn-red"
               :title="'<?=_("Number of translations in db")?>' + ': ' + w.num_translations_db"
            ></i><?=_("The number of translations in po file and the number of translations in db are different, please remake the po file")?>
          </div>
        </template>
      </div>
    </div>
    <div v-else-if="!locale_dirs.length"
         style="padding-top:10px"
         class="bbn-c bbn-full-grid">
      <?=_("No translation files found for this path, to start translation configure at least one language using the")?> <i class="nf nf-fa-flag bbn-large"></i> <?=_("button")?>
    </div>
  </div>
  <!--if the source language of the path is not set -->
  <div v-else-if="!dashboard.isOptionsProject">
    <div class="bbn-padded bbn-grid-fields">
      <span v-text="_('Select a source language for this option')"/>
      <div>
        <bbn-dropdown :source="dd_primary"
                      v-model="source.language"
                      @change="set_cfg"
                      placeholder="<?=_('Select a language')?>"/>
      </div>
    </div>
  </div>
</div>
