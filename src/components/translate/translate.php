<div class="appui-i18n-translate bbn-overlay bbn-padding bbn-alt-background bbn-alt-background bbn-flex-height">
  <div class="bbn-grid"
       style="grid-template-columns: 1fr 1fr; gap: var(--sspace)">
    <div class="bbn-background bbn-text bbn-radius bbn-padding shadow">
      <div bbn-if="source.language"
          class="bbn-grid-fields bbn-r">
        <span class="bbn-no-wrap"
              bbn-text="_('Path')"/>
        <span bbn-text="source.title"
              class="bbn-r"/>
        <template bbn-if="!isOptionsProject">
          <span class="bbn-no-wrap"
                bbn-text="_('Source language')"/>
          <appui-i18n-lang :code="source.language"
                           class="bbn-r"/>
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
          <div bbn-if="data[source.language].num && (normalize(w.val) !== 100)"
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
    <div class="bbn-background bbn-text bbn-radius bbn-padding bbn-middle shadow">
      <div bbn-if="isTranslating">
        <div class="bbn-secondary-text-alt bbn-upper bbn-c bbn-b bbn-bottom-space bbn-m">
          <?=_("You are currently translating into")?>
        </div>
        <div class="bbn-flex-wrap bbn-middle"
             style="gap: var(--space)">
          <appui-i18n-lang bbn-for="l in selectedLang"
                           :code="l"
                           class="bbn-m bbn-spadding bbn-radius bbn-alt-background bbn-alt-text"/>
        </div>
        <bbn-button class="bbn-bg-red bbn-white bbn-top-space bbn-upper"
                    @click="stopTranslation"
                    icon="nf nf-fa-stop">
          <?=_('Stop translation')?>
        </bbn-button>
      </div>
      <div bbn-else>
        <div class="bbn-secondary-text-alt bbn-upper bbn-c bbn-b bbn-bottom-space bbn-m">
          <?=_("Select the language for which you want to translate")?>
        </div>
        <div class="bbn-flex-wrap bbn-middle"
             style="gap: var(--space)">
          <appui-i18n-lang bbn-for="(w, i) in data"
                           bbn-if="i !== source.language"
                           :code="i"
                           :class="['bbn-p', 'bbn-m', 'bbn-spadding', 'bbn-radius', 'bbn-alt-background', 'bbn-alt-text', {
                             'bbn-state-selected': selectedLang.includes(i)
                           }]"
                           @click="toggleLang(i)"/>
        </div>
        <bbn-button class="bbn-bg-green bbn-white bbn-top-space bbn-upper"
                    @click="startTranslation"
                    icon="nf nf-fa-play"
                    :disabled="!selectedLang.length">
          <?=_('Start translation')?>
        </bbn-button>
      </div>
    </div>
  </div>
  <div class="bbn-flex-fill bbn-background bbn-text bbn-radius bbn-padding bbn-top-space shadow">
    <template bbn-if="isTranslating">
      <div bbn-if="isLoading"
           class="bbn-overlay bbn-modal">
        <bbn-loader font-size="l"/>
      </div>
      <div bbn-elseif="toTranslate.length && currentTranslation">
        <div bbn-text="currentTranslation.expression"/>
        <div></div>
        <div>
          <bbn-button icon="nf nf-md-page_previous"
                      @click="prevTranslation">
            <?=_('Previous')?>
          </bbn-button>
          <bbn-button icon="nf nf-oct-skip_fill"
                      @click="nextTranslation"
                      icon-position="right">
            <?=_('Skip')?>
          </bbn-button>
          <bbn-button icon="nf nf-md-page_next"
                      @click="saveTranslation"
                      icon-position="right">
            <?=_('Save')?>
          </bbn-button>
        </div>
      </div>
    </template>
  </div>
</div>