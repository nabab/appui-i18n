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
             :data="{source_lang: source.source_lang, lang_name:source.lang_name, translation_lang: source.translation_lang}"
             @change="insert_translation"
  >
    <bbn-column field="original_expression"
                title="<?=_('Original Expression')?>"
                :editable="false"
                cls="bbn-i"
    ></bbn-column>

    <bbn-column ftitle="<?=_('Status')?>"
                width="40"
                cls="bbn-l"
                :buttons="buttons"
    ></bbn-column>

    <bbn-column field="translation"
                title="<?=_('Translation')?>"
    ></bbn-column>

    <bbn-column field="user"
                title="<?=_('User')?>"
                :render="render_user"
                :editable="false"
    ></bbn-column>

  </bbn-table>
</div>