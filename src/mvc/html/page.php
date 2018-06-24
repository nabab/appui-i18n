<div class="appui-languages bbn-full-screen">
  <bbn-tabnav :autoload="true"
  >
   
      
    <bbns-tab url="dashboard"
             title="<i class='fa fa-edit bbn-lg' title='Dashboard'></i>Projects Dashboard"
             :load="true"
             :source="source"
             :static="false"
             :pinned="true"
    ></bbns-tab>
  
    
  <!--  <bbns-tab url="home"
             title="<i class='fa fa-tasks bbn-lg' title='Projects'></i>Projects table"
             :load="true"
             :source="source"
    ></bbns-tab>
-->
    <bbns-tab url="history"
             title="<i class='fa fa-flag bbn-lg' title='Translations'></i>Translations"
             :static="true"
             :load="true"
             :source="source"
    ></bbns-tab>

  </bbn-tabnav>
</div>
<script v-for="temp in source.templates" :id="temp.id" type="text/x-template" v-html="temp.html"></script>