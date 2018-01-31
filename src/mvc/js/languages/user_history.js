(() => {
  return {
    props: ['source'],
    data(){
      return {
        //primary is used to render the name of languages in the table
        primary: bbn.vue.closest(this, 'bbn-tabnav').$parent.source.primary,
        ready: false,

      }
    },
    mounted(){
      //this timeout is to wait for bbn-table to be mounted
   /*   setTimeout( () => {
        if( this.$refs.user_history ){
          this.editedRow = false;
          this.ready = true;
          )
        }
      }, 3000)*/
    },
    computed: {
    /*  editedRow(){
        if(this.ready){
          return this.$refs.user_history.editedRow
        }
      }*/
    },
    watch: {
/*
      editedRow(val){
        if ( val ){
          alert(val)
          bbn.fn.log('vaffancuuuuuuuuuuuuuuuuuuuulo')
          $(".bbn-input input", this.$refs.strings_table.$el).off('keyup');
          $(".bbn-input input", this.$refs.strings_table.$el).on('keyup', (e) => {
            if ( e.keyCode === 13){
              this.pressedEnterKey = true;
              e.preventDefault();
            }
            else {
              this.pressedEnterKey = false;
            }
          })
        }
      }*/
    },
    methods: {
      render_empty_expression(row){
        return row.expression.length ? row.expression : '<span style="color:grey; opacity:0.4"> This expression has' +
          ' been' +
          ' deleted' +
          ' from db </span>'
      },
      render_lang(row){
        let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.lang , 'text')
        return st;
      },
      render_original_lang(row){
        let st = '';
        st += bbn.fn.get_field( this.primary, 'code', row.original_lang , 'text')
        return st;
      },
    },
  }
})();