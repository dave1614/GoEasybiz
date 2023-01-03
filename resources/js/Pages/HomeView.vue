
<template>
  <LayoutAuthenticated>
    <Head title="Dashboard" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiChartTimelineVariant"
        title="Overview"
        main
      >
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

      <CardBox class="mb-5" v-if="user.providus_account_number != null && user.providus_account_name != null">
        <div class="mx-3 my-5 text-sm">
          <h3 class="mb-3 text-2xl font-bold">Personalized Account Funding</h3>
          
          <p class="font-bold text-primary mb-4 mt-2">
            <span>Funding less than ₦9000 is ₦25, while above ₦9000 cost ₦75 only.</span>
          </p>

          <!-- <h4 class="text-lg ">Bank Name</h4>
          <p class="text-primary">Providus Bank</p>

          <h4 class="text-lg mt-2">Account Name</h4>
          <p class="text-primary" v-html="user.providus_account_name"></p>
          
      
          <h4 class="text-lg mt-2">Account Number</h4>
          <p class="text-primary">{{user.providus_account_number}}</p> -->

          <table>
            <thead>
              <tr>
          
                <th></th>
                
                <th>Bank Name</th>
                <th>Account Name</th>
                <th>Account Number</th>
              </tr>
            </thead>
            <tbody>

              <tr v-if="user.providus_account_number != null">
                <td></td>
                <td>Providus Bank</td>
                <td>{{ user.providus_account_name }}</td>
                <td>{{ user.providus_account_number }}</td>
              </tr>

              <!-- <tr v-if="monnify_details.wema_account_number != null">
                <td></td>
                <td>Wema Bank</td>
                <td>{{ monnify_details.wema_account_name }}</td>
                <td>{{ monnify_details.wema_account_number }}</td>
              </tr>

              <tr v-if="monnify_details.sterling_account_number != null">
                <td></td>
                <td>Sterling Bank</td>
                <td>{{ monnify_details.sterling_account_name }}</td>
                <td>{{ monnify_details.sterling_account_number }}</td>
              </tr>

              <tr v-if="monnify_details.fidelity_account_number != null">
                <td></td>
                <td>Fidelity Bank</td>
                <td>{{ monnify_details.fidelity_account_name }}</td>
                <td>{{ monnify_details.fidelity_account_number }}</td>
              </tr>

              <tr v-if="monnify_details.moniepoint_account_number != null">
                <td>Moniepoint</td>
                <td>{{ monnify_details.moniepoint_account_name }}</td>
                <td>{{ monnify_details.moniepoint_account_number }}</td>
              </tr> -->
            </tbody>
          </table>
      
        </div>
      </CardBox>

      <div class="grid grid-cols-1 gap-6 lg:grid-cols-4 mb-6">
        <CardBoxWidget
          :trend="(last_account_statement.amount != 0.00) ? `${(((last_account_statement.amount_after - last_account_statement.amount_before) / last_account_statement.amount_before) * 100).toFixed(2)}%` : null"
          
          :trend-type="last_account_statement.amount_before < last_account_statement.amount_after ? 'up' : 'down'"
          color="text-yellow-500"
          :icon="mdiWallet"
          :number="parseFloat(last_account_statement.amount_after)"
          label="E-Wallet"
          prefix="₦"
        />
        <CardBoxWidget
          
          color="text-blue-500"
          :icon="mdiCash"
          :number="parseFloat(user.total_earnings)"
          prefix="₦"
          label="Total Commission Earned"
        />

        <CardBoxWidget
          
          color="text-orange-500"
          :icon="mdiCashPlus"
          :number="parseFloat(last_week_commission)"
          prefix="₦"
          label="Last Week Commision"
        />

        <CardBoxWidget
          
          color="text-slate-300"
          :icon="mdiCashPlus"
          :number="parseFloat(total_upteam_earnings)"
          prefix="₦"
          label="Upteam Support Bonus"
        />

        <CardBoxWidget
          
          color="text-red-500"
          :icon="mdiCashCheck"
          :number="parseFloat(user.total_withdrawan)"
          prefix="₦"
          label="Withdrawn"
        />

        <CardBoxWidget
          color="text-primary"
          :icon="mdiTable"
          :number="left_team_total"
          label="Team Total (Left)"
        />

        <CardBoxWidget
          color="text-primary"
          :icon="mdiTable"
          :number="right_team_total"
          label="Team Total (Right)"
        />
        <!-- <CardBoxWidget
          trend="Overflow"
          trend-type="alert"
          color="text-red-500"
          :icon="mdiChartTimelineVariant"
          :number="total_amount_wthdrawn"
          suffix="%"
          label="Withdrawn"
        /> -->
        
      </div>

      <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-1">
        <CardBox class="">
          <UserAvatarCurrentUser class="h-24 w-24 mx-auto" />
          
          <div class="mt-6 text-center">
            <p class="text-xs text-gray-400 font-semibold">{{ user.email }}</p>
            <p class="text-lg text-gray-500 font-bold mt-3" v-html="`${user.phone_code}${user.phone}`"></p>
            <p class="text-2xl dark:text-white text-black font-bold mt-3" v-html="`${user.name}`"></p>
          </div>
          <div class="flex justify-center mt-6 text-center">
            <MultipurposeButton :href="route('edit_profile')" label="Edit Profile" />
          </div>

          <h5 class="text-xl font-bold mt-8 text-center">Promotion Tools</h5>
          <div class="grid grid-cols-12 gap-6 mt-4">
            <span class="text-gray-400 text-sm font-semibold text-left col-span-6">Referral Link</span>
            <div class="col-span-6 ">
              <BaseButtons class="float-right">
                <BaseButton color="info" :icon="mdiFacebook" target="_blank" :href="`https://www.facebook.com/sharer/sharer.php?u=${displayForm.social_url}`" />
                <BaseButton color="contrast" :icon="mdiTwitter" target="_blank" :href="`http://twitter.com/share?url=${displayForm.social_url}`"  />
                <BaseButton color="success" :icon="mdiWhatsapp" target="_blank" :href="`https://api.whatsapp.com/send?text=${displayForm.social_url}`"  />
                
              </BaseButtons>
            </div>

          </div>
          <FormField class="mt-2" label="">
            <FormControl v-model="displayForm.url" :icon="mdiAccount" name="url" />
          </FormField>
        </CardBox>
      </div>


      <!-- <SectionBannerStarOnGitHub class="mt-6 mb-6" /> -->

      <!-- <SectionTitleLineWithButton :icon="mdiChartPie" title="Trends overview">
        <BaseButton
          :icon="mdiReload"
          color="whiteDark"
          @click="fillChartData"
        />
      </SectionTitleLineWithButton>

      <CardBox class="mb-6">
        <div v-if="chartData">
          <line-chart :data="chartData" class="h-96" />
        </div>
      </CardBox> -->

      <SectionTitleLineWithButton :icon="mdiAccountMultiple" title="Team Perfomance" />

      <NotificationBar color="info" :icon="mdiMonitorCellphone">
        <b>Top 10 Mlm Earners</b>
      </NotificationBar>

      <CardBox has-table>
        <table>
          <thead>
            <tr>
              
              <th>#</th>
              <!-- <th /> -->
              <th>Full Name</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user,index) in top_ten_earners.data" :key="user.id">
              <!-- <td>{{ i++ }}.</td> -->
              <td v-html="`${(index + 1) + ((top_ten_earners.current_page - 1) * 10)}.`"></td>
              <!-- <td class="border-b-0 lg:w-6 before:hidden">
                <UserAvatar :username="user.name" class="w-24 h-24 mx-auto lg:w-6 lg:h-6" />
              </td> -->
              <td data-label="Name">
                {{ user.name }}
              </td>
              <td data-label="Amount" v-html="mainStore.addCommas(user.total_earnings)">
                
              </td>
              
            </tr>
          </tbody>
        </table>
        <!-- <TableSampleClients /> -->
      </CardBox>
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
  mdiCashPlus,
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
const monnify_details = page.props.value?.monnify_details;
console.log(monnify_details)

const top_ten_earners = page.props.value?.top_ten_earners;
const downline_arr_num = page.props.value?.downline_arr_num;
const left_team_total = page.props.value?.left_team_total;
const right_team_total = page.props.value?.right_team_total;
const last_week_commission = page.props.value?.last_week_commission;
const total_upteam_earnings = page.props.value?.total_upteam_earnings;


// const total_amount_wthdrawn = page.props.value?.user;

const displayForm = useForm({
  url: route('register',{
    c: user.country_id,
    p: user.phone,
  }),
  social_url: route('register',{
    
    p: user.phone,
  })
})

Swal.close();

const account_created = page.props.value?.account_created;
const last_account_statement = page.props.value?.last_account_statement;


console.log(last_account_statement)

if (account_created) {
  setTimeout(() => {
    Swal.fire({
      title: 'Account creation successful',
      html: 'Your account has been successfully created. Welcome to Go-Easybiz',
      icon: 'success',
    })
  }, 3000);


}
</script>
