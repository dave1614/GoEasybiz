<template>
  <div>
    <CardBoxModal v-model="isModalActive" title="Sample modal">
      <p>Lorem ipsum dolor sit amet <b>adipiscing elit</b></p>
      <p>This is sample modal</p>
    </CardBoxModal>

    <CardBoxModal v-model="isModalDangerActive" title="Please confirm" button="danger" has-cancel>
      <p>Lorem ipsum dolor sit amet <b>adipiscing elit</b></p>
      <p>This is sample modal</p>
    </CardBoxModal>

    <div v-if="checkedRows.length" class="p-3 bg-gray-100/50 dark:bg-slate-800">
      <span v-for="checkedRow in checkedRows" :key="checkedRow.id"
        class="inline-block px-2 py-1 rounded-sm mr-2 text-sm bg-gray-100 dark:bg-slate-700">
        {{ checkedRow.name }}
      </span>
    </div>

    <table>
      <thead>
        <tr>
          
          <th></th>
          <th>Facility Name</th>
          <th>Dept.</th>
          <th>Sub Dept.</th>
          <th>Personnel</th>
          <th>Role</th>
          <th>Created</th>
          <th />
        </tr>
      </thead>
      <tbody>
        <tr v-for="role in roles.data" :key="role.id">
          <!-- <TableCheckboxCell v-if="checkable" @checked="checked($event, client)" /> -->
          <!-- <td class="border-b-0 lg:w-6 before:hidden">
            <UserAvatar :username="client.name" class="w-24 h-24 mx-auto lg:w-6 lg:h-6" />
          </td> -->
          <td>{{ role.index }}. </td>
          <td class="capitalize" data-label="Facility Name">
            {{ role.facility.name }}
          </td>
          <td data-label="Dept" class="capitalize" v-html="role.dept == null ? '' : role.dept.name">
            
          </td>
          <td data-label="Sub Dept" class="capitalize" v-html="role.sub_dept == null ? '' : role.sub_dept.name">
            
          </td>
          <td data-label="Personnel" v-html="role.personnel == null ? '' : role.personnel.name" class="capitalize">

          </td>
          <td data-label="Role" class="capitalize">
            {{ role.role.replaceAll('_', ' ') }}
          </td>
          <!-- <td data-label="Progress" class="lg:w-32">
            <progress class="flex w-2/5 self-center lg:w-full" max="100" :value="client.progress">
              {{ client.progress }}
            </progress>
          </td> -->
          <td data-label="Created" class="lg:w-1 whitespace-nowrap">
            <small class="text-gray-500 dark:text-slate-400" :title="role.created">{{ role.created }}</small>
          </td>
          <td class="before:hidden lg:w-1 whitespace-nowrap">
            <BaseButtons type="justify-start lg:justify-end" no-wrap>
              <BaseButton color="info" :icon="mdiAccountTie" small @click="isModalActive = true" />
              <BaseButton color="danger" :icon="mdiTrashCan" small @click="isModalDangerActive = true" />
            </BaseButtons>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="p-3 lg:px-6 border-t border-gray-100 dark:border-slate-800">
      <BaseLevel>
        <BaseButtons>
          <BaseButton v-for="page in roles.links" :key="page" :active="page.active" :label="page.label"
            :color="page.active ? 'lightDark' : 'whiteDark'" small @click="currentPage = page" :href="page.url != null ? page.url : ''" :disabled="page.url === null"/>
        </BaseButtons>
        <small>Page {{ roles.current_page }} of {{ roles.last_page }}</small>
      </BaseLevel>
    </div>
  </div>
</template>
<script setup>
import { computed, ref } from "vue";
import { useMainStore } from "@/stores/main";
import { mdiEye, mdiTrashCan, mdiAccountTie } from "@mdi/js";
import CardBoxModal from "@/components/CardBoxModal.vue";
import TableCheckboxCell from "@/components/TableCheckboxCell.vue";
import BaseLevel from "@/components/BaseLevel.vue";
import BaseButtons from "@/components/BaseButtons.vue";
import BaseButton from "@/components/BaseButton.vue";
import UserAvatar from "@/components/UserAvatar.vue";

const props = defineProps({
  checkable: Boolean,
  roles: {
    type: Object,
    required: true,
  },
});

console.log(props.roles)

const mainStore = useMainStore();

const items = computed(() => mainStore.clients);

const isModalActive = ref(false);

const isModalDangerActive = ref(false);

const perPage = ref(5);

const currentPage = ref(0);

const checkedRows = ref([]);

const itemsPaginated = computed(() =>
  items.value.slice(
    perPage.value * currentPage.value,
    perPage.value * (currentPage.value + 1)
  )
);

const numPages = computed(() => Math.ceil(items.value.length / perPage.value));

const currentPageHuman = computed(() => currentPage.value + 1);

const pagesList = computed(() => {
  const pagesList = [];

  for (let i = 0; i < numPages.value; i++) {
    pagesList.push(i);
  }

  return pagesList;
});

const remove = (arr, cb) => {
  const newArr = [];

  arr.forEach((item) => {
    if (!cb(item)) {
      newArr.push(item);
    }
  });

  return newArr;
};

const checked = (isChecked, client) => {
  if (isChecked) {
    checkedRows.value.push(client);
  } else {
    checkedRows.value = remove(
      checkedRows.value,
      (row) => row.id === client.id
    );
  }
};
</script>


