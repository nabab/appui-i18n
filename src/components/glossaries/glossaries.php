<div class="strings-table" style="min-height: 500px">

  <bbn-table source="internationalization/page/data/glossary"
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
    <bbns-column field="original_exp"
                title="<?=_('Original Expression')?>"
                :editable="false"
                cls="bbn-i"
    ></bbns-column>

    <bbns-column field="translation"
                title="<?=_('Translation')?>"
    ></bbns-column>

<!--    <bbns-column field="id_user"
                :render="render_user"
                title="<?/*=_('User')*/?>"
                :editable="false"
    ></bbns-column>-->

    <bbns-column ftitle="<?=_('Status')?>"
                 width="40"
                 cls="bbn-c"
                 :editable="false"
                 :render="icons"
    ></bbns-column>
<!--
    <bbns-column ftitle="<?/*=_('Actions')*/?>"
                width="40"
                cls="bbn-b"
                :buttons="buttons"
    ></bbns-column>-->



  </bbn-table>
</div>