<div class="appui-languages bbn-full-screen">
  <bbn-tabnav :autoload="true"
  >
    <bbn-tab url="home"
             title="<i class='fa fa-home bbn-l' title='Projects'></i>"
             :static="true"
             :load="true"
             :source="source"

    ></bbn-tab>

    <bbn-tab url="history"
             title="<i class='fa fa-flag bbn-l' title='Translations'></i>"
             :static="true"
             :load="true"
             :source="source"
    ></bbn-tab>

  </bbn-tabnav>
</div>
<script v-for="temp in source.templates" :id="temp.id" type="text/x-template" v-html="temp.html"></script>