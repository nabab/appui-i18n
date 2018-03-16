<div :source="source"


>
  <div v-if="source.total > 0" class="bbn-grid-fields">

    <span>Number of strings found in this path:</span>
    <span v-text="source.total"></span>

    <span v-if="source.languages.length">Locale files of translations: </span>
    <span v-else>There are no locale files of translation configured for this path, click on <i class="bbn-b fa fa-flag"></i> button</span>
    <div v-if="source.languages.length">
      <div v-for="s in source.languages" v-text="s" style="display: inline;padding-right: 6px;"></div>
    </div>

    <span v-if="source.new > 0">New strings found in this path's files:</span>
    <span v-if="source.new" v-text="source.new"></span>

    <div v-if="languages" class="bbn-grid-full">
      <div v-for="(t, i) in translated">
        <span v-text="get_field(primary, 'code', i, 'text')"
              class="bbn-b bbn-i"
        ></span>
        <span v-text="' ' + t + ' / '+ source.total"></span>

        <bbn-progressbar :value="(t/source.total*100)"
                         style="padding-top:6px"
                         type="percent"
        ></bbn-progressbar>



      </div>
    </div>

  </div>
  <div v-else>
    <!-- i want to exist to have the possibility to have the button to check for new strings in this path-->
    <span>No strings found in this path, check if there are new ones by clicking <i class="fa fa-flash bbn-b"></i>
      button</span>
  </div>



</div>