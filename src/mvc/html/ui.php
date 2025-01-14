<div class="appui-i18n-languages languages bbn-overlay">
  <bbn-router :autoload="true" :nav="true">
    <bbns-container url="dashboard"
                    icon="nf nf-fa-edit"
                    :label="_('Projects Dashboard')"
                    :load="true"
                    :source="source"
                    :static="false"
                    :pinned="true"
    ></bbns-container>
    <!--
		<bbns-container url="home"
							icon="nf nf-fa-tasks"
              label="Projects table"
              :load="true"
              :source="source">
		</bbns-container>
		-->
   <!-- <bbns-container url="history"
              icon="nf nf-fa-flag"
              label="Translations"
              :fixed="true"
              :load="true"
              :source="source"
    ></bbns-container>-->
  </bbn-router>
</div>
<?php
foreach ($templates as $tpl) {
  ?><script id="<?= $tpl['id'] ?>" type="text/x-template">
    <?= $tpl['html'] ?>
  </script>
<?php
}
