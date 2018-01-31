

<div class="bbn-flex-width">

  <div class="bbn-full-screen bbn-c bbn-middle">
    <bbn-scroll>
      <ul class="statistic_list">
        <li v-for="(s, index) in source.statistics">
          <div class="statistic_description bbn-darkgrey" v-text="index"></div>

          <div v-if="( checktype(s) === 'string' ) || (checktype(s) === 'number' )"
               class="statistic_val bbn-purple"
               v-text="s"
          ></div>

          <div v-else-if="checktype(s) === 'object'"
               v-for="(z, idx) in s"
               class="statistic_val bbn-purple"
          >
            <div v-if="z['name']"
                 v-html="trophy_icon + ' ' + z['name'] + ' '  + z['value_occurrence'] + ' strings'"
            ></div>
            <div v-else
                 v-text="z"
            ></div>
          </div>

        </li>


        <li>

          <div class="statistic_description bbn-darkgrey">
            Strings translated from
          </div>
          <bbn-dropdown :source="source_langs"
                        v-model="source_lang"
          ></bbn-dropdown>

          <div class="statistic_description bbn-darkgrey"
               style="margin-left:0.3em">
            to
          </div>

          <bbn-dropdown :source="dropdown_source"
                        v-model="search_for_lang"
                        placeholder="Select a language"
          ></bbn-dropdown>
          <div class="statistic_val bbn-purple"
               v-html="!dd_ready ? '' : computed_result"
          >
          </div>
        </li>

        <li>
          <div class="statistic_description bbn-darkgrey"

          ></div>
          <bbn-progressbar :value="parseFloat(translatedPercentage)"
                           v-if="dd_ready"
                           type="percent"
                           :class="progressbar_color"
          ></bbn-progressbar>
        </li>

        <li>

          <!--the function on lang_statistic.source_lang in v-text is made for not having 'undefined' until lang_statistic.source_lang is filled after the post-->
          <div class="statistic_description bbn-darkgrey"
               v-text="'Total number of ' + (lang_statistic.source_lang ? lang_statistic.source_lang : '') + ' strings in all projects:'"
          ></div>
          <div class="statistic_val bbn-purple"
               v-text="lang_statistic.source_total_strings"
          >
          </div>
        </li>

        <!--<li>

          <div class="statistic_description bbn-darkgrey">
            Select a project for more informations
          </div>
          <bbn-dropdown :source="source_projects_dd"
                        v-model="project"
          >
          </bbn-dropdown>

        </li>-->






      </ul>
    </bbn-scroll>
  </div>

</div>