<div class="appui-strings-table" style="min-height: 500px">

  <bbn-table :source="source_glossary"
             :columns="configured_langs"
             editable="inline"
             :pageable="true"
             :sortable="true"
             :limit="25"
             :info="true"
             :filterable="true"
             :multifilter="true"
             ref="strings_table"
             :order="[{field: 'expression', dir: 'ASC'}]"
             :data="{ id_option: source.id_option, langs: source.langs }"
  >
    <bbn-column field="id_exp"
                width="40%"
                class="bbn-xl"
                ftitle="<?/*=_('The strings in their original language')*/?>"
                :fixed="true"
                :hidden="true"
    ></bbn-column>
    <bbn-column field="exp"
                :title="first_column_title"
                width="40%"
                class="bbn-xl"
                ftitle="<?/*=_('The strings in their original language')*/?>"
                :fixed="true"
    ></bbn-column>
  </bbn-table>
</div>