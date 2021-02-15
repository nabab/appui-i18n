<div class="appui-i18n-languages languages bbn-overlay">
  <bbn-router :autoload="true" :nav="true">
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
  </bbn-router>
</div>
<?php
foreach ($templates as $tpl) {
  ?><script id="<?=$tpl['id']?>" type="text/x-template">
    <?=$tpl['html']?>
  </script>
<?php
}
