<template>
  <ConfirmDialog></ConfirmDialog>

  <AdminTableLayout>
    <template #table>
      <div>
        <DataTable
          :value="teamsStore.teams && teamsStore.teams.data"
          responsive-layout="scroll"
          :loading="teamsStore.loading"
          striped-rows
          data-key="id"
          filter-display="row"
        >
          <template #empty>
            <p v-if="!teamsStore.loading" class="p-4 text-center text-2xl bg-gray-800 text-white">
              No records found.
            </p>
          </template>

          <template #header>
            <div class="flex justify-between items-center text-2xl uppercase">
              <div class="flex">
                <p class="mr-2">Teams</p>
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
          <Column field="id" header="Id" :hidden="!columnVisibility.id">
            <template #body="slotProps"> {{ slotProps.data.id }}</template>
          </Column>
          <Column field="name" :show-filter-menu="false" :hidden="!columnVisibility.name">
            <template #header>
              <div class="flex justify-between items-center w-full">
                <p>Name</p>
                <SortComponent @direction-change="query.sort.name = $event" />
              </div>
            </template>
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
            <template #body="slotProps"> {{ slotProps.data.attributes.name }}</template>
          </Column>

          <Column field="created_at" header="Created at" :hidden="!columnVisibility.created_at">
            <template #body="slotProps">
              {{
                moment(slotProps.data.attributes.created_at).format('ddd, MMM D, yyyy, h:mm a')
              }}</template
            >
          </Column>

          <!--Show Users-->

          <Column field="users" header="Users" :hidden="!columnVisibility.users">
            <template #body="slotProps">
              <router-link
                class="inlne-block flex items-center justify-start hover:bg-transparent"
                :to="{
                  name: 'admin.teams.users.index',
                  params: { id: slotProps.data.id }
                }"
              >
                <Avatar icon="pi pi-eye" />
              </router-link>
            </template>
          </Column>
        </DataTable>
      </div>
    </template>
  </AdminTableLayout>
</template>

<script>
import { onMounted, reactive, ref, watch } from 'vue'

import { useUsersTeamsStore } from '@/stores/users/teams'

import { useRoute, useRouter } from 'vue-router'

import moment from 'moment/moment'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import PrimeButton from 'primevue/button'
import MenuComponent from 'primevue/menu'
import InputText from 'primevue/inputtext'
import ConfirmDialog from 'primevue/confirmdialog'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'

import SortComponent from '@/components/SortComponent.vue'

import { lowercaseFirstLetter, snake } from '@/helpers'

export default {
  components: {
    AdminTableLayout,
    Avatar,
    PrimeButton,
    DataTable,
    Column,
    SortComponent,
    InputText,
    MenuComponent,
    ConfirmDialog,
    IconField,
    InputIcon
  },
  setup() {
    const teamsStore = useUsersTeamsStore()

    const router = useRouter()
    const route = useRoute()

    const query = reactive({
      sort: {}
    })

    const columnsMenuRef = ref()
    const columnVisibility = reactive({
      id: true,
      name: true,
      users: true,
      created_at: true,
      actions: true
    })
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
        label: 'Created at',
        command: () => {
          columnVisibility.created_at = !columnVisibility.created_at
        }
      },
      {
        label: 'Users',
        command: () => {
          columnVisibility.users = !columnVisibility.users
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

    onMounted(() => {
      getAllRequest(false)
    })

    watch(query, () => {
      getAllRequest()
    })

    function getAllRequest(withQuery = true) {
      if (withQuery) {
        teamsStore.getAll(route.params.id, {
          query: { ...query, filters: filters.value }
        })
      } else {
        teamsStore.getAll(route.params.id)
      }
    }

    function applyFilters() {
      getAllRequest()
    }

    function reset() {
      //Reset filters
      filters.value = {}

      //Reset sort
      query.sort = {}

      getAllRequest(false)
    }

    function toggleActionsMenu(event) {
      actionsMenuRef.value.toggle(event)
    }

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
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
      teamsStore,
      moment,
      query,
      filters,
      applyFilters,
      reset,
      columns,
      columnsMenuRef,
      toggleColumnsMenu,
      columnVisibility,
      lowercaseFirstLetter,
      snake,
      router,
      actions,
      actionsMenuRef,
      toggleActionsMenu,
      hideAllColumns,
      displayAllColumns
    }
  }
}
</script>
