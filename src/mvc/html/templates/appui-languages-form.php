<bbn-form class="bbn-full-screen"
          :source="source.row"
          ref="form"
          action="internationalization/actions/languages_form"
          confirm-leave="<?=_("Are you sure you want to exit without saving changes?")?>"
>
  <div class="bbn-grid-fields bbn-full-screen">

    <!--deve mandare il codice del linguaggio scelto come source {source:en}
    AGGIUNGERE V-MODEL -->
    <!--n>Select the source language</span>
    <bbn-dropdown :source="source.data.primary"
		></bbn-dropdown-->
		<div style="height:300px;" class="bbn-padded bbn-middle">
    	<span>Check the box to activate translation in the language for this root</span>  
    </div>
    
    <div class="bbn-padded">
    	<div v-for="(l, index) in source.data.primary"
           class="bbn-vlpadded"
           ref="checkbox"
      >
      	<bbn-checkbox :id="l.id"
                      value="1"
                      :checked="inArray(l.id, source.row.langs) > -1"
                      @change="change_checked_langs"
        ></bbn-checkbox>
        <label v-text="l.text"></label>
			</div>
    </div>  
  </div>

    
    <!-- buttons cancel e submit deve mandare {source_language:'', e i linguaggi messi a true, ex-> ita:true}-->

</bbn-form>