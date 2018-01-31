<div class="appui-strings-table" style="min-height: 500px">

  <bbn-table source="internationalization/languages/data/glossary_table"
             editable="inline"
             :pageable="true"
             :sortable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             :info="true"
             :order="[{field: 'expression', dir: 'ASC'}]"
             :data="{lang: source.lang, lang_name:source.lang_name}"
  >
    <bbn-column field="expression"
                title="<?=_('Expression')?>"
    ></bbn-column>

    <bbn-column field="original_lang"
                title="<?=_('Translated from:')?>"
                :render="render_original_lang"
    ></bbn-column>

    <bbn-column field="original_exp"
                title="<?=_('Original Expression')?>"
    ></bbn-column>

    <bbn-column field="user"
                title="<?=_('User')?>"
                :render="render_user"
    ></bbn-column>

  </bbn-table>
</div>