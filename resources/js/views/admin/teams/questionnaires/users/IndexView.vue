<template>
  <AdminTableLayout>
    <template #table>
      <div>
        <DataTable
          :value="teamsQuestionnairesStore.users && teamsQuestionnairesStore.users.data"
          responsive-layout="scroll"
          :loading="teamsQuestionnairesStore.loading"
          striped-rows
          data-key="id"
          filter-display="row"
        >
          <template #empty>
            <p
              v-if="!teamsQuestionnairesStore.loading"
              class="p-4 text-center text-2xl bg-gray-800 text-white"
            >
              No records found.
            </p>
          </template>

          <template #header>
            <div class="flex justify-between items-center text-2xl uppercase">
              <div class="flex">
                <p class="mr-2">Users</p>
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
                              snake(lowercaseFirstLetter(slotProps['item']['label']))
                                .toLowerCase()
                                .replaceAll(' ', '')
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
          <Column
            field="userId"
            header="User ID"
            :show-filter-menu="false"
            :hidden="!columnVisibility.user_id"
          >
            <template #body="slotProps"> {{ slotProps.data.attributes.user_id }}</template>
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
              <span severity="info" class="mr-1">
                {{
                  findRelations(
                    teamsQuestionnairesStore.users.included,
                    slotProps.data.relationships.user.data.id,
                    slotProps.data.relationships.user.data.type
                  ).attributes.name
                }}
              </span>
            </template>
          </Column>

          <Column
            field="email"
            header="Email"
            :show-filter-menu="false"
            :hidden="!columnVisibility.email"
          >
            <template #filter>
              <span>
                <IconField>
                  <InputIcon class="pi pi-search" />
                  <InputText
                    v-model="filters.email"
                    type="text"
                    placeholder="Search"
                    @keyup.enter="applyFilters"
                  />
                </IconField>
              </span>
            </template>
            <template #body="slotProps">
              <span severity="info" class="mr-1">
                {{
                  findRelations(
                    teamsQuestionnairesStore.users.included,
                    slotProps.data.relationships.user.data.id,
                    slotProps.data.relationships.user.data.type
                  ).attributes.email
                }}
              </span>
            </template>
          </Column>

          <!-- Attempts -->
          <Column
            field="attempts"
            :show-filter-menu="false"
            :hidden="!columnVisibility.attempts"
            class="w-[25rem] !text-center"
          >
            <template #header>
              <div class="text-start w-full">Attempted Status</div>
            </template>
            <template #filter>
              <SelectButton
                v-model="filters.attempted"
                class="w-[25rem]"
                :options="attemptsOptions"
                option-value="value"
                option-label="name"
              />
            </template>
            <template #body="slotProps">
              <i
                v-if="slotProps.data.attributes.attempts > 0"
                class="pi pi-check-circle !text-2xl text-green-500"
              ></i>
              <i v-else class="pi pi-times-circle !text-2xl text-yellow-500"></i>
            </template>
          </Column>

          <Column field="marks" :show-filter-menu="false" :hidden="!columnVisibility.marks">
            <template #header>
              <div class="flex justify-between items-center w-full">
                <p>Marks(%)</p>
                <SortComponent @direction-change="query.sort.marks = $event" />
              </div>
            </template>
            <template #body="slotProps">
              <Badge
                v-if="slotProps.data.relationships.evaluation.data"
                severity="info"
                class="mr-1"
              >
                {{
                  findRelations(
                    teamsQuestionnairesStore.users.included,
                    slotProps.data.relationships.evaluation.data.id,
                    slotProps.data.relationships.evaluation.data.type
                  ).attributes.marks_percentage
                }}
              </Badge>
              <Badge v-else severity="info" class="mr-1"> N/A </Badge>
            </template>
          </Column>

          <!--Show More Datails-->
          <Column field="showMore" header="More Details" :hidden="!columnVisibility.show_more">
            <template #body="slotProps">
              <router-link
                class="inlne-block mx-0 flex items-center justify-start hover:bg-transparent"
                :to="{
                  name: 'admin.users.questionnaires.index',
                  params: {
                    id: slotProps.data.attributes.user_id
                  },
                  query: {
                    uq_id: slotProps.data.attributes.user_questionnaire_id
                  }
                }"
              >
                <Avatar icon="pi pi-eye" />
              </router-link>
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
import { onMounted, reactive, ref, watch } from 'vue'
import Paginator from '@/components/PaginatorComponent.vue'

import { useTeamsQuestionnairesStore } from '@/stores/teams/questionnaires'
import { useAuthStore } from '@/stores/auth'

import { useRoute, useRouter } from 'vue-router'

import moment from 'moment/moment'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import PrimeButton from 'primevue/button'
import SelectButton from 'primevue/selectbutton'
import Badge from 'primevue/tag'
import MenuComponent from 'primevue/menu'
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'

import SortComponent from '@/components/SortComponent.vue'

import { ROLES } from '@/constants'
import { findRelations, lowercaseFirstLetter, snake } from '@/helpers'

export default {
  components: {
    AdminTableLayout,
    Avatar,
    PrimeButton,
    DataTable,
    Column,
    Badge,
    SelectButton,
    Paginator,
    SortComponent,
    InputText,
    MenuComponent,
    IconField,
    InputIcon
  },
  setup() {
    const teamsQuestionnairesStore = useTeamsQuestionnairesStore()
    const authStore = useAuthStore()

    const router = useRouter()
    const route = useRoute()

    const attemptsOptions = [
      { name: 'All', value: '' },
      { name: 'Attempted', value: 'true' },
      { name: 'Not Attempted', value: 'false' }
    ]

    const query = reactive({
      sort: {},
      pagination: { number: 1, size: 10 }
    })

    const actionsMenuRef = ref()
    const actions = ref([
      {
        label: 'Bulk Controllers',
        command: () => {}
      },
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
      user_id: true,
      name: true,
      email: true,
      marks: true,
      total_users: true,
      attempts: true,
      attempted_users: true,
      created_at: true,
      show_more: true
    })
    const columnsMenuRef = ref()
    const columns = ref([
      {
        label: 'Bulk Controllers',
        command: () => {}
      },
      {
        label: 'User ID',
        command: () => {
          columnVisibility.user_id = !columnVisibility.user_id
        }
      },
      {
        label: 'Name',
        command: () => {
          columnVisibility.name = !columnVisibility.name
        }
      },
      {
        label: 'Email',
        command: () => {
          columnVisibility.email = !columnVisibility.email
        }
      },
      {
        label: 'Marks',
        command: () => {
          columnVisibility.marks = !columnVisibility.marks
        }
      },
      {
        label: 'Total Users',
        command: () => {
          columnVisibility.total_users = !columnVisibility.total_users
        }
      },
      {
        label: 'Attempts',
        command: () => {
          columnVisibility.attempts = !columnVisibility.attempts
        }
      },
      {
        label: 'Attempted Users',
        command: () => {
          columnVisibility.attempted_users = !columnVisibility.attempted_users
        }
      },
      {
        label: 'Created at',
        command: () => {
          columnVisibility.created_at = !columnVisibility.created_at
        }
      },
      {
        label: 'Show More',
        command: () => {
          columnVisibility.show_more = !columnVisibility.show_more
        }
      }
    ])

    const filters = ref({})
    const showPaginator = ref(true)

    onMounted(() => {
      getAllUsersRequest({ pagination: { number: 1, size: 10 } })
    })

    watch(query, (newQuery) => {
      getAllUsersRequest({ ...newQuery, filters: filters.value })
    })

    // We need to reset show paginator if it is disabled
    watch(teamsQuestionnairesStore, (newUsersStore) => {
      if (!newUsersStore.loading) {
        showPaginator.value = true
      }
    })

    function getAllUsersRequest(query) {
      teamsQuestionnairesStore.getAllUsers(route.params.id, {
        query: {
          ...query,
          includes: ['evaluation', 'user']
        }
      })
    }

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function applyFilters() {
      query.pagination.number = 1
      showPaginator.value = false // Reset the pagination

      getAllUsersRequest({
        filters: filters.value,
        ...query
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

      getAllUsersRequest({ pagination: { number: 1, size: 10 } })
    }

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
    }

    function toggleActionsMenu(event) {
      actionsMenuRef.value.toggle(event)
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
      attemptsOptions,
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
      findRelations,
      hideAllColumns,
      displayAllColumns
    }
  }
}
</script>
