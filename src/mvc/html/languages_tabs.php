<div class="appui-languages bbn-full-screen">
  <bbn-tabnav :autoload="true"
  >
   
      
    <bbn-tab url="dashboard"
             title="<i class='fa fa-edit bbn-lg' title='Dashboard'></i>Dashboard"
             :static="true"
             :load="true"
             :source="source"
    ></bbn-tab>
  
    
    <bbn-tab url="home"
             title="<i class='fa fa-tasks bbn-lg' title='Projects'></i>Projects table"
             :load="true"
             :source="source"
    ></bbn-tab>

    <bbn-tab url="history"
             title="<i class='fa fa-flag bbn-lg' title='Translations'></i>Translations"
             :static="true"
             :load="true"
             :source="source"
    ></bbn-tab>

  </bbn-tabnav>
</div>
<script v-for="temp in source.templates" :id="temp.id" type="text/x-template" v-html="temp.html"></script>