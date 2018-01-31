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
  >

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
                ftitle="<?=_('The expression you translated')?>"
                :render="render_empty_expression"
    ></bbn-column>

    <bbn-column field="lang"
                title="<?=_('Language')?>"
                ftitle="<?=_('The language of your translation')?>"
                width="100"
                :render="render_lang"
    ></bbn-column>


    <bbn-column field="last_modification"
                title="<?=_('Date')?>"
                ftitle="<?=_('Date of last modification')?>"
                type="date"
    ></bbn-column>

    <bbn-column field="operation"

    ></bbn-column>

  </bbn-table>
</div>