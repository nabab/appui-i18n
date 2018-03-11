(() => {
  return {
    props: ['source'],
    name: 'widget',
    mounted(){
      if ( this.source.new > 0 ){
        appui.warning(
          this.source.new + ' new strings found in ' + this.source.path )
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
      get_field: bbn.fn.get_field,
      find_strings(){
        let url = bbn.vue.closest(this, 'bbn-tab').getComponent().source.root + 'languages_tabs/data/widgets/';
        bbn.fn.post(url + this.source.id_option, (d) => {
          if ( d.total !== this.source.total ){
            let diff = d.total - this.source.total;
            if ( ( diff > 0 ) ){
              appui.warning(diff + ' new string(s) found in ' + this.source.path)
            }
            else if ( ( diff < 0 ) ){
              //Math.abs to have diff positive
              appui.warning(Math.abs(diff) + ' string(s) deleted from ' + this.source.path + ' files')
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
            id_option: this.source.id_option,
            configured_langs: this.configured_langs,
            languages: this.source.languages
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
          this.source.res.forEach((v, i) => {
            this.source.languages.forEach((lng) => {
              if ( v.translation[lng] ){
                res[lng]++
              }
            })
          });
          return res;
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
            let idx =  $.inArray(obj.value, this.source.languages);
            if( idx > -1 ){
              this.source.languages.splice(idx, 1)
            }
            else{
              this.source.languages.push(obj.value)
            }
          },
          inArray: $.inArray,
        },
        props: ['source'],
      }
    }
  }
})();