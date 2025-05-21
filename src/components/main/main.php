<div class="appui-i18n-main bbn-overlay">
  <div bbn-if="isLoading"
       class="bbn-overlay bbn-modal">
    <bbn-loader font-size="l"/>
  </div>
  <bbn-router bbn-else
              :autoload="true"
              :nav="true">
    <bbns-container url="dashboard"
                    icon="nf nf-md-view_dashboard_variant"
                    :label="_('Projects Dashboard')"
                    component="appui-i18n-dashboard"
                    :source="data"
                    :fixed="true"/>
  </bbn-router>
</div>