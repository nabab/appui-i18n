<div class="bbn-full-screen bbn-flex-height" 
     >
  <div class="bbn-grid-fields bbn-flex-fill bbn-padded bbn-c">
    <span>Select source language:</span>
    <bbn-dropdown placeholder="Choose" :source="source.dd_source_lang" v-model="source.source_lang"></bbn-dropdown>

    <span>Select a language for the translation:</span>
    <bbn-dropdown placeholder="Choose" :source="source.dd_translation_lang" v-model="source.translation_lang"></bbn-dropdown>

  </div>
  <div v-show="source.source_lang && source.translation_lang" class="bbn-vspadded bbn-r">
  	<bbn-button @click="link">Open translation table</bbn-button>
    <bbn-button @click="cancel">Cancel</bbn-button>
  </div>
	
    
    <!-- buttons cancel e submit deve mandare {source_language:'', e i linguaggi messi a true, ex-> ita:true}-->

</div>