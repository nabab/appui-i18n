(() => {
  return {
    props: {
      source: {
        type: Object
      },
      currentLanguage: {
        type: String
      },
      primariesLanguages: {
        type: Array
      }
    },
    data(){
      return {
        root: appui.plugins['appui-i18n'] + '/'
      }
    },
    methods:{
      success(d){
        if (d.success) {
          appui.success(bbn._('Languages successfully updated'))
        }
        else {
          appui.error();
        }
      },
      toggleLang(val, obj){
        let idx = this.source.langs.indexOf(obj.id);
        if (idx > -1) {
          this.source.langs.splice(idx, 1);
        }
        else {
          this.source.langs.push(obj.id)
        }
      }
    }
  }
})();