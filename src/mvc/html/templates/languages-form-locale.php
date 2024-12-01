<bbn-form :source="source.row"
          :data="{id_project: source.data.id_project}"
          ref="form-locale"
          :action="'<?php echo APPUI_I18N_ROOT; ?>' + (source.data.id_project === 'options' ? 'options/find_options' : 'actions/generate')"
          confirm-leave="<?php echo _("Are you sure you want to exit without saving changes?"); ?>"
          :prefilled="true"
          @success="success"
          @close="update"
>

  <div class="bbn-grid-fields">

    <div style="height:300px;" class="bbn-padding bbn-middle">
      <span><?php echo _('Check the box to create local folder of translation\'s files for the language in this path'); ?></span>
    </div>

    <div class="bbn-padding">
      <div v-for="l in source.data.configured_langs"
            class="bbn-vlpadding"
            ref="checkbox"
      >
        <bbn-checkbox :value="getField(source.data.primary, 'code', {id: l})"
                      :checked="checked_lang(l)"
                      @change="change_languages"
                      :disabled="getField(source.data.primary, 'code', {id: l}) === source.data.language"
                      :title="getField(source.data.primary, 'code', {id: l}) === source.data.language ? 'You cannot delete translation file in source language before to reset \'Source language of this path:\' from the widget' : 'Select to create or delete a translation file'"
                      :label="getField(source.data.primary, 'text', {id: l})"
        ></bbn-checkbox>
        <div></div>
      </div>
    </div>

   

  </div>
  <div class="bbn-s bbn-padding"
       v-html="message"
       style="position:absolute; bottom:0;left: 0;margin-bottom: 6px;margin-right:6px;"
  ></div>


</bbn-form>