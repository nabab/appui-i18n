//the first 3 buttons are like radio, v-model on the same property and assign a value from 1 to 3
(() => {
  return {
    data(){
      return {
        //the source language of the root, i'll receive it from the model
        source_lang: false,
        home_switch: 1,
        base_url: bbn.vue.closest(this, 'bbn-tabnav').baseURL,
        search: 0,

      }
    },
    created(){
      this.run_script = this.$options.components['appui-roots-table'].methods['run_script'];
      this.open_strings_table = this.$options.components['appui-roots-table'].methods['open_roots_table'];

    },
		methods: {
      render_projects(row){
        return '<a href="internationalization/components/languages/glossaries">'+ row.name +'</a>'
      },
      render_langs(row){
        var st = '';
        if ( ( row.langs !== null ) && ( row.langs.length ) ){
        	$.each( row.langs, (i, v) => {
            st += bbn.fn.get_field(this.source.primary, 'id', v, 'text') + '<br>'
          });

         return st;

        }
        else {
          st = 'Configure a language for this project';
        }
      },
      //I NEED TO HAVE HERE ONLY THE LANGUAGES WITH VALUE = 1
      cfg_root(row){
        $.each( this.source.primary, (a, z) => {
        	if( $.inArray(z.id, row.langs) ){
            this.active = 1
          }
      });
        return this.$refs.table1.edit(row, bbn._("Config languages for the project"));
			},

      //3 methods to render languages table
      render_language_name(row){
        //checks in the model of appui.languages if there's an array of translated strings for this index(lang)
        if( appui.languages.source[row.lang] ){
          //the click on the link will send the lang to the url as argument
          return '<a class="bbn-b bbn-l" title="Click on the language to open glossary table" href="' + this.base_url +'glossaries/'+ row.lang +'">' + row.lang + '</a>'
        }
        else {
          return '<span class="bbn-b bbn-l">' + row.lang + '</span> (there\'s no glossary table avalaible for this language)'
        }
      },

      render_untr_strings(row){
        return bbn.fn.money(row.strings) + ' strings to translate for this language'
      },

      //from this link the tab_path will be opened, arguments[0] will be used to take data from model
      render_path_childrens(row){
        return '<a class="bbn-b bbn-l"  href="' + this.base_url +'paths/'+ row.bbn_path +'">' + row.text + '</a>'
      },

    },

    components: {
      'appui-roots-table':{
        //expander
        template:'#appui-roots-table',
        props:['source'],
        data(){
          return{
            path: this.source.path
          }
        },
        methods: {
           open_strings_table(row){
            bbn.fn.link(bbn.vue.closest(this, 'bbn-tabnav').baseURL + 'strings_tab/' +row.id_option );
          },
          run_script(row){
            bbn.fn.post('internationalization/find_strings', row, (d) => {
              if( d.done > 0 ){
                bbn.fn.alert(d['todo'].length + ' successfully updated for '+ row.text + '<i class="fa' +
                  ' fa-smile-o"></i>!');
              }
              else{
                bbn.fn.alert('There are no new strings to update <i class="fa fa-frown-o" aria-hidden="true"></i>');
              }
            });
          },
        },
      },
      /*'appui-strings-table':{
        //expander
        template:'#appui-strings-table',
        props:['source'],
        data(){
          return{
          }
        },
        methods:{
         
        }
			},*/
      'appui-languages-form': {
				template: '#appui-languages-form',
       	methods:{
          inArray: $.inArray,
          change_checked_langs(val, obj){
            bbn.fn.log('event',obj.id)
            let form = bbn.vue.find(this, 'bbn-form'),
              checkboxes = bbn.vue.findAll(this, 'bbn-checkbox'),
              idx =  $.inArray(obj.id, this.source.row.langs);
            if( idx > -1 ){
              this.source.row.langs.splice(idx, 1)
            }
            else{
              this.source.row.langs.push(obj.id)
            }
          }
        },
        props: ['source'],
      },
    },
    watch:{
      home_switch(val, oldVal){
        if(val){
          this.search = false;
        }
      }
    }
  }
})();