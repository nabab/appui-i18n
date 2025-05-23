<div class="appui-i18n-translate bbn-overlay bbn-padding bbn-alt-background bbn-alt-background bbn-flex-height">
  <div class="bbn-grid"
       style="grid-template-columns: 1fr 1fr; gap: var(--sspace)">
    <div class="bbn-background bbn-text bbn-radius bbn-padding">
      <div bbn-if="source.language"
          class="bbn-grid-fields bbn-r">
        <template bbn-if="!isOptionsProject">
          <span class="bbn-no-wrap"
                bbn-text="_('Source language of this path')"/>
          <appui-i18n-lang :code="source.language"/>
        </template>
        <template bbn-if="localeDirs?.length">
          <span><?= _("Files of translations found") ?></span>
          <div class="bbn-flex-wrap"
              style="column-gap: var(--sspace); justify-content: end">
            <appui-i18n-lang bbn-for="w in localeDirs"
                            :code="w"
                            :only-flag="true"/>
          </div>
        </template>
        <template bbn-if="data && data[source.language]">
          <span bbn-if="data[source.language].num"><?= _('Total number of expressions') ?></span>
          <span bbn-text="data[source.language].num"
                class="bbn-r"/>
          <div bbn-if="data[source.language].num"
               bbn-for="(w, i) in data"
               class="bbn-grid-full bbn-vmiddle bbn-flex-width bbn-grid-sgap bbn-border bbn-radius bbn-left-xspadding">
            <template bbn-if="isOptionsProject || configuredLangs?.includes(getField(source.primaries, 'id', 'code', i))">
              <appui-i18n-lang :code="i"
                               class="bbn-b bbn-i"/>
              <span bbn-text=" w.num_translations + ' / '+ w.num"/>
              <div class="bbn-flex-fill">
                <bbn-progressbar :value="normalize(w.val)"
                                type="percent"
                                :class="['bbn-no-vborder', 'bbn-no-border-right', w.class]"
                                :radius="true"/>
              </div>
            </template>
          </div>
        </template>
        <div bbn-else-if="!localeDirs?.length"
            style="text-align: center"
            class="bbn-grid-full"
            bbn-html="_('No translation files found for this path')">
        </div>
      </div>
    </div>
    <div class="bbn-background bbn-text bbn-radius bbn-padding">
      aaaaa
    </div>
  </div>
  <div class="bbn-flex-fill bbn-background bbn-text bbn-radius bbn-padding bbn-top-space">
    bbbb
  </div>
</div>