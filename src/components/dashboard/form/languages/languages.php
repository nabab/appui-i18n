<div class="bbn-w-100">
  <bbn-form :scrollable="false"
            :source="source"
            ref="form"
            :action="root + 'actions/languages_form'"
            confirm-leave="<?=_('Are you sure you want to exit without saving changes?')?>"
            :prefilled="true"
            @success="success">
    <div bbn-if="primariesLanguages?.length"
          class="bbn-padding bbn-grid bbn-grid-gap"
          style="grid-template-columns: repeat(3, 1fr)">
      <div bbn-for="l in primariesLanguages"
          class="bbn-spadding bbn-radius bbn-alt-background">
        <bbn-checkbox :id="l.id"
                      :checked="source.langs.includes(l.id)"
                      @change="toggleLang"
                      :label="l.text"
                      component="appui-i18n-lang"
                      :component-options="{code: l.code}"
                      :disabled="l.code === currentLanguage"/>
      </div>
    </div>
    <h2 bbn-else><?=_('No primary languages found')?></h2>
  </bbn-form>
</div>