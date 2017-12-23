//the first 3 buttons are like radio, v-model on the same property and assign a value from 1 to 3
(() => {
  return {
    data(){
      return {
        //the source language of the root, i'll receive it from the model
        source_lang: false,
        home_switch: 1,
        base_url: bbn.vue.closest(this, 'bbn-tabnav').baseURL,
        //search = 1 after the click on the search button (visible only on user_history and complete history tables). If search = 1 an autocomplete will be shown
        search: 0,
        //history and complete_history_access are created to take the time to fill the data of tables after the click on home buttons
				history: false,
        user_history : [{ exp : '', name: '', lang: '', last_modified:''}],
        complete_history_access: false,
        // complete_history will be fill only if the user is_admin at the moment of the click on the fourth button
        complete_history: [{ exp : '', name: '', lang: '', last_modified:''}],
        search_value : '',
        autocomplete_source: [],
        page_title: 'HomePage'
      }
    },
    created(){
      this.run_script = this.$options.components['appui-roots-table'].methods['run_script'];
      this.open_strings_table = this.$options.components['appui-roots-table'].methods['open_roots_table'];
			this.path_code = this.$options.components['appui-roots-table'].methods['path_code'];

    },
		methods: {
      alert(){
        bbn.fn.log('fgs')
      },
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
    computed: {

    },

    components: {
      'appui-roots-table':{
        //expander
        template:'#appui-roots-table',
        props:['source'],
        data(){
          return{
            path: this.source.path,
            id_project: this.source.id,
         	}
        },
        methods: {
          open_strings_table(row){
            bbn.fn.link(bbn.vue.closest(this, 'bbn-tabnav').baseURL + 'strings_tab/' + row.id_option );
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
        components: {
          'languages-progress-bar':{
            template: '<bbn-progressbar :value="source"></bbn-progressbar>',
            props: ['source'],
            beforeMount(){
            },
          },
        }
      },

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
      search_value(val, oldVal){
        let lang = bbn.fn.get_field(
          this.complete_history_access ? this.complete_history : this.user_history, 'id_exp', val, 'lang'
        );

        if ( lang ){
          if ( this.complete_history_access ){
            bbn.fn.post('internationalization/languages/history_table', {
              'search': 1,
              'id_exp' : val,
              'lang': lang
            }, (d) => {
              if ( d.success ){

              }
            } );
          }

        }

      },
      complete_history(val, oldVal){
        if( val ){
          return this.autocomplete_source = this.complete_history.map( (i) => {       return{
              value : i['id_exp'],
              text : i['expression']
            }
          });
        }
      },
      user_history(val, oldVal){
        if( val ){
          return this.autocomplete_source = this.user_history.map( (i) => {
            return{
              value : i['id_exp'],
              text : i['expression']
            }
          });
        }
      },
      home_switch(val, oldVal){
        if ( val ){
          this.page_title = 'HomePage'
          this.search = false;
          this.history = false;
          this.complete_history_access = false;

          if ( val === 2 ){
            this.history = false;
            this.page_title = 'Languages';
            this.complete_history_access = false;
            bbn.fn.post('internationalization/languages/statistic_table', (d) => {
              if ( d.success ){
                bbn.fn.log( 'success = ' + d.success )
              }
            })
          }
          else if ( val === 3 ){
						//if click on history button, property home_switch will be = 3, and a controller will be called, at success = true, data for the history table
						//(history table will be shown when v-if = history = true)
            this.page_title = 'User\'s translations'
            this.complete_history_access = false;
            bbn.fn.post('internationalization/languages/history_table', (d) => {
							if ( d.success ){
							  if ( d.user_history.length ){
							    this.user_history = d.user_history;
                  bbn.fn.log(JSON.stringify(this.user_history))
							    this.$nextTick( () => {
          bbn.fn.log(JSON.stringify(this.user_history))
                    return [
                      //this.history is the v-if on the users_history_table
                      this.history = true,
                      this.is_admin = d.is_admin,
                    ];
                  });
                }
              }
						});
					}
					/*the table of complete history of all users will be shown if complete_history_access = true and home_switch =4*/
          else if ( val === 4 ){
            this.page_title = 'All users\' translations'
            bbn.fn.post('internationalization/languages/history_table', { is_admin : this.source.is_admin }, (d) => {
              if ( d.success ){
                if ( d.complete_history.length ){
                  this.complete_history = d.complete_history;
                  this.$nextTick( () => {
                    this.history = false;
                    return this.complete_history_access = true;
                  });
                }
              }
            });
          }
        }
      },


    }
  }
})();