/**
 * Created by BBN on 10/02/2017.
 */
new Vue({
  el: $(ele).children()[0],
  data: data,
  mounted: function(){
    bbn.fn.log(this.$data);
  }
})