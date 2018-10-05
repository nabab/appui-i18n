<div class="languages bbn-full-screen">
  <bbn-tabnav :autoload="true">
    <bbns-tab url="dashboard"
              icon="fas fa-edit"
             	title="Projects Dashboard"
              :load="true"
              :source="source"
              :static="false"
              :pinned="true"
    ></bbns-tab>
    <!--
		<bbns-tab url="home"
							icon="fas fa-tasks"
              title="Projects table"
              :load="true"
              :source="source">
		</bbns-tab>
		-->
   <!-- <bbns-tab url="history"
              icon="fas fa-flag"
              title="Translations"
              :static="true"
              :load="true"
              :source="source"
    ></bbns-tab>-->
  </bbn-tabnav>
</div>
<script v-for="temp in source.templates" :id="temp.id" type="text/x-template" v-html="temp.html"></script>