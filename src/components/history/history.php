<div class="appui-i18n-history">
  <bbn-table :source="source"
             :sortable="true"
             :pageable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             :editable="false"
             ref="user_history"
             :order="[{field: 'last_modification', dir: 'DESC'}]"
             :pagination="true">
    <bbns-column field="original_lang"
                 label="<i class='nf nf-fa-flag'/>"
                 flabel="<?=_('The source language of the expression')?>"
                 :width="50"
                 :render="renderFlag"
                 cls="bbn-c"/>
    <bbns-column field="original_exp"
                 label="<?=_('Original Expression')?>"
                 flabel="<?=_('Expression in the source language')?>"
                 cls="bbn-i"/>
    <bbns-column field="lang"
                 label="<i class='nf nf-fa-flag_checkered'/>"
                 flabel="<?=_('The language of your translation')?>"
                 :width="50"
                 :render="renderFlag"
                 cls="bbn-c"/>
    <bbns-column field="expression"
                 label="<?=_('Translation')?>"
                 flabel="<?=_('The expression you translated')?>"/>
    <bbns-column bbn-if="user"
                 field="user"
                 label="<?=_('User')?>"
                 flabel="<?=_('Translator\'s name')?>"
                 :render="renderUser"/>
    <bbns-column field="last_modification"
                 label="<?=_('Last action')?>"
                 flabel="<?=_('Date of last modification')?>"
                 type="date"
                 :width="100"
                 cls="bbn-c"/>
    <bbns-column field="operation"
                 label="<?=_('Action')?>"
                 :width="80"
                 cls="bbn-c"/>
    <bbns-column flabel="<?=_('Status')?>"
                 :render="renderStatus"
                 :width="40"/>
  </bbn-table>
</div>
