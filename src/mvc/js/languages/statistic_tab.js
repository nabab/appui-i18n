(() => {
  return {
    props:['source'],
    data(){
      return{
        //v-model for the first dropdown to select the specific language for statistic
        search_for_lang : 'fr',
        //v-model of the second dropdown to select the source language for statistic
        source_lang: false,
        lang_statistic: [],

        //an intermediate property used for the render of the statistic
        dd_ready: false,
        //v-model of progress-bar
        translatedPercentage: false,
        progressbar_color: ''

      }
    },
    computed: {
      //Icon for statistic table
      trophy_icon(){
        return  '<i class="fa fa-trophy" style="color:rgba(255, 215, 0, 1.0)"></i>'
      },
      //source of the second dropdown in statistic's tab
      dropdown_source(){
        // don't want the language source in the source of this dropdown
        if( this.source.dropdown_langs && this.source_lang ){
          var res = [];
          $.each(this.source.dropdown_langs, (i, v) => {
            if ( v['code'] !== this.source_lang ){
              res.push({text: v['text'], value: v['code']})
            }
          })
          return res;
        }
      },
      //the source for the first dropdown in statistic tab
      source_langs(){
        if( this.source.source_dd_langs ){
          var res = [];
          $.each(this.source.source_dd_langs, (i, v) => {
            res.push({text: v['text'], value: v['code']})
          })
          return res;
        }
      },
      computed_result(){
        return this.lang_statistic.translated_nr + ' of '+ this.lang_statistic.source_total_strings + ' total';
      },

    },
    methods: {

      //detect the type of data in statistics array
      checktype(obj) {
        return typeof (obj)
      },
     /* lang_name(val){
        return bbn.fn.get_field(this.source.dropdown_langs, 'value', val, 'text'))
      }*/
    },
    watch: {

      //v-model of the first dropdown
      source_lang(val){
        if ( val ){
          bbn.fn.post('internationalization/languages/lang', {
            'source_lang': val,
            'lang' : this.search_for_lang
          }, (d) => {
            if(d.success){
              this.lang_statistic = d.lang_statistic;
            }
          })
        }
      },

      //v-model of the second dropdown
      search_for_lang(val){
        if ( val ){
          //an intermediate property used for the render of the statistic
          this.dd_ready = true;
          bbn.fn.post('internationalization/languages/lang', {
            'lang': val,
            'source_lang': this.source_lang
          }, (d) => {
            if (d.success){
              this.lang_statistic = d.lang_statistic;
            }
          });
        }
      },
      //to give value to this.translatedPercentage
      lang_statistic: {
        deep: true,
        handler(val){
          if ( val ){
            return this.translatedPercentage = val.translatedPercentage
          }
        }
      },
      //change the css class for the progressbar basing on its value
      translatedPercentage(val){
        if ( val ){
          bbn.fn.log('vvvvvvvvv', val, parseFloat(val));
          if ( 0 < parseFloat(val) < 30 ){
            this.progressbar_color = 'low'
            bbn.fn.log(this.progressbar_color )

          }
          else if ( 30 <= parseFloat(val) < 60  ){
            this.progressbar_color = 'medium'
            bbn.fn.log(this.progressbar_color )
          }
          else if ( 60 <= parseFloat(val) < 90  ){
            this.progressbar_color = 'medium-high'
            bbn.fn.log(this.progressbar_color )
          }
          else if ( 90 <= parseFloat(val) <= 100  ){
            this.progressbar_color = 'high'
            bbn.fn.log(this.progressbar_color )
          }
        }
      }





    },
  }
})();