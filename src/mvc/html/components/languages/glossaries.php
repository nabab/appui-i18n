<div class="appui-strings-table" style="min-height: 500px">

  <bbn-table source="internationalization/languages_tabs/data/glossary"
             editable="inline"
             :pageable="true"
             :sortable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             ref="glossary_table"
             :info="true"
             :order="[{field: 'exp', dir: 'ASC'}]"
             :data="{source_lang: source.source_lang, lang_name:source.lang_name, translation_lang: source.translation_lang}"
             @change="insert_translation"
  >
    <bbn-column field="original_exp"
                title="<?=_('Original Expression')?>"
                :editable="false"
                cls="bbn-i"
    ></bbn-column>

    <bbn-column field="translation"
                title="<?=_('Translation')?>"
    ></bbn-column>

    <bbn-column field="id_user"
                :render="render_user"
                title="<?=_('User')?>"
                :editable="false"
    ></bbn-column>

    <bbn-column ftitle="<?=_('Status')?>"
                width="40"
                cls="bbn-c"
                :render="icons"
    ></bbn-column>

    <bbn-column ftitle="<?=_('Actions')?>"
                width="40"
                cls="bbn-b"
                :buttons="buttons"
    ></bbn-column>



  </bbn-table>
</div>