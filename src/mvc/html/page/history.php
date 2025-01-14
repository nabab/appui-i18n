<div class="bbn-flex-width" v-if="source.is_dev">

  <bbn-table :source="root + 'page/data/history'"
             :info="true"
             :sortable="true"
             :pageable="true"
             :filterable="true"
             :multifilter="true"
             :limit="25"
             editable="inline"
             ref="history"
             :order="[{field: 'last_modification', dir: 'DESC'}]"
             @change="insert_translation"
  >
    <bbns-column field="last_modification"
                label="<?php echo _('Date'); ?>"
                type="date"
                flabel="<?php echo _('Date of last modification'); ?>"
                :editable="false"
                width="8%"
    ></bbns-column>

    <bbns-column field="user"
                 label="<?php echo _('User'); ?>"
                 flabel="<?php echo _('Translator\'s name'); ?>"
                 :editable="false"
                 width="12%"
                 :render="renderUser"
    ></bbns-column>

    <bbns-column field="original_exp"
                label="<?php echo _('Original Expression'); ?>"
                flabel="<?php echo _('Expression in the source language'); ?>"
                :editable="false"
                cls="bbn-i"
    ></bbns-column>

    <bbns-column field="original_lang"
                label=" "
                flabel="<?php echo _('The source language of the expression'); ?>"
                width="50"
                :editable="false"
                :render="render_original_lang"
                cls="bbn-c"
    ></bbns-column>

    <bbns-column field="expression"
                label="<?php echo _('Translation'); ?>"
                flabel="<?php echo _('The expression translated by the user'); ?>"
    ></bbns-column>


    <bbns-column field="translation_lang"
                label=" "
                flabel="<?php echo _('The language of translation'); ?>"
                width="50"
                :render="render_lang"
                cls="bbn-c"
                :editable="false"
    ></bbns-column>

    <bbns-column flabel="<?php echo _('Status'); ?>"
                :render="render_status"
                width="40"
                cls="bbn-c"
    ></bbns-column>

  </bbn-table>
</div>
