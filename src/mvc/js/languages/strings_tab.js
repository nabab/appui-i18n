
(() => {
  return {
    created(){
      bbn.fn.log('.................................',this);
      this.source = bbn.vue.find(this,'appui-languages-strings_table' ).source;
    },
    props:['souce']

  }
})();