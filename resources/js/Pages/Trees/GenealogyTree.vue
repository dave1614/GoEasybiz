<style>
/* tbody tr {
  cursor: pointer;
} */

.frame-area {
  display: block;
  width: 100%;
  /* max-width: 400px; */
  height: 1000px;
  /* overflow: auto; */
  border: #999999 1px solid;
  margin: 0px;
  padding: 0px;
}


.ratio {
  font-weight: bold;
}

.tf-tree {
  text-align: center;
  /*cursor: col-resize;*/
}

.tf-tree .tf-nc .name {
  font-size: 13px;
}

.tf-tree .tf-nc {
  border: 0;

  /*width: 100%;*/
  /*position: relative;*/


  /*width: 150px;
      height: 220px;
      background: #fff;
      border: 0;
      border-radius: 4px;*/

  /*cursor: pointer;*/

}

.tf-tree .tf-nc .tree_icon {
  cursor: pointer;
  width: 74px;
  border: 2px solid #c8d5d8;
  border-radius: 50%;
  padding: 4px;
  background: #fff;
}

.tf-tree .tf-nc .demo_name_style {
  background-color: #5c519f;
  padding: 2px 3px 4px 3px;
  border-radius: 2px;
  margin-top: 5px;
  margin-bottom: 0;
  color: #fff;
  width: 150px;
}

/* .tf-tree .tf-nc .icons-div { */
  /*margin-top: 10px;
      margin-bottom: 20px;*/
/* } */

/*.tf-nc.business{
      border: 5px solid #89229b;
      box-shadow: 0 2px 6px 0 #89229b;
    }

    .tf-nc.basic{
      border: 5px solid #4caf50;
      box-shadow: 0 2px 6px 0 #4caf50;
    }

    .tf-nc.basic .package{
      color: #4caf50;
      text-transform: uppercase;
      font-weight: 700;
    }

    .tf-nc.business .package{
      color: #89229b;
      text-transform: uppercase;
      font-weight: 700;
    }*/
/*.tf-nc .register-text{
      line-height: 200px;
      font-size: 19px;
      font-weight: bold;
    }

    .tf-nc.register{
      border: 5px solid #000;
    }*/

.tf-tree .tf-nc .user-name {
  font-weight: bold;
  font-size: 12px;
}

/* .tf-custom .tf-nc:before,
.tf-custom .tf-nc:after {
  
}

.tf-custom li li:before {
  
} */

.spinner {
  display: none;
}

.tf-hover-more-info {
  padding: 0;
}

.tf-hover-more-info .first-row {
  background: #40b7e5;
}

.tf-hover-more-info .first-row img {
  margin-top: 7px;
  width: 60px !important;
  height: 60px !important;
  border-radius: 50% !important;
  border: 1px solid #0bb4f5;
  background: #fff;
  padding: 4px;
}

.tf-hover-more-info .first-row p {
  font-weight: bold;
  color: #fff;

}

.tf-hover-more-info .first-row p:first-child {
  margin-top: 10px;
  margin-bottom: 2px;

}

.tf-hover-more-info .second-row {
  padding: 0;
  padding: 15px;
}

.tf-hover-more-info .second-row p {
  color: #000;
}

.tf-hover-more-info .second-row p:first-child {
  margin-bottom: 0;
}

.tf-hover-more-info .second-row p span {
  font-weight: bold;
}

img {
  -webkit-touch-callout: none;
}
</style>
<template>
  <LayoutAuthenticated>

    <Head title="Genealogy Tree" />
    <SectionMain>
      <SectionTitleLineWithButton :icon="mdiChartTimelineVariant" title="Genealogy Tree" main>
        <!-- <BaseButton
          href="https://github.com/justboil/admin-one-vue-tailwind"
          target="_blank"
          :icon="mdiGithub"
          label="Star on GitHub"
          color="contrast"
          rounded-full
          small
        /> -->
      </SectionTitleLineWithButton>


      <div class="content">
        <div class="overlay" style="display: none;"></div>
        <div id="other-overlay" :style="show_other_overlay == true ? 'display: block;' : 'display: none;' "></div>
        <div class="">
          <div class="container-fluid">
            <div class="row justify-content-center" style="">
      
              <CardBox class="" v-if="user.is_admin == 1">
                <div class="card-body">
      
                  <form action="" id="search-mlm-form" onsubmit="submitSearchMlmInput(this,event)">
                    <div class="form-group">
                      <input type="text" id="search-mlm-input" name="search-mlm-input" class="w-full rounded-md border-gray-300 text-gray-500"
                        :value="user.phone">
                    </div>
                    
                    <BaseButtons class="mt-4">
                      <BaseButton label="Search" color="success" type="submit" />
                      <!-- <input type="button" class="btn btn-info" value="" "> -->

                      <BaseButton onclick="clearSearchInputTextField(this,event)" label="Clear" color="info"  />
                    </BaseButtons>
                  </form>
                </div>
              </CardBox>
      
              <div class="col-md-12" id="main-page-col-md-12" v-html="downline_html">
      
      
              </div>
            </div>
      
      
          </div>
        </div>
      
        <!-- <div @click="goBack">
              <floating-action-button :styles="'background: 9124a3;'" :title="'Go Back'">
                
                <i class="fas fa-arrow-left" style="font-size: 25px; color: #fff;"></i>
              </floating-action-button>
            </div> -->
      
        <footer class="footer">
          <div class="container-fluid">
      
          </div>
        </footer>
      </div>

    </SectionMain>
  </LayoutAuthenticated>
</template>

<script setup>
import { useForm, usePage, Head } from '@inertiajs/inertia-vue3';
import { computed, ref, onMounted } from "vue";
import { useMainStore } from "@/stores/main";
import {
  mdiAccountMultiple,
  mdiCartOutline,
  mdiChartTimelineVariant,
  mdiMonitorCellphone,
  mdiReload,
  mdiGithub,
  mdiChartPie,
  mdiWallet,
  mdiCash,
  mdiCashCheck,
  mdiTable,
  mdiOpenInNew,
  mdiFacebook,
  mdiTwitter,
  mdiWhatsapp,
  mdiAccount,
  mdiAccountMultiplePlus,
  mdiAccountGroup,
  mdiCreditCardPlus,
} from "@mdi/js";
import * as chartConfig from "@/Components/Charts/chart.config.js";
import LineChart from "@/Components/Charts/LineChart.vue";
import SectionMain from "@/Components/SectionMain.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import CardBox from "@/Components/CardBox.vue";
import TableSampleClients from "@/Components/TableSampleClients.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBoxTransaction from "@/Components/CardBoxTransaction.vue";
import CardBoxClient from "@/Components/CardBoxClient.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import SectionBannerStarOnGitHub from "@/Components/SectionBannerStarOnGitHub.vue";
import UserCard from "@/components/UserCard.vue";
import UserAvatarCurrentUser from "@/components/UserAvatarCurrentUser.vue";
import MultipurposeButton from '@/Components/MultipurposeButton.vue';
import BaseButtons from '@/Components/BaseButtons.vue';
import FormField from '@/Components/FormField.vue';
import FormControl from '@/Components/FormControl.vue';
import UserAvatar from '@/Components/UserAvatar.vue';


const page = usePage()
const chartData = ref(null);

const fillChartData = () => {
  chartData.value = chartConfig.sampleChartData();
};

const mainStore = useMainStore();
onMounted(() => {
  fillChartData();
  // mainStore.changeIsAdminVal(true)
});

const i = 1;


const clientBarItems = computed(() => mainStore.clients.slice(0, 4));

const transactionBarItems = computed(() => mainStore.history);


const hasTermsAndPrivacyPolicyFeature = computed(() => page.props.value?.jetstream?.hasTermsAndPrivacyPolicyFeature)
const user = page.props.value?.user;
const downline_html = page.props.value?.downline_html;

const show_other_overlay = ref(false);



</script>
