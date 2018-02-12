<div class="bbn-flex-width" v-if="source.is_dev">

  <bbn-table source="internationalization/languages/data/complete_history_table"
             :info="true"
             :sortable="true"
             :pageable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             editable="inline"
             ref="complete_history"
             :order="[{field: 'last_modification', dir: 'DESC'}]"

  >

    <bbn-column field="user"
                title="<?=_('User')?>"
                ftitle="<?=_('Translator\'s name')?>"
    ></bbn-column>

    <bbn-column field="original_expression"
                title="<?=_('Original Expression')?>"
                ftitle="<?=_('Expression in the source language')?>"
    ></bbn-column>

    <bbn-column field="original_lang"
                title="<?=_('Source Language')?>"
                ftitle="<?=_('The source language of the expression')?>"
                width="100"
                :render="render_original_lang"
    ></bbn-column>

    <bbn-column field="expression"
                title="<?=_('Translation')?>"
                ftitle="<?=_('The expression translated by the user')?>"
                :render="render_empty_expression"
    ></bbn-column>

    <bbn-column field="lang"
                title="<?=_('Language')?>"
                ftitle="<?=_('The language of translation')?>"
                width="100"
                :render="render_lang"
    ></bbn-column>

    <bbn-column field="last_modification"
                title="<?=_('Date')?>"
                type="date"
                ftitle="<?=_('Date of last modification')?>"
    ></bbn-column>

  </bbn-table>
</div>