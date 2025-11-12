<template>
  <ConfirmDialog></ConfirmDialog>
  <AttachQuestionnaireViewComponent
    type="user"
    :display="displayAssignQuestionnaireView"
    :attachable-id="userIdToAttachQuestionnaire"
    @hide="displayAssignQuestionnaireView = $event"
  />

  <DialogModal
    v-model:visible="displayAttachTeamsDialog"
    modal
    header="Attach Team(s)"
    :style="{ width: '50vw' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
  >
    <div>
      <p class="text-lg ld mb-4">Select Team(s)</p>
      <MultiSelect
        v-model="selectedTeams"
        :options="availableTeams"
        :loading="teamsStore.loading"
        name="team"
        optionLabel="name"
        optionValue="id"
        filter
        placeholder="Select Team(s)"
        class="w-full md:w-80"
      />
    </div>

    <div class="flex justify-end gap-2">
      <PrimeButton
        type="button"
        label="Cancel"
        severity="info"
        @click="displayAttachTeamsDialog = false"
      ></PrimeButton>
      <PrimeButton
        type="button"
        label="Attach"
        :disabled="selectedTeams.length === 0"
        :loading="usersTeamsStore.status === 'attaching'"
        @click="onConfirmTeamAttach"
      ></PrimeButton>
    </div>
  </DialogModal>

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
            <p v-if="!usersStore.loading" class="p-4 text-center text-2xl bg-gray-800 text-white">
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
                  <PrimeButton
                    v-if="authStore.user.role === 'SUPER_ADMIN'"
                    icon="pi pi-user-plus"
                    label="New User"
                    class="!py-1 !mr-4"
                    icon-pos="right"
                    @click="() => router.push({ name: 'admin.users.create' })"
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
              ><Select v-model="filters.role" :options="roles" option-label="name" />
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

          <Column field="teams" header="Teams" :hidden="!columnVisibility.teams">
            <template #body="slotProps">
              <router-link
                class="inlne-block flex items-center justify-center hover:bg-transparent"
                :to="{
                  name: 'admin.users.teams.index',
                  params: { id: slotProps.data.id }
                }"
              >
                <Avatar icon="pi pi-eye" />
              </router-link>
            </template>
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
                  class="p-button-sm"
                  icon="pi pi-user-plus "
                  title="Attach Team(s)"
                  @click="attachTeams(slotProps.data.id)"
                />
                <PrimeButton
                  class="p-button-sm"
                  icon="pi pi-calendar "
                  title="Assign Questionnaire"
                  @click="attachQuestionnaire(slotProps.data.id)"
                />
                <span v-if="slotProps.data.attributes.role !== 'SUPER_ADMIN'" class="space-x-1">
                  <PrimeButton
                    class="p-button-sm"
                    icon="pi pi-file-edit"
                    title="Edit"
                    @click="
                      () =>
                        router.push({
                          name: 'admin.users.edit',
                          params: { id: slotProps.data.id }
                        })
                    "
                  />
                  <PrimeButton
                    class="p-button-danger p-button-sm"
                    icon="pi pi-trash "
                    title="Delete"
                    @click="deleteUser(slotProps.data.id)"
                  />
                </span>
                <span v-else class="p-buttonset space-x-1">
                  <PrimeButton
                    v-tooltip.left="'Use profile section to edit super admin.'"
                    class="p-button-sm opacity-60"
                    icon="pi pi-info-circle"
                  />
                  <PrimeButton
                    v-tooltip.left="'Super admin cannot be deleted !'"
                    class="p-button-sm p-button-danger opacity-60"
                    icon="pi pi-info-circle"
                  />
                </span>
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
import { onMounted, reactive, ref, watch } from 'vue'
import Paginator from '@/components/PaginatorComponent.vue'

import { useAuthStore } from '@/stores/auth'
import { useUsersStore } from '@/stores/users'
import { useTeamsStore } from '@/stores/teams'
import { useUsersTeamsStore } from '@/stores/users/teams'

import { useRouter } from 'vue-router'

import moment from 'moment/moment'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import DialogModal from 'primevue/dialog'
import PrimeButton from 'primevue/button'
import Tag from 'primevue/tag'
import MenuComponent from 'primevue/menu'
import MultiSelect from 'primevue/multiselect'
import InputText from 'primevue/inputtext'
import Select from 'primevue/select'
import ConfirmDialog from 'primevue/confirmdialog'
import { useConfirm } from 'primevue/useconfirm'
import { useToast } from 'primevue/usetoast'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'

import BulkDeleteComponent from '@/components/BulkDeleteComponent.vue'
import SortComponent from '@/components/SortComponent.vue'
import AttachQuestionnaireViewComponent from '@/components/questionnaires/AttachView.vue'

import { ROLES } from '@/constants'
import { lowercaseFirstLetter, snake } from '@/helpers'

export default {
  components: {
    Avatar,
    AdminTableLayout,
    PrimeButton,
    DataTable,
    DialogModal,
    Column,
    Tag,
    Paginator,
    SortComponent,
    InputText,
    Select,
    MenuComponent,
    MultiSelect,
    ConfirmDialog,
    BulkDeleteComponent,
    AttachQuestionnaireViewComponent,
    IconField,
    InputIcon
  },
  setup() {
    onMounted(() => {
      usersStore.getAll({
        query: { pagination: { number: 1, size: 10 } }
      })
    })

    const confirm = useConfirm()
    const toast = useToast()

    const usersStore = useUsersStore()
    const authStore = useAuthStore()
    const teamsStore = useTeamsStore()
    const usersTeamsStore = useUsersTeamsStore()

    const router = useRouter()

    const displayAttachTeamsDialog = ref(false)
    let selectedUserIdToAttachTeams = ''
    const availableTeams = ref([])
    const selectedTeams = ref([])

    const displayAssignQuestionnaireView = ref(false)
    const userIdToAttachQuestionnaire = ref()

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
      },
      {
        label: 'Add User',
        icon: 'pi pi-user-plus',
        command: () => router.push({ name: 'admin.users.create' }),
        visible: authStore.user.role === 'SUPER_ADMIN'
      }
    ])

    const columnVisibility = reactive({
      id: true,
      name: true,
      email: true,
      role: true,
      created_at: true,
      teams: true,
      questionnaires: true,
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
        label: 'Teams',
        command: () => {
          columnVisibility.teams = !columnVisibility.teams
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
        label: 'Delete selected',
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

    watch(query, (newQuery) => {
      usersStore.getAll({ query: { ...newQuery, filters: filters.value } })
    })

    // We need to reset show paginator if it is disabled
    watch(usersStore, (newUsersStore) => {
      if (!newUsersStore.loading) {
        showPaginator.value = true
      }

      if (newUsersStore.status === 'deleted') {
        displayBulkDeleteComponent.value = false
        selectedUsers.value = []
        usersStore.getAll({ query: { ...query, filters: filters.value } })
      }
    })

    watch(selectedUsers, (newSelectedUsers) => {
      if (newSelectedUsers && newSelectedUsers.length > 0) {
        showBulkActions.value = true
        return
      }
      showBulkActions.value = false
    })

    watch(
      () => usersTeamsStore.status,
      (status) => {
        console.log('status', status)

        if (status === 'attached') {
          displayAttachTeamsDialog.value = false
        }
      }
    )

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function applyFilters() {
      query.pagination.number = 1
      showPaginator.value = false // Reset the pagination

      usersStore.getAll({ query: { filters: filters.value, ...query } })
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

      usersStore.getAll({
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

    function deleteUser(id) {
      confirm.require({
        message: 'Do you want to delete this record? [This action cannot be undone !]',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        iconClass: 'bg-red-500',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Yes Delete',
        accept: () => {
          usersStore.deleteUser(id)
        },
        reject: () => {}
      })
    }

    function onBulkDeleteConfirmed() {
      getSelectedUsersIds()
      usersStore.bulkDeleteUsers({ ids: getSelectedUsersIds() })
    }

    function getSelectedUsersIds() {
      let ids = []

      selectedUsers.value.forEach((element) => {
        if (element.attributes.role !== 'SUPER_ADMIN') {
          ids.push(element.id)
        }
      })

      return ids
    }

    function attachQuestionnaire(userId) {
      displayAssignQuestionnaireView.value = true
      userIdToAttachQuestionnaire.value = userId
    }

    async function attachTeams(userId) {
      selectedUserIdToAttachTeams = userId

      selectedTeams.value = []

      displayAttachTeamsDialog.value = true

      await teamsStore.getAll()

      const tmpAvailableTeams = []

      teamsStore.teams.data?.forEach((team) => {
        tmpAvailableTeams.push({
          name: team.attributes.name,
          id: team.id
        })
      })

      availableTeams.value = tmpAvailableTeams
    }

    function onConfirmTeamAttach() {
      confirm.require({
        message: 'Do you want to attach selected teams(s) to the user',
        header: 'Attach Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
          label: 'Cancel',
          severity: 'secondary',
          outlined: true
        },
        acceptProps: {
          label: 'Yes Attach'
        },
        accept: () => {
          usersTeamsStore.attachTeams(selectedUserIdToAttachTeams, selectedTeams.value)
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
      usersStore,
      teamsStore,
      usersTeamsStore,
      displayAttachTeamsDialog,
      availableTeams,
      selectedTeams,
      displayAssignQuestionnaireView,
      userIdToAttachQuestionnaire,
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
      deleteUser,
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
      attachQuestionnaire,
      attachTeams,
      onConfirmTeamAttach,
      displayAllColumns,
      hideAllColumns
    }
  }
}
</script>
