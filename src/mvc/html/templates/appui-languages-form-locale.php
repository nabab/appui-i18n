<bbn-form class="bbn-full-screen"
          :source="source.row"
          ref="form-locale"
          action="internationalization/actions/generate"
          confirm-leave="<?=_("Are you sure you want to exit without saving changes?")?>"
          :prefilled="true"
          @success="success"
>

  <div class="bbn-grid-fields">

    <div style="height:300px;" class="bbn-padded bbn-middle">
      <span>Check the box to create local folder of translation's files for the language in this path</span>
    </div>

    <div class="bbn-padded">
      <div v-for="l in source.data.configured_langs"
           class="bbn-vlpadded"
           ref="checkbox"
      >
        <bbn-checkbox :value="get_field(primary, 'id', l, 'code')"
                      :checked="checked_lang(l)"
                      @change="change_languages"
                      :disabled="get_field(primary, 'id', l, 'code') === source.data.language"
                      :title="get_field(primary, 'id', l, 'code') === source.data.language ? 'You cannot delete translation file in source language before to reset \'Source language of this path:\' from the widget' : 'Select to create or delete a translation file' "
        ></bbn-checkbox>
        <label v-text="get_field(primary, 'id', l, 'text')"></label>

      </div>

    </div>

    <div class="bbn-s bbn-grid-full bbn-padded"
         v-html="message"
         style="position:absolute; bottom:0"
    ></div>

  </div>


</bbn-form>