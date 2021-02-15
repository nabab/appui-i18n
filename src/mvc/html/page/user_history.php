<div class="bbn-flex-width">

  <bbn-table source="internationalization/page/data/user_history"
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
  <bbns-column field="original_lang"
              title=" "
              ftitle="<?=_('The source language of the expression')?>"
              width="50"
              :render="render_original_lang"
              cls="bbn-c"
              :editable="false"
  ></bbns-column>
    <bbns-column field="original_exp"
                title="<?=_('Original Expression')?>"
                ftitle="<?=_('Expression in the source language')?>"
                cls="bbn-i"
                :editable="false"
    ></bbns-column>


    <bbns-column field="lang"
                title=" "
                ftitle="<?=_('The language of your translation')?>"
                width="50"
                :render="render_lang"
                cls="bbn-c"
                :editable="false"
    ></bbns-column>


    <bbns-column field="expression"
                title="<?=_('Translation')?>"
                ftitle="<?=_('The expression you translated')?>"
                :editable="true"
    ></bbns-column>

    <bbns-column field="last_modification"
                title=" "
                ftitle="<?=_('Date of last modification')?>"
                type="date"
                width="100"
                :editable="false"
    ></bbns-column>

    <bbns-column field="operation"
                title=" "
                :editable="false"
                width="80"
                cls="bbn-c"
    ></bbns-column>

    <bbns-column ftitle="<?=_('Status')?>"
                :render="render_status"
                width="40"

    ></bbns-column>

  </bbn-table>
</div>
