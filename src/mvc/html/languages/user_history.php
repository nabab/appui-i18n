<div class="bbn-flex-width">

  <bbn-table source="internationalization/languages/data/user_history_table"
             :sortable="true"
             :pageable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             editable="inline"
             ref="user_history"
             :order="[{field: 'last_modification', dir: 'DESC'}]"
             :pagination="true"
             @change="insert_translation"
  >

    <bbn-column field="original_expression"
                title="<?=_('Original Expression')?>"
                ftitle="<?=_('Expression in the source language')?>"
                cls="bbn-i"
                :editable="false"
    ></bbn-column>

    <bbn-column field="original_lang"
                title="<?=_('Source Language')?>"
                ftitle="<?=_('The source language of the expression')?>"
                width="100"
                :render="render_original_lang"
                cls="bbn-c"
                :editable="false"
    ></bbn-column>

    <bbn-column field="expression"
                title="<?=_('Translation')?>"
                ftitle="<?=_('The expression you translated')?>"
    ></bbn-column>

    <bbn-column field="lang"
                title="<?=_('Language')?>"
                ftitle="<?=_('The language of your translation')?>"
                width="100"
                :render="render_lang"
                cls="bbn-c"
                :editable="false"
    ></bbn-column>

    <bbn-column ftitle="<?=_('Status')?>"
                :render="render_status"
                width="40"

    ></bbn-column>


    <bbn-column field="last_modification"
                title="<?=_('Date')?>"
                ftitle="<?=_('Date of last modification')?>"
                type="date"
                :editable="false"
    ></bbn-column>

    <bbn-column field="operation"
                :editable="false"
                width="80"
                cls="bbn-c"
    ></bbn-column>

  </bbn-table>
</div>