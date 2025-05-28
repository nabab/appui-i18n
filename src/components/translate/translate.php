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
                           style="justify-content: end"/>
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
          <span><?= _('Total number of expressions') ?></span>
          <span bbn-text="data[source.language].num"
                class="bbn-r"/>
          <div bbn-if="normalize(w.val) !== 100"
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
                    icon="nf nf-fa-stop"
                    :disabled="isGenerating">
          <?=_('Stop translation')?>
        </bbn-button>
        <div bbn-if="toTranslate.length"
             bbn-text="_('Expression %d of %d', currentIndex + 1, toTranslate.length)"
             class="bbn-top-space"/>
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
  <div bbn-if="isTranslating"
       class="bbn-flex-fill bbn-background bbn-text bbn-radius bbn-padding bbn-top-space shadow">
    <div bbn-if="isLoading || isGenerating"
          class="bbn-overlay bbn-modal">
      <bbn-loader font-size="l"
                  :label="isGenerating ? _('Generating files...') : ''"/>
    </div>
    <div bbn-elseif="toTranslate.length && currentTranslation"
          class="bbn-flex-height">
      <div class="bbn-lg bbn-header bbn-no-border bbn-spadding bbn-radius bbn-c bbn-bottom-space"
            bbn-text="currentTranslation.expression"/>
      <div class="bbn-flex-fill">
        <bbn-form :scrollable="true"
                  :buttons="formButtons"
                  :source="currentTranslation || {}"
                  mode="big"
                  ref="form">
          <div bbn-for="(lang, idx) in selectedLang"
                :class="['bbn-alt-background', 'bbn-spadding', 'bbn-radius', 'bbn-lg', 'bbn-flex-width', {
                  'bbn-bottom-space': selectedLang[idx+1]
                }]">
            <appui-i18n-lang :code="lang"
                              :style="{
                                zoom: '1.2',
                                writingMode: 'vertical-lr',
                                alignItems: 'start',
                                gap: 'var(--sspace)',
                                justifyContent: isSuggestionsActive ? 'start' : 'center',
                              }"
                              :only-flag="!isSuggestionsActive"/>
            <div class="bbn-flex-fill">
              <bbn-textarea bbn-model="currentTranslation[lang].translation"
                            :resizable="false"
                            :class="['bbn-w-100', {'bbn-bottom-sspace': isSuggestionsActive}]"/>
              <div bbn-if="isSuggestionsActive && currentTranslation[lang]?.suggestions?.length"
                    class="bbn-flex-column"
                    style="gap: var(--sspace)">
                <div bbn-for="(sugg, i) in currentTranslation[lang].suggestions"
                      class="bbn-flex">
                  <span :class="['bbn-spadding', 'bbn-radius', 'bbn-reactive', {
                          'bbn-secondary-text': sugg !== currentTranslation[lang].translation,
                          'bbn-state-selected': sugg === currentTranslation[lang].translation
                        }]"
                        :style="{
                          'background-color': sugg !== currentTranslation[lang].translation ? bgColors[i] : ''
                        }"
                        bbn-text="sugg"
                        @click="setSuggest(lang, sugg)"/>
                </div>
              </div>
              <div bbn-elseif="isSuggestionsActive && isLoadingSuggestions"
                    class="bbn-vmiddle">
                <bbn-loadicon :size="20"/>
                <span class="bbn-left-sspace"><?=_('Loading suggestions...')?></span>
              </div>
            </div>
          </div>
        </bbn-form>
      </div>
    </div>
  </div>
</div>