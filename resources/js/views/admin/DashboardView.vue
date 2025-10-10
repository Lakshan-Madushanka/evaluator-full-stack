<template>
  <div class="space-y-12">
    <!--Cards -->
    <div
      class="sm:grid space-y-4 sm:space-y-0 grid-cols-2 gap-y-4 gap-x-8 xl:gap-y-0 xl:grid-cols-4 xl:gap-x-4"
    >
      <Card>
        <template #title>
          <div v-if="dashboardStore.data.usersCount.total">
            <OverlayBadge
              :value="dashboardStore.data.usersCount.total"
              class="inline-flex"
              severity="secondary"
              size="small"
            >
              <span>Users</span>
            </OverlayBadge>
          </div>
        </template>
        <template #content>
          <div v-if="dashboardStore.loading">
            <ProgressSpinner />
          </div>
          <ul v-else class="mt-[-5px]">
            <li
              class="w-full pr-4 flex justify-between border-b-2 border-neutral-100 border-opacity-100 py-1 dark:border-opacity-50"
            >
              <p class="">Super Admins</p>
              <Tag severity="secondary">{{ dashboardStore.data.usersCount.superAdmins }}</Tag>
            </li>
            <li
              class="w-full pr-4 flex justify-between border-b-2 border-neutral-100 border-opacity-100 py-1 dark:border-opacity-50"
            >
              <p class="">Admins</p>
              <Tag severity="secondary">{{ dashboardStore.data.usersCount.admins }}</Tag>
            </li>
            <li
              class="w-full pr-4 flex justify-between border-b-2 border-neutral-100 border-opacity-100 py-1 dark:border-opacity-50"
            >
              <p class="">Candidates</p>
              <Tag severity="secondary">{{ dashboardStore.data.usersCount.regular }}</Tag>
            </li>
          </ul>
        </template>
      </Card>
      <Card>
        <template #title> Categories </template>
        <template #content>
          <div v-if="dashboardStore.loading">
            <ProgressSpinner />
          </div>
          <Avatar v-else class="p-8 text-2xl font-bold">
            {{ dashboardStore.data.noOfCategories }}
          </Avatar>
        </template>
      </Card>
      <Card class="">
        <template #title> Questionnaires </template>
        <template #content>
          <div v-if="dashboardStore.loading">
            <ProgressSpinner />
          </div>
          <Avatar v-else class="p-8 text-2xl font-bold">
            {{ dashboardStore.data.noOfQuestionnaires }}
          </Avatar>
        </template>
      </Card>
      <Card class="">
        <template #title> Questions </template>
        <template #content>
          <div v-if="dashboardStore.loading">
            <ProgressSpinner />
          </div>
          <Avatar v-else class="p-8 text-2xl font-bold">
            {{ dashboardStore.data.noOfQuestions }}
          </Avatar>
        </template>
      </Card>
    </div>

    <!-- Charts -->
    <div v-if="categoryQuestionnairesChartData" class="flex flex-wrap justify-between space-y-4">
      <Chart
        type="bar"
        class="w-full lg:w-[48%] dark:shadow dark:shadow-white"
        :data="categoryQuestionnairesChartData"
      />
      <Chart
        type="bar"
        class="w-full lg:w-[48%] dark:shadow dark:shadow-white"
        :data="categoryQuestionsChartData"
      />
    </div>

    <!-- Table -->
    <div class="mt-8 whitespace-nowrap">
      <DataTable
        :value="dashboardStore.data.evaluations"
        responsive-layout="scroll"
        :loading="dashboardStore.loading"
        striped-rows
        data-key="id"
      >
        <template #header>
          <p class="text-2xl font-bold">Latest Evaluations</p>
        </template>
        <template #empty>
          <p v-if="!dashboardStore.loading" class="p-4 text-center text-2xl bg-blue-200">
            No records found.
          </p>
        </template>

        <!--No-->
        <Column field="no" header="No">
          <template #body="slotProps"> {{ slotProps.index + 1 }}</template>
        </Column>

        <!--User name-->
        <Column field="user_name" header="User Name">
          <template #body="slotProps"> {{ slotProps.data.user.name }}</template>
        </Column>

        <!--Questionnaire name-->
        <Column field="questionnaire_name" header="Questionnaire Name">
          <template #body="slotProps"> {{ slotProps.data.questionnaire.name }}</template>
        </Column>

        <!--Questionnaire name-->
        <Column field="marks" header="Marks(%)">
          <template #body="slotProps">
            <Tag severity="success" rounded class="ml-4">{{ slotProps.data.marks_percentage }}</Tag>
          </template>
        </Column>

        <!--Created at-->
        <Column field="data" header="Date">
          <template #body="slotProps">
            {{ moment(slotProps.data.created_at).format('ddd, MMM D, yyyy, h:mm a') }}
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
</template>

<script>
import { onMounted } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'
import { useDashboard } from '@/composables/dashboard'

import Card from 'primevue/card'
import Chart from 'primevue/chart'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import ProgressSpinner from 'primevue/progressspinner'
import OverlayBadge from 'primevue/overlaybadge'
import Avatar from 'primevue/avatar'
import { Tag } from 'primevue'

import moment from 'moment/moment'

export default {
  components: {
    Card,
    Chart,
    Column,
    DataTable,
    ProgressSpinner,
    OverlayBadge,
    Avatar,
    Tag
  },
  setup() {
    const dashboardStore = useDashboardStore()

    const { categoryQuestionnairesChartData, categoryQuestionsChartData } = useDashboard()

    onMounted(() => {
      dashboardStore.getMainDashnboardData()
    })

    return {
      dashboardStore,
      categoryQuestionnairesChartData,
      categoryQuestionsChartData,
      moment
    }
  }
}
</script>
