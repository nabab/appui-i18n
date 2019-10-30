<div class="languages bbn-overlay">
  <bbn-tabnav :autoload="true">
    <bbns-container url="dashboard"
                    icon="nf nf-fa-edit"
                    :title="_('Projects Dashboard')"
                    :load="true"
                    :source="source"
                    :static="false"
                    :pinned="true"
    ></bbns-container>
    <!--
		<bbns-container url="home"
							icon="nf nf-fa-tasks"
              title="Projects table"
              :load="true"
              :source="source">
		</bbns-container>
		-->
   <!-- <bbns-container url="history"
              icon="nf nf-fa-flag"
              title="Translations"
              :static="true"
              :load="true"
              :source="source"
    ></bbns-container>-->
  </bbn-tabnav>
</div>
<script v-for="temp in source.templates" :id="temp.id" type="text/x-template" v-html="temp.html"></script>