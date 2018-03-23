<div >
  <!--if the source language of the path is set -->
  <div v-if="language">



    <div class="bbn-grid-full">
      <span><?=_("Source language of this path:")?></span>

      <bbn-button icon="fa fa-remove"
                  :notext="true"
                  @click="remove_language"
                  title="<?=("Reset source language for this path")?>"
                  class="bbn-s bbn-b"
                  style="float:right; margin-left:6px"
      ></bbn-button>

      <span class="bbn-l" v-text="get_field(primary, 'code', language, 'text')" style="float:right"></span>
    </div>

    <div class="bbn-grid-fields" v-if="locale_dirs.length" style="padding-top:10px">
      <span>Locale files of translations found: </span>
      <div>
        <div v-for="w in locale_dirs"
             v-text="w" style="display: inline; padding-left: 6px; float: right;"
             class="bbn-r"
        ></div>
      </div>
    </div>

    <div v-if="data_widget.length" class="bbn-grid-fields" style="padding-top:10px">

      <!--<span v-else>There are no locale files of translation configured for this path, click on <i class="bbn-b fa fa-flag"></i> button</span>-->


      <span v-if="search(data_widget, 'lang', language) > -1" > Number of strings in the source language: </span>
      <span v-if="search(data_widget, 'lang', language) > -1"
            v-text="get_field(data_widget, 'lang', language, 'nm_translations') || 0"
            class="bbn-r"
      ></span>


      <div v-for="(w, i) in data_widget"  class="bbn-grid-full">

        <span v-text="get_field(primary, 'code', w.lang, 'text')"
              class="bbn-b bbn-i"
        ></span>
        <span v-text="' ' + w.nm_translations + ' / '+ w.num"></span>

        <bbn-progressbar :value="(w.nm_translations/w.num*100)"
                         style="padding-top:6px"
                         type="percent"
        ></bbn-progressbar>
      </div>

    </div>
    <div v-else style="padding-top:10px"><?=_("No translation files found for this path, to start translation configure at least one language using the  ")?> <i class="fa fa-flag bbn-large"></i> <?=_("button")?></div>
    <!-- i want to exist to have the possibility to have the button to check for new strings in this path-->
    <!--div v-else>
      <span>No strings found in this path, check if there are new ones by clicking <i class="fa fa-flash bbn-b"></i>
        button
      </span>
    </div-->

  </div>

  <!--if the source language of the path is not set -->
  <div v-else>
    <div class="bbn-padded bbn-grid-fields">
      <span>Select a source language for this path</span>
      <bbn-dropdown :source="dd_primary"
                    v-model="language"
                    @change="set_language"
                    placeholder="<?=_('Select a language')?>"
      ></bbn-dropdown>
    </div>
  </div>

</div>