<template>
  <ConfirmDialog></ConfirmDialog>

  <AdminTableLayout>
    <template #table>
      <div>
        <DataTable
          :value="
            teamsQuestionnairesStore.questionnaires && teamsQuestionnairesStore.questionnaires.data
          "
          responsive-layout="scroll"
          :loading="teamsQuestionnairesStore.loading"
          striped-rows
          data-key="id"
          filter-display="row"
        >
          <template #empty>
            <p
              v-if="!teamsQuestionnairesStore.loading"
              class="p-4 text-center text-2xl bg-blue-200"
            >
              No records found.
            </p>
          </template>

          <template #header>
            <div class="flex justify-between items-center text-2xl uppercase">
              <div class="flex">
                <p class="mr-2">Questionnaires</p>
                <Avatar class="hover:cursor-pointer" icon="pi pi-eye" @click="toggleColumnsMenu" />
                <MenuComponent ref="columnsMenuRef" :model="columns" :popup="true">
                  <template #item="slotProps">
                    <div class="flex items-center p-2 hover:cursor-pointer w-full">
                      <div
                        v-if="slotProps['item']['label'] === 'Bulk Controllers'"
                        class="flex justify-between w-full text-sm text-blue-400"
                      >
                        <span class="hover:text-blue-800" @click="displayAllColumns"
                          >Display All</span
                        >
                        <span class="hover:text-blue-800" @click="hideAllColumns">Hide All</span>
                      </div>
                      <template v-else>
                        <i
                          :class="
                            columnVisibility[
                              snake(lowercaseFirstLetter(slotProps['item']['label'])).toLowerCase()
                            ]
                              ? 'pi pi-eye'
                              : 'pi pi-eye-slash'
                          "
                          class="mr-2"
                        ></i>
                        <p>{{ slotProps.item.label }}</p>
                      </template>
                    </div>
                  </template>
                </MenuComponent>
              </div>

              <div class="flex">
                <i
                  class="pi pi-align-justify hover:cursor-pointer lg:!hidden"
                  @click="toggleActionsMenu"
                ></i>
                <MenuComponent ref="actionsMenuRef" :model="actions" :popup="true" />

                <div class="hidden lg:flex">
                  <PrimeButton
                    icon="pi pi-refresh"
                    icon-pos="right"
                    class="!w-12 !py-1 !mr-4"
                    @click="reset"
                  />
                  <PrimeButton
                    icon="pi pi-filter"
                    class="!mr-4 !py-1"
                    icon-pos="right"
                    label="Apply Filters"
                    :disabled="Object.keys(filters).length === 0"
                    @click="applyFilters"
                  />
                </div>
              </div>
            </div>
          </template>

          <Column field="no" header="No">
            <template #body="slotProps"> {{ slotProps.index + 1 }}</template>
          </Column>
          <Column field="id" header="Id" :show-filter-menu="false" :hidden="!columnVisibility.id">
            <template #filter>
              <span>
                <IconField>
                  <InputIcon class="pi pi-search" />
                  <InputText
                    v-model="filters.id"
                    type="text"
                    placeholder="Search"
                    @keyup.enter="applyFilters"
                  />
                </IconField>
              </span>
            </template>
            <template #body="slotProps"> {{ slotProps.data.id }}</template>
          </Column>
          <Column
            field="name"
            header="Name"
            :show-filter-menu="false"
            :hidden="!columnVisibility.name"
          >
            <template #filter>
              <span>
                <IconField>
                  <InputIcon class="pi pi-search" />
                  <InputText
                    v-model="filters.name"
                    type="text"
                    placeholder="Search"
                    @keyup.enter="applyFilters"
                  />
                </IconField>
              </span>
            </template>
            <template #body="slotProps">
              {{ slotProps.data.attributes.questionnaire_name }}</template
            >
          </Column>
          <Column field="total_users" header="Total Users" :hidden="!columnVisibility.total_users">
            <template #body="slotProps">
              <Badge severity="info">{{ slotProps.data.attributes.total_users }}</Badge>
            </template>
          </Column>
          <Column
            field="attempted_users"
            header="Attempted Users"
            :hidden="!columnVisibility.attempted_users"
          >
            <template #body="slotProps">
              <Badge severity="info">{{ slotProps.data.attributes.attempted_users }}</Badge>
            </template>
          </Column>

          <!--Show Users-->
          <Column field="showResults" header="Results" :hidden="!columnVisibility.results">
            <template #body="slotProps">
              <router-link
                class="inlne-block mx-0 flex items-center justify-start hover:bg-transparent"
                :to="{
                  name: 'admin.teams.questionnaires.users.index',
                  params: {
                    id: slotProps.data.attributes.team_questionnaire_id
                  }
                }"
              >
                <Avatar icon="pi pi-eye" />
              </router-link>
            </template>
          </Column>

          <Column field="created_at" :hidden="!columnVisibility.created_at">
            <template #header>
              <div class="flex justify-between items-center w-full">
                <p>Created at</p>
                <SortComponent @direction-change="query.sort.created_at = $event" />
              </div>
            </template>
            <template #body="slotProps">
              {{
                moment(slotProps.data.attributes.created_at).format('ddd, MMM D, yyyy, h:mm a')
              }}</template
            >
          </Column>

          <!--Actions-->
          <Column field="Actions" header="Actions" :hidden="!columnVisibility.actions">
            <template #body="slotProps">
              <span class="p-buttonset space-x-1">
                <PrimeButton
                  v-if="slotProps.data.attributes.attempted_users === 0"
                  class="p-button-danger p-button-sm"
                  icon="pi pi-trash "
                  title="Delete"
                  :loading="teamsQuestionnairesStore.status === 'detaching'"
                  @click="deleteQuestionnaire(slotProps.data.id)"
                />
                <PrimeButton
                  v-else
                  v-tooltip="'Cannot remove a questionnaire when there are ateempted users'"
                  disabled
                  class="p-button-danger p-button-sm"
                  icon="pi pi-trash "
                  title="Delete"
                />
              </span>
            </template>
          </Column>

          <template #footer>
            <Paginator
              v-if="teamsQuestionnairesStore.users && showPaginator"
              :rows="teamsQuestionnairesStore.users && teamsQuestionnairesStore.users.meta.per_page"
              :total-records="
                teamsQuestionnairesStore.users && teamsQuestionnairesStore.users.meta.total
              "
              @page="onPage"
            >
            </Paginator>
            <p
              class="hidden sm:flex p-2 relative bottom-[-20px] w-full justify-center lg:justify-end"
            >
              {{ teamsQuestionnairesStore.users ? teamsQuestionnairesStore.users.meta.total : 0 }}
              records found.
            </p>
          </template>
        </DataTable>
      </div>
    </template>
  </AdminTableLayout>
</template>

<script>
import { onMounted, ref, reactive, watch } from 'vue'
import Paginator from '@/components/PaginatorComponent.vue'

import { useTeamsQuestionnairesStore } from '@/stores/teams/questionnaires'
import { useAuthStore } from '@/stores/auth'

import { useRouter, useRoute } from 'vue-router'

import moment from 'moment/moment'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import PrimeButton from 'primevue/button'
import Badge from 'primevue/tag'
import MenuComponent from 'primevue/menu'
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'

import SortComponent from '@/components/SortComponent.vue'

import { ROLES } from '@/constants'
import { lowercaseFirstLetter, snake } from '@/helpers'

export default {
  components: {
    AdminTableLayout,
    Avatar,
    PrimeButton,
    DataTable,
    Column,
    Badge,
    Paginator,
    SortComponent,
    InputText,
    MenuComponent,
    IconField,
    InputIcon,
    ConfirmDialog
  },
  setup() {
    const confirm = useConfirm()

    const teamsQuestionnairesStore = useTeamsQuestionnairesStore()
    const authStore = useAuthStore()

    const router = useRouter()
    const route = useRoute()

    const query = reactive({
      sort: {},
      pagination: { number: 1, size: 10 }
    })

    const actionsMenuRef = ref()
    const actions = ref([
      {
        label: 'Refresh',
        icon: 'pi pi-refresh',
        command: () => reset()
      },
      {
        label: 'Apply Filters',
        icon: 'pi pi-filter',
        command: () => applyFilters()
      }
    ])

    const columnVisibility = reactive({
      id: true,
      name: true,
      total_users: true,
      attempted_users: true,
      results: true,
      created_at: true,
      actions: true
    })
    const columnsMenuRef = ref()
    const columns = ref([
      {
        label: 'Bulk Controllers',
        command: () => {}
      },
      {
        label: 'Id',
        command: () => {
          columnVisibility.id = !columnVisibility.id
        }
      },
      {
        label: 'Name',
        command: () => {
          columnVisibility.name = !columnVisibility.name
        }
      },
      {
        label: 'Total Users',
        command: () => {
          columnVisibility.total_users = !columnVisibility.total_users
        }
      },
      {
        label: 'Attempted Users',
        command: () => {
          columnVisibility.attempted_users = !columnVisibility.attempted_users
        }
      },
      {
        label: 'Results',
        command: () => {
          columnVisibility.results = !columnVisibility.results
        }
      },
      {
        label: 'Created at',
        command: () => {
          columnVisibility.created_at = !columnVisibility.created_at
        }
      },
      {
        label: 'Actions',
        command: () => {
          columnVisibility.actions = !columnVisibility.actions
        }
      }
    ])

    const filters = ref({})
    const showPaginator = ref(true)

    onMounted(() => {
      teamsQuestionnairesStore.getAll(route.params.id, {
        query: { pagination: { number: 1, size: 10 } }
      })
    })

    watch(query, (newQuery) => {
      teamsQuestionnairesStore.getAll(route.params.id, {
        query: { ...newQuery, filters: filters.value }
      })
    })

    // We need to reset show paginator if it is disabled
    watch(teamsQuestionnairesStore, (newteamsQuestionnairesStore) => {
      if (!newteamsQuestionnairesStore.loading) {
        showPaginator.value = true
      }
    })

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function applyFilters() {
      query.pagination.number = 1
      showPaginator.value = false // Reset the pagination

      teamsQuestionnairesStore.getAll(route.params.id, {
        query: { filters: filters.value, ...query }
      })
    }

    function reset() {
      //Reset pagnator
      showPaginator.value = false
      query.pagination = { number: 1, size: 10 }

      //Reset filters
      filters.value = {}

      //Reset sort
      query.sort = {}

      teamsQuestionnairesStore.getAll(route.params.id, {
        query: { pagination: { number: 1, size: 10 } }
      })
    }

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
    }

    function toggleActionsMenu(event) {
      actionsMenuRef.value.toggle(event)
    }

    function deleteQuestionnaire(questionnaireId) {
      confirm.require({
        message: 'Do you want to delete this questionnaire? [This action cannot be undone !]',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        iconClass: 'bg-red-500',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Yes Delete',
        accept: async () => {
          await teamsQuestionnairesStore.detach(route.params.id, questionnaireId)
          reset()
        },
        reject: () => {}
      })
    }

    function displayAllColumns() {
      for (let visibility in columnVisibility) {
        columnVisibility[visibility] = true
      }
    }

    function hideAllColumns() {
      for (let visibility in columnVisibility) {
        columnVisibility[visibility] = false
      }
    }
    return {
      teamsQuestionnairesStore,
      onPage,
      moment,
      query,
      filters,
      applyFilters,
      showPaginator,
      reset,
      ROLES,
      columns,
      columnsMenuRef,
      toggleColumnsMenu,
      columnVisibility,
      lowercaseFirstLetter,
      snake,
      authStore,
      actionsMenuRef,
      actions,
      toggleActionsMenu,
      router,
      deleteQuestionnaire,
      hideAllColumns,
      displayAllColumns
    }
  }
}
</script>
