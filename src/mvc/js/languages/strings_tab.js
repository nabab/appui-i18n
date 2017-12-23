
(() => {
  return {
    created(){

    },
    methods: {
      ciao(){alert('ciao')}
    },
    props:['source'],
    mounted(){
      bbn.fn.log('------------------------------------------', this)
    }

  }
})();