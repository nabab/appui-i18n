<div class="appui-i18n-glossaries bbn-overlay">
  <bbn-table :source="root + 'page/data/glossary'"
             editable="nobuttons"
             :pageable="true"
             :sortable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             ref="table"
             :info="true"
             :order="[{field: 'exp', dir: 'ASC'}]"
             :data="{
               source_lang: source.source_lang,
               lang_name: source.lang_name,
               translation_lang: source.translation_lang
             }"
             @change="insertTranslation">
    <bbns-column field="exp"
                label="<?=_('Original Expression')?>"
                :editable="false"
                cls="bbn-i"/>
    <bbns-column field="translation"
                 label="<?=_('Translation')?>"/>
    <!--<bbns-column flabel="<?/*=_('Actions')*/?>"
                :width="40"
                cls="bbn-b"
                :buttons="buttons"/>-->
  </bbn-table>
</div>
