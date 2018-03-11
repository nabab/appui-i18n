<bbn-form class="bbn-full-screen"
								:source="source"
                ref="form-locale"
                action="internationalization/actions/generate_locale_folder"
                confirm-leave="<?=_("Are you sure you want to exit without saving changes?")?>"

>
  <div class="bbn-grid-fields bbn-full-screen">

    <div style="height:300px;" class="bbn-padded bbn-middle">
    	<span>Check the box to create local folder of translation's files for the language in this path</span>
    </div>
    
    <div class="bbn-padded">
    	<div v-for="(l, index) in source.configured_langs"
           class="bbn-vlpadded"
           ref="checkbox"
      >
      	<bbn-checkbox :value="l.code"
                      :checked="inArray(l.code, source.languages) > -1"
                      @change="change_languages"
        ></bbn-checkbox>
        <label v-text="l.text"></label>

			</div>

    </div>
    <div class="bbn-s bbn-grid-full bbn-padded"
         v-html="message"
         style="position:absolute; bottom:0"
    ></div>
  </div>
  

</bbn-form>