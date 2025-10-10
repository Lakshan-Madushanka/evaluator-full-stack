<template>
  <ConfirmDialog></ConfirmDialog>

  <BulkDeleteComponent
    :display-component="displayBulkDeleteComponent"
    :value="bulkDeleteValue"
    :status="usersStore.status"
    @on-dialog-hidden="displayBulkDeleteComponent = $event"
    @bulk-delete-confirmed="onBulkDeleteConfirmed"
  />
  <AdminTableLayout>
    <template #table>
      <div>
        <DataTable
          v-model:selection="selectedUsers"
          :value="usersStore.users && usersStore.users.data"
          responsive-layout="scroll"
          :loading="usersStore.loading"
          striped-rows
          data-key="id"
          filter-display="row"
        >
          <Column selection-mode="multiple">
            <template #header>
              <i
                v-if="showBulkActions"
                class="pi pi-ellipsis-v hover:cursor-pointer"
                @click="toggleBulkActions"
              >
                <span class="font-bold">{{ selectedUsers.length }}</span>
                records selected
              </i>
              <MenuComponent
                id="overlay_menu"
                ref="bulkActionMenu"
                :model="bulkActions"
                :popup="true"
              />
            </template>
          </Column>

          <template #empty>
            <p v-if="!usersStore.loading" class="p-4 text-center text-2xl bg-blue-200">
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
                    <div class="flex items-center p-2 hover:cursor-pointer">
                      <i
                        :class="
                          columnVisibility[snake(lowercaseFirstLetter(slotProps['item']['label']))]
                            ? 'pi pi-eye'
                            : 'pi pi-eye-slash'
                        "
                        class="mr-2"
                      ></i>
                      <p>{{ slotProps.item.label }}</p>
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
            <template #body="slotProps"> {{ slotProps.data.attributes.name }}</template>
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
            <template #body="slotProps"> {{ slotProps.data.attributes.email }}</template>
          </Column>
          <Column
            v-if="authStore.user.role === 'SUPER_ADMIN'"
            field="role"
            :show-filter-menu="false"
            :hidden="!columnVisibility.role"
          >
            <template #filter
              ><Dropdown v-model="filters.role" :options="roles" option-label="name" />
            </template>
            <template #header>
              <div class="flex justify-between w-full items-center">
                <p>Role</p>
                <SortComponent @direction-change="query.sort.role = $event" />
              </div>
            </template>
            <template #body="slotProps">
              <Tag severity="info">
                {{ ROLES[slotProps.data.attributes.role]['name'] }}
              </Tag>
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

          <Column
            field="questionnaires"
            header="Questionnaires"
            :hidden="!columnVisibility.questionnaires"
          >
            <template #body="slotProps">
              <router-link
                class="inlne-block flex items-center justify-center hover:bg-transparent"
                :to="{
                  name: 'admin.users.questionnaires.index',
                  params: { id: slotProps.data.id }
                }"
              >
                <Avatar icon="pi pi-eye" />
              </router-link>
            </template>
          </Column>

          <Column
            v-if="authStore.user.role === 'SUPER_ADMIN'"
            field="Actions"
            header="Actions"
            :hidden="!columnVisibility.actions"
          >
            <template #body="slotProps">
              <span class="p-buttonset space-x-1">
                <PrimeButton
                  class="p-button-danger p-button-sm"
                  icon="pi pi-trash"
                  title="Remove User"
                  @click="removeUser(slotProps.data.id)"
                />
              </span>
            </template>
          </Column>

          <template #footer>
            <Paginator
              v-if="usersStore.users && showPaginator"
              :rows="usersStore.users && usersStore.users.meta.per_page"
              :total-records="usersStore.users && usersStore.users.meta.total"
              @page="onPage"
            >
            </Paginator>
            <p
              class="hidden sm:flex p-2 relative bottom-[-20px] w-full justify-center lg:justify-end"
            >
              {{ usersStore.users ? usersStore.users.meta.total : 0 }} records found.
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

import { useTeamsUsersStore } from '@/stores/teams/users'
import { useAuthStore } from '@/stores/auth'

import { useRouter, useRoute } from 'vue-router'

import moment from 'moment/moment'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import PrimeButton from 'primevue/button'
import Tag from 'primevue/tag'
import MenuComponent from 'primevue/menu'
import InputText from 'primevue/inputtext'
import Dropdown from 'primevue/dropdown'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'

import BulkDeleteComponent from '@/components/BulkDeleteComponent.vue'
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
    Tag,
    Paginator,
    SortComponent,
    InputText,
    Dropdown,
    MenuComponent,
    ConfirmDialog,
    BulkDeleteComponent,
    IconField,
    InputIcon
  },
  setup() {
    const confirm = useConfirm()
    const toast = useToast()

    const usersStore = useTeamsUsersStore()
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
      email: true,
      role: true,
      created_at: true,
      questionnaires: true,
      actions: true
    })
    const columnsMenuRef = ref()
    const columns = ref([
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
        label: 'Email',
        command: () => {
          columnVisibility.email = !columnVisibility.email
        }
      },
      {
        label: 'Role',
        command: () => {
          columnVisibility.role = !columnVisibility.role
        }
      },
      {
        label: 'Created at',
        command: () => {
          columnVisibility.created_at = !columnVisibility.created_at
        }
      },
      {
        label: 'Questionnaires',
        command: () => {
          columnVisibility.questionnaires = !columnVisibility.questionnaires
        }
      },
      {
        label: 'Actions',
        command: () => {
          columnVisibility.actions = !columnVisibility.actions
        },
        visible: authStore.user.role === 'SUPER_ADMIN'
      }
    ])

    const showBulkActions = ref(false)
    const bulkActionMenu = ref()
    const displayBulkDeleteComponent = ref(false)
    const bulkDeleteValue = ref('')

    const selectedUsers = ref()
    const filters = ref({ role: { name: 'All', value: '' } })
    const showPaginator = ref(true)

    const roles = [
      { name: 'All', value: '' },
      { name: 'Super Admin', value: 'SUPER_ADMIN' },
      { name: 'Admin', value: 'ADMIN' },
      { name: 'Regular', value: 'REGULAR' }
    ]

    const bulkActions = [
      {
        label: 'Remove selected users',
        icon: 'pi pi-trash',
        command: () => {
          let value = ''
          if (selectedUsers.value.length === 1) {
            if (selectedUsers.value[0]['attributes']['role'] === 'SUPER_ADMIN') {
              toast.add({
                severity: 'warn',
                summary: 'Super Admin cannot be deleted !',
                life: 5000
              })
              return
            }
            value = selectedUsers.value[0]['attributes']['email']
          } else {
            value = selectedUsers.value.length + ' ' + 'records'
          }

          bulkDeleteValue.value = value
          displayBulkDeleteComponent.value = true
        }
      }
    ]

    onMounted(() => {
      usersStore.getAll(route.params.id, {
        query: { pagination: { number: 1, size: 10 } }
      })
    })

    watch(query, (newQuery) => {
      usersStore.getAll(route.params.id, {
        query: { ...newQuery, filters: filters.value }
      })
    })

    // We need to reset show paginator if it is disabled
    watch(usersStore, (newUsersStore) => {
      if (!newUsersStore.loading) {
        showPaginator.value = true
      }

      if (newUsersStore.status === 'deleted') {
        displayBulkDeleteComponent.value = false
        selectedUsers.value = []
        usersStore.getAll(route.params.id, {
          query: { ...query, filters: filters.value }
        })
      }
    })

    watch(selectedUsers, (newSelectedUsers) => {
      if (newSelectedUsers && newSelectedUsers.length > 0) {
        showBulkActions.value = true
        return
      }
      showBulkActions.value = false
    })

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function applyFilters() {
      query.pagination.number = 1
      showPaginator.value = false // Reset the pagination

      usersStore.getAll(route.params.id, {
        query: { filters: filters.value, ...query }
      })
    }

    function reset() {
      // Reser selected users
      selectedUsers.value = []

      //Reset pagnator
      showPaginator.value = false
      query.pagination = { number: 1, size: 10 }

      //Reset filters
      filters.value = { role: { name: 'All', value: '' } }

      //Reset sort
      query.sort = {}

      usersStore.getAll(route.params.id, {
        query: { pagination: { number: 1, size: 10 } }
      })
    }

    function toggleBulkActions(event) {
      bulkActionMenu.value.toggle(event)
    }

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
    }

    function toggleActionsMenu(event) {
      actionsMenuRef.value.toggle(event)
    }

    function onBulkDeleteConfirmed() {
      getSelectedUsersIds()
      usersStore.detachUsers(route.params.id, getSelectedUsersIds())
    }

    function getSelectedUsersIds() {
      let ids = []

      selectedUsers.value.forEach((user) => {
        ids.push(user.id)
      })

      return ids
    }

    function removeUsers(userIds) {
      usersStore.detachUsers(route.params.id, userIds)
    }

    function removeUser(userId) {
      confirm.require({
        message: 'Do you want to remove the selected user from the team ?',
        header: 'Remove Confirmation',
        icon: 'pi pi-info-circle',
        iconClass: 'bg-red-500',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Yes Remove',
        accept: () => {
          removeUsers([userId])
        },
        reject: () => {}
      })
    }

    return {
      usersStore,
      onPage,
      moment,
      query,
      filters,
      applyFilters,
      roles,
      showPaginator,
      reset,
      ROLES,
      selectedUsers,
      showBulkActions,
      bulkActionMenu,
      bulkActions,
      toggleBulkActions,
      displayBulkDeleteComponent,
      bulkDeleteValue,
      onBulkDeleteConfirmed,
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
      removeUsers,
      removeUser
    }
  }
}
</script>
