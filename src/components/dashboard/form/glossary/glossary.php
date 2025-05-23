<bbn-form :source="formSource"
          @submit="link"
          @cancel="cancel"
          :scrollable="false">
  <div class="bbn-grid-fields bbn-flex-fill bbn-padding bbn-c">
    <span><?=_('Select source language:')?></span>
    <div>
      <bbn-dropdown placeholder="<?=_('Choose')?>"
                    :source="source"
                    bbn-model="formSource.sourceLang"
                    source-value="code"/>
    </div>
    <span><?=_('Select a language for the translation:')?></span>
    <div>
      <bbn-dropdown placeholder="<?=_('Choose')?>"
                    :source="source"
                    bbn-model="formSource.translationLang"
                    source-value="code"/>
    </div>
  </div>
</bbn-form>