<bbn-form :source="source.row"
          @submit="link"
          :prefilled="true"
          @cancel="cancel"
          :scrollable="false"
>
  <div class="bbn-flex-height">
  <div class="bbn-grid-fields bbn-flex-fill bbn-padded bbn-c">
    <span>
      <?=_('Select source language')?>:
    </span>
    <div>
      <bbn-dropdown placeholder="Choose" :source="source.dd_translation_lang" v-model="source.source_lang"></bbn-dropdown>
    </div>

    <span>
      <?=_('Select a language for the translation')?>:
    </span>
    <div>
      <bbn-dropdown placeholder="Choose" :source="source.dd_translation_lang" v-model="source.translation_lang"></bbn-dropdown>
    </div>

  </div>
  <!--div-- v-show="source.source_lang && source.translation_lang" class="bbn-vspadded bbn-r">
  	<bbn-button @click="link">Open translation table</bbn-button>
    <bbn-button @click="cancel">Cancel</bbn-button>
  </!--div-->


    <!-- buttons cancel e submit deve mandare {source_language:'', e i linguaggi messi a true, ex-> ita:true}-->

  </div>

</bbn-form>