<bbn-form :source="source.row"
          :data="{id_project: source.data.id_project}"
          ref="form-locale"
          :action="( source.data.id_project === 'options' ) ? 'internationalization/options/find_options' : 'internationalization/actions/generate'"
          confirm-leave="<?=_("Are you sure you want to exit without saving changes?")?>"
          :prefilled="true"
          @success="success"
          @close="update"
>

  <div class="bbn-grid-fields">

    <div style="height:300px;" class="bbn-padded bbn-middle">
      <span><?=_('Check the box to create local folder of translation\'s files for the language in this path')?></span>
    </div>

    <div class="bbn-padded">
      <div v-for="l in source.data.configured_langs"
            class="bbn-vlpadded"
            ref="checkbox"
      >
        <bbn-checkbox :value="get_field(source.data.primary, 'id', l, 'code')"
                      :checked="checked_lang(l)"
                      @change="change_languages"
                      :disabled="get_field(source.data.primary, 'id', l, 'code') === source.data.language"
                      :title="get_field(source.data.primary, 'id', l, 'code') === source.data.language ? 'You cannot delete translation file in source language before to reset \'Source language of this path:\' from the widget' : 'Select to create or delete a translation file'"
                      :label="get_field(source.data.primary, 'id', l, 'text')"
        ></bbn-checkbox>
        <div></div>
      </div>
    </div>

   

  </div>
  <div class="bbn-s bbn-padded"
       v-html="message"
       style="position:absolute; bottom:0;left: 0;margin-bottom: 6px;margin-right:6px;"
  ></div>


</bbn-form>