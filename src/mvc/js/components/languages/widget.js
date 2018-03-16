(() => {
  return {
    props: ['source'],
    name: 'widget',
    mounted(){
      if ( this.source.new > 0 ){
        appui.warning(
          this.source.new + ' new strings found in ' + this.source.path );
          //I remake_cache because i don't see the correct data in the widget
          this.remake_cache();
      }
    },
    methods: {
      open_strings_table(){
        //open the table of strings of this path combining new strings found in the files with strings present in db
        //send arguments[0] (id_option of the path) to 'internationalization/languages_tabs/path_translations/'

        //internationalization/languages_tabs/path_translations/ will return the cached_model in its data, if a
        // cached_model doesn't exist for this id_option it will be created
        if ( this.source.res.length >0 ){
          bbn.fn.link('internationalization/languages_tabs/path_translations/' + this.source.id_option);
        }
        else{
          appui.error('There are no strings in this path')
        }
      },
      remake_cache(){
        bbn.fn.post('internationalization/actions/reload_cache', { id_option: this.source.id_option }, (d) => {
          if ( d.success ){
            this.source.res = d.res;
            this.source.total = d.total;
            this.$nextTick( () => {
              this.source.languages = d.languages;
            })
          }
        })
      },
      get_field: bbn.fn.get_field,
      find_strings(){
        let url = bbn.vue.closest(this, 'bbn-tab').getComponent().source.root + 'actions/reload_cache';
        bbn.fn.post(url, { id_option: this.source.id_option },  (d) => {
          if ( d.success ){
            this.source.res = d.res;
            let diff = ( d.total - this.source.total );
            if ( diff > 0 ){
              appui.warning(diff + ' new string(s) found in ' + this.source.path);
            }
            else if ( diff < 0 ){
              appui.warning(Math.abs(diff) + ' string(s) deleted from ' + this.source.path + ' files');
            }
            this.source.total = d.total;
          }
        });

      },
      config_locale_dir(){
        bbn.vue.closest(this, 'bbn-tab').popup().open({
          width: 500,
          height: 600,
          title: bbn._("Define languages for the translation"),
          component: this.$options.components['appui-languages-form-locale'],
          source: {
            row: {
              id_option: this.source.id_option,
              //locale dirs
              languages: this.source.languages
            },
            data: {
              //langs configured for the project
              configured_langs: this.configured_langs,
              translated: this.translated,
              get_field: this.get_field,
              primary: this.primary,
              remake_cache: this.remake_cache
            }
          }
        })
      },
    },
    computed: {
      configured_langs(){
        return bbn.vue.closest(this, 'bbn-tab').getComponent().source.configured_langs
      },
      primary(){
        return bbn.vue.closest(this, 'bbn-tab').getComponent().source.primary;
      },
      translated(){
        var count = {};
        if( this.source.res.length ){

          let res = {};
          this.source.languages.forEach((a) => {
            res[a] = 0;
          });
          $.each(this.source.res, (i, v) => {
            if ( v.translation ){
              this.source.languages.forEach((lng) =>{
                if ( v.translation[lng] ){
                  res[lng]++
                }
              })
            }
          });
          return res;
        }
        else {
          res = [];
        }
      },
    },
    components: {
      'appui-languages-form-locale': {
        template: '#appui-languages-form-locale',
        computed: {
          message(){
            return 'If the language for which you want to create the translation file is not in this list, you have to configure it for the whole project using the form ( <i class="fa fa-cogs"></i> ) in the dashboard'
          },
         /* languages(){
            let res = [];
            if ( this.source.row.languages.length ){
              this.source.row.languages.forEach( (v) => {
                res.push(bbn.fn.get_field(this.source.data.primary, 'code', v, 'id') )
              } )
            }
            return res;
          }*/
        },
        methods: {
          //change the languages of locale dirs
          change_languages(val, obj){
            let idx =  $.inArray(obj.value, this.source.row.languages);
            if( idx > -1 ){
              this.source.row.languages.splice(idx, 1)
            }
            else{
              this.source.row.languages.push(obj.value)
            }
          },
          inArray: $.inArray,
        },
        props: ['source'],
      }
    }
  }
})();