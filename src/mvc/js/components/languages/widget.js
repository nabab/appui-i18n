(() => {
  return {
    props: ['source'],
    name: 'widget',
    mounted(){
      if ( this.source.new > 0 ){
        appui.warning(
          this.source.new + ' new strings found in ' + this.source.path )
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
            bbn.fn.log(d)
            this.source.res = d.res;
          }
        })
      },
      get_field: bbn.fn.get_field,
      find_strings(){
        this.remake_cache();
        /*let url = bbn.vue.closest(this, 'bbn-tab').getComponent().source.root + 'actions/delete_cache';
        bbn.fn.post(url, {id_option: this.source.id_option})*/
        let url = bbn.vue.closest(this, 'bbn-tab').getComponent().source.root + 'languages_tabs/data/widgets/';

        bbn.fn.post(url + this.source.id_option, (d) => {
          bbn.fn.log(JSON.stringify(this.source.res), '---', JSON.stringify(d.res))
          if ( d.total !== this.source.total ){
            let diff = d.total - this.source.total;
            if ( ( diff > 0 ) ){
              appui.warning(diff + ' new string(s) found in ' + this.source.path);
              this.source.total = d.total;
              this.translated[this.source.source_lang] = d.total;
            }
            else if ( ( diff < 0 ) ){
              //Math.abs to have diff positive
              appui.warning(Math.abs(diff) + ' string(s) deleted from ' + this.source.path + ' files')
              this.source.total = d.total;
              this.translated[this.source.source_lang] = d.total;
            }
            else if ( ( diff = 0 ) ){
              //Math.abs to have diff positive
              appui.warning('No strings updated or deleted in this path')
              this.source.total = d.total;
              this.translated[this.source.source_lang] = d.total;
            }
          }
        });
      },
      config_locale_dir(){
        bbn.vue.closest(this, 'bbn-tab').popup().open({
          width: 500,
          height: 500,
          title: bbn._("Config locale folder for translation files"),
          component: this.$options.components['appui-languages-form-locale'],
          source: {
            row: {
              id_option: this.source.id_option,
              languages: this.source.languages
            },
            data: {
              configured_langs: Object.values(this.configured_langs),
              translated: this.translated
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
            return 'If the language for which you want to create the folder is not in this list, you have to configure it for the whole project using the form ( <i class="fa fa-cogs"></i> ) in the dashboard'
          },
        },
        methods: {
          //change the languages of locale dirs
          change_languages(val, obj){
            let idx =  $.inArray(obj.value, this.source.row.languages);
            bbn.fn.log('val', val, obj, idx)
            if( idx > -1 ){
              this.source.row.languages.splice(idx, 1)
            }
            else{
              bbn.fn.log('else', this.source.row.languages, obj.value)
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