<bbn-form class="bbn-full-screen"
          :source="source.row"
          ref="form-locale"
          action="internationalization/actions/generate_locale_folder"
          confirm-leave="<?=_("Are you sure you want to exit without saving changes?")?>"
>
  <div class="bbn-grid-fields bbn-full-screen">

    <div style="height:300px;" class="bbn-padded bbn-middle">
      <span>Check the box to create local folder of translation's files for the language in this path</span>
    </div>

    <div class="bbn-padded">
      <div v-for="l in source.data.configured_langs"
           class="bbn-vlpadded"
           ref="checkbox"
      >
        <bbn-checkbox :value="source.data.get_field(source.data.primary, 'id', l, 'code')"
                      :checked="inArray(source.data.get_field(source.data.primary, 'id', l, 'code'), source.row
                      .languages) > -1"
                      @change="change_languages"
        ></bbn-checkbox>
        <label v-text="source.data.get_field(source.data.primary, 'id', l, 'text')"></label>

      </div>

    </div>
    <div class="bbn-s bbn-grid-full bbn-padded"
         v-html="message"
         style="position:absolute; bottom:0"
    ></div>
  </div>


</bbn-form>