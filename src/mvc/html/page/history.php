<div class="bbn-flex-width" v-if="source.is_dev">

  <bbn-table source="internationalization/page/data/history"
             :info="true"
             :sortable="true"
             :pageable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             editable="inline"
             ref="history"
             :order="[{field: 'last_modification', dir: 'DESC'}]"
             @change="insert_translation"
  >
    <bbns-column field="last_modification"
                title="<?=_('Date')?>"
                type="date"
                ftitle="<?=_('Date of last modification')?>"
                :editable="false"
                width="8%"
    ></bbns-column>

    <bbns-column field="user"
                title="<?=_('User')?>"
                ftitle="<?=_('Translator\'s name')?>"
                :editable="false"
                width="12%"
    ></bbns-column>

    <bbns-column field="original_exp"
                title="<?=_('Original Expression')?>"
                ftitle="<?=_('Expression in the source language')?>"
                :editable="false"
                cls="bbn-i"
    ></bbns-column>

    <bbns-column field="original_lang"
                title="<?=_('Source Language')?>"
                ftitle="<?=_('The source language of the expression')?>"
                width="50"
                :editable="false"
                :render="render_original_lang"
                cls="bbn-c"
    ></bbns-column>

    <bbns-column field="expression"
                title="<?=_('Translation')?>"
                ftitle="<?=_('The expression translated by the user')?>"
    ></bbns-column>


    <bbns-column field="translation_lang"
                title="<?=_('Language')?>"
                ftitle="<?=_('The language of translation')?>"
                width="50"
                :render="render_lang"
                cls="bbn-c"
                :editable="false"
    ></bbns-column>

    <bbns-column ftitle="<?=_('Status')?>"
                :render="render_status"
                width="40"
                cls="bbn-c"
    ></bbns-column>

  </bbn-table>
</div>