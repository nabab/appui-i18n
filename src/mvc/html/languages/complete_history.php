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
             :toolbar="$options.components['toolbar']"
             @change="insert_translation"
  >
    <bbn-column field="last_modification"
                title="<?=_('Date')?>"
                type="date"
                ftitle="<?=_('Date of last modification')?>"
                :editable="false"
                width="8%"
    ></bbn-column>

    <bbn-column field="user"
                title="<?=_('User')?>"
                ftitle="<?=_('Translator\'s name')?>"
                :editable="false"
                width="12%"
    ></bbn-column>

    <bbn-column field="original_expression"
                title="<?=_('Original Expression')?>"
                ftitle="<?=_('Expression in the source language')?>"
                :editable="false"
                cls="bbn-i"
    ></bbn-column>

    <bbn-column field="original_lang"
                title="<?=_('Source Language')?>"
                ftitle="<?=_('The source language of the expression')?>"
                width="100"
                :editable="false"
                :render="render_original_lang"
                cls="bbn-c"
    ></bbn-column>

    <bbn-column field="expression"
                title="<?=_('Translation')?>"
                ftitle="<?=_('The expression translated by the user')?>"
    ></bbn-column>

    <bbn-column ftitle="<?=_('Status')?>"
                :render="render_status"
                width="40"
                cls="bbn-c"
    ></bbn-column>

    <bbn-column field="translation_lang"
                title="<?=_('Language')?>"
                ftitle="<?=_('The language of translation')?>"
                width="100"
                :render="render_lang"
                cls="bbn-c"
                :editable="false"
    ></bbn-column>

  </bbn-table>
</div>