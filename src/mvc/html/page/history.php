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
                title="<?php echo _('Date'); ?>"
                type="date"
                ftitle="<?php echo _('Date of last modification'); ?>"
                :editable="false"
                width="8%"
    ></bbns-column>

    <bbns-column field="user"
                 title="<?php echo _('User'); ?>"
                 ftitle="<?php echo _('Translator\'s name'); ?>"
                 :editable="false"
                 width="12%"
                 :render="renderUser"
    ></bbns-column>

    <bbns-column field="original_exp"
                title="<?php echo _('Original Expression'); ?>"
                ftitle="<?php echo _('Expression in the source language'); ?>"
                :editable="false"
                cls="bbn-i"
    ></bbns-column>

    <bbns-column field="original_lang"
                title=" "
                ftitle="<?php echo _('The source language of the expression'); ?>"
                width="50"
                :editable="false"
                :render="render_original_lang"
                cls="bbn-c"
    ></bbns-column>

    <bbns-column field="expression"
                title="<?php echo _('Translation'); ?>"
                ftitle="<?php echo _('The expression translated by the user'); ?>"
    ></bbns-column>


    <bbns-column field="translation_lang"
                title=" "
                ftitle="<?php echo _('The language of translation'); ?>"
                width="50"
                :render="render_lang"
                cls="bbn-c"
                :editable="false"
    ></bbns-column>

    <bbns-column ftitle="<?php echo _('Status'); ?>"
                :render="render_status"
                width="40"
                cls="bbn-c"
    ></bbns-column>

  </bbn-table>
</div>
