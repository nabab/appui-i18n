<div class="appui-i18n-dashboard-widget">
  <!--if the source language of the path is set -->
  <div bbn-if="source.language"
       class="bbn-grid-fields bbn-r">
    <template bbn-if="!isOptionsProject">
      <span bbn-text="_('Source language of this path')"/>
      <div class="bbn-vmiddle"
           style="column-gap: var(--sspace); justify-content: end">
        <appui-i18n-lang :code="source.language"/>
        <i class="nf nf-fa-remove bbn-p bbn-red"
           @click="removeLanguage"
           title="<?= _("Reset source language for this path") ?>"/>
      </div>
    </template>
    <template bbn-if="data && localeDirs?.length && !no_strings">
      <span><?= _("Files of translations found") ?></span>
      <div class="bbn-flex-wrap"
           style="column-gap: var(--sspace); justify-content: end">
        <appui-i18n-lang bbn-for="w in localeDirs"
                         :code="w"
                         :only-flag="true"/>
      </div>
    </template>
    <div bbn-if="no_strings"
         class="bbn-grid-full bbn-orange bbn-c">
      <?= _("There are no strings in this path") ?>
    </div>
    <template bbn-if="data && data[source.language]">
      <span bbn-if="data[source.language].num"><?= _('Total number of expressions') ?></span>
      <span bbn-text="data[source.language].num ? data[source.language].num : (!no_strings ? _('Regenerate the translation files') : '')"
            :class="data[source.language].num ? '' : 'bbn-grid-full bbn-orange bbn-c'"
            :style="data[source.language].num ? 'text-align:right!important' : ''"/>
      <div bbn-if="data[source.language].num"
           bbn-for="(w, i) in data"
           class="bbn-grid-full bbn-vmiddle bbn-flex-width bbn-grid-sgap bbn-border bbn-radius bbn-left-xspadding">
        <template bbn-if="isOptionsProject || configuredLangs?.includes(getField(primary, 'id', 'code', i))">
          <appui-i18n-lang :code="i"
                           class="bbn-b bbn-i"/>
          <span bbn-text=" w.num_translations + ' / '+ w.num"/>
          <div class="bbn-flex-fill">
            <bbn-progressbar :value="normalize(w.val)"
                             type="percent"
                             :class="['bbn-no-vborder', 'bbn-no-border-right', w.class]"
                             :radius="true"/>
          </div>
          <div class="bbn-grid-full bbn-c"
               bbn-if="w.num_translations_db && (w.num !== 0) && (w.num_translations_db !== w.num_translations)">
            <i class="nf nf-fa-exclamation_triangle bbn-large bbn-red"
               :title="'<?= _("Number of translations in db") ?>' + ': ' + w.num_translations_db"
            ></i><?= _("The number of translations in po file and the number of translations in db are different, please remake the po file") ?>
          </div>
        </template>
      </div>
    </template>
    <div bbn-else-if="!localeDirs?.length"
         style="padding-top:10px"
         class="bbn-c bbn-full-grid">
      <?= _("No translation files found for this path, to start translation configure at least one language using the")?> <i class="nf nf-fa-flag bbn-large"></i> <?=_("button") ?>
    </div>
  </div>
  <!--if the source language of the path is not set -->
  <div bbn-else-if="!isOptionsProject">
    <div class="bbn-padding bbn-grid-fields">
      <span bbn-text="_('Select a source language for this option')"/>
      <div>
        <bbn-dropdown :source="dd_primary"
                      bbn-model="source.language"
                      @change="set_cfg"
                      placeholder="<?= _('Select a language') ?>"/>
      </div>
    </div>
  </div>
</div>
