<template>
  <ConfirmDialog></ConfirmDialog>

  <AdminTableLayout>
    <template #table>
      <div>
        <DataTable
          :value="
            usersQuestionnairesStore.questionnaires && usersQuestionnairesStore.questionnaires.data
          "
          responsive-layout="scroll"
          :loading="usersQuestionnairesStore.loading"
          striped-rows
          data-key="id"
          filter-display="row"
        >
          <template #empty>
            <p
              v-if="!usersQuestionnairesStore.loading"
              class="p-4 text-center text-2xl bg-blue-200"
            >
              No records found.
            </p>
          </template>

          <template #header>
            <div class="flex justify-between items-center text-2xl uppercase">
              <div class="flex items-center">
                <div class="sm:hidden mr-4">
                  <i
                    v-tooltip.top="`Questionnaires of user ${route.params.id} (id)`"
                    class="pi pi-info-circle !text-4xl"
                  ></i>
                </div>
                <p class="mr-2 hidden sm:block">
                  Questionnaires of user
                  <span class="text-green-500">{{ route.params.id }}</span>
                  <span class="text-sm lowercase">(id)</span>
                </p>
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

          <!-- No -->
          <Column field="no" header="No">
            <template #body="slotProps"> {{ slotProps.index + 1 }}</template>
          </Column>

          <!-- Id -->
          <Column
            field="questionnaireId"
            header="Questionnaire Id"
            :show-filter-menu="false"
            :hidden="!columnVisibility.id"
          >
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

          <!-- Code -->
          <Column field="id" header="Code" :hidden="!columnVisibility.code">
            <template #body="slotProps">
              <div v-copy-to-clipboard="slotProps.data.id" class="mr-6">
                <Password
                  v-model="slotProps.data.attributes.code"
                  :feedback="false"
                  toggleMask
                  disabled
                  inputClass="min-w-[21rem] !border-none !bg-gray-100/60 dark:!bg-gray-800"
                  :inputId="slotProps.data.id"
                />
              </div>
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
              <div class="text-center w-full">Attempted Status</div>
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

          <!-- Started at -->
          <Column
            field="started_at"
            :hidden="!columnVisibility.started_at"
            :show-filter-menu="false"
          >
            <template #header>
              <div>
                <p>Started at</p>
              </div>
            </template>
            <template #body="slotProps">
              <Tag v-if="!slotProps.data.attributes.started_at" severity="info">N/A</Tag>

              <span v-else>
                {{
                  moment(slotProps.data.attributes.started_at).format('ddd, MMM D, yyyy, h:mm a')
                }}
              </span>
            </template>
          </Column>

          <!-- Finished at -->
          <Column
            field="finished_at"
            :hidden="!columnVisibility.finished_at"
            :show-filter-menu="false"
          >
            <template #header>
              <div>
                <p>Finished at</p>
              </div>
            </template>
            <template #body="slotProps">
              <Tag v-if="!slotProps.data.attributes.finished_at" severity="info">N/A</Tag>

              <span v-else>
                {{
                  moment(slotProps.data.attributes.finished_at).format('ddd, MMM D, yyyy, h:mm a')
                }}
              </span></template
            >
          </Column>

          <!-- Exires at -->
          <Column
            field="expires_at"
            :hidden="!columnVisibility.expires_at"
            :show-filter-menu="false"
          >
            <template #header>
              <div>
                <p>Expires at</p>
              </div>
            </template>
            <template #body="slotProps">
              {{
                moment(slotProps.data.attributes.expires_at).format('ddd, MMM D, yyyy, h:mm a')
              }}</template
            >
          </Column>

          <!-- Exired Status -->
          <Column
            field="expires_at"
            :hidden="!columnVisibility.expired_status"
            :show-filter-menu="false"
            class="w-[25rem] !text-center"
          >
            <template #header>
              <div class="text-center w-full">
                <p>Expired Status</p>
              </div>
            </template>
            <template #filter>
              <SelectButton
                v-model="filters.expired"
                class="w-[25rem]"
                :options="expiredOptions"
                option-value="value"
                option-label="name"
              />
            </template>
            <template #body="slotProps">
              <Tag
                v-if="moment(slotProps.data.attributes.expires_at).isBefore(moment())"
                severity="danger"
                >Expired</Tag
              >
              <Tag v-else severity="info">N/A</Tag>
            </template>
          </Column>

          <!-- Created at -->
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

          <!--Show Evaluation-->
          <Column field="evaluation" header="Evaluation" :hidden="!columnVisibility.evaluation">
            <template #body="slotProps">
              <router-link
                v-if="slotProps.data.attributes.attempts > 0"
                class="inlne-block mx-0 flex items-center justify-start hover:bg-transparent"
                :to="{
                  name: 'admin.evaluations.index',
                  query: {
                    uq_id: slotProps.data.attributes.user_questionnaire_id
                  }
                }"
              >
                <Avatar icon="pi pi-eye" />
              </router-link>
              <Tag v-else severity="info"> N/A </Tag>
            </template>
          </Column>

          <!-- Actions -->
          <Column field="Actions" header="Actions" :hidden="!columnVisibility.actions">
            <template #body="slotProps">
              <span class="p-buttonset space-x-1">
                <PrimeButton
                  v-if="
                    shouldAlloweToResendNotiificaton(
                      slotProps.data.attributes.attempts,
                      slotProps.data.attributes.expires_at
                    )
                  "
                  class="p-button-sm"
                  icon="pi pi-envelope"
                  title="Resend notification"
                  @click="resendNotification(slotProps.data.attributes.user_questionnaire_id)"
                />
                <PrimeButton
                  v-if="slotProps.data.attributes.attempts === 0"
                  class="p-button-sm"
                  icon="pi pi-trash "
                  title="Revoke Access"
                  severity="danger"
                  @click="revokeAccess(slotProps.data.attributes.user_questionnaire_id)"
                />
              </span>
            </template>
          </Column>

          <template #footer>
            <Paginator
              v-if="usersQuestionnairesStore.questionnaires && showPaginator"
              :rows="
                usersQuestionnairesStore.questionnaires &&
                usersQuestionnairesStore.questionnaires.meta.per_page
              "
              :total-records="
                usersQuestionnairesStore.questionnaires &&
                usersQuestionnairesStore.questionnaires.meta.total
              "
              @page="onPage"
            >
            </Paginator>
            <p
              class="hidden sm:flex p-2 relative bottom-[-20px] w-full justify-center lg:justify-end"
            >
              {{
                usersQuestionnairesStore.questionnaires
                  ? usersQuestionnairesStore.questionnaires.meta.total
                  : 0
              }}
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

import { useUsersQuestionnairesStore } from '@/stores/users/questionnaires'

import { useRouter, useRoute } from 'vue-router'

import moment from 'moment/moment'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import PrimeButton from 'primevue/button'
import MenuComponent from 'primevue/menu'
import ConfirmDialog from 'primevue/confirmdialog'
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import Password from 'primevue/password'
import SelectButton from 'primevue/selectbutton'
import Tag from 'primevue/tag'
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
    Paginator,
    SortComponent,
    Tag,
    InputText,
    IconField,
    InputIcon,
    Password,
    MenuComponent,
    ConfirmDialog,

    SelectButton
  },
  setup() {
    const confirm = useConfirm()

    const usersQuestionnairesStore = useUsersQuestionnairesStore()

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
      },
      {
        label: 'Add User',
        icon: 'pi pi-user-plus',
        command: () => router.push({ name: 'admin.questionnaires.create' })
      }
    ])

    const columnVisibility = reactive({
      id: true,
      code: true,
      attempts: true,
      started_at: true,
      finished_at: true,
      expires_at: true,
      expired_status: true,
      evaluation: true,
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
        label: 'Code',
        command: () => {
          columnVisibility.code = !columnVisibility.code
        }
      },
      {
        label: 'Attempts',
        command: () => {
          columnVisibility.attempts = !columnVisibility.attempts
        }
      },

      {
        label: 'Started at',
        command: () => {
          columnVisibility.started_at = !columnVisibility.started_at
        }
      },
      {
        label: 'Finished at',
        command: () => {
          columnVisibility.finished_at = !columnVisibility.finished_at
        }
      },
      {
        label: 'Expires at',
        command: () => {
          columnVisibility.expires_at = !columnVisibility.expires_at
        }
      },
      {
        label: 'Expired status',
        command: () => {
          columnVisibility.expired_status = !columnVisibility.expired_status
        }
      },
      {
        label: 'Evaluation',
        command: () => {
          columnVisibility.evaluation = !columnVisibility.evaluation
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

    const attemptsOptions = [
      { name: 'All', value: '' },
      { name: 'Attempted', value: 'true' },
      { name: 'Not Attempted', value: 'false' }
    ]

    const expiredOptions = [
      { name: 'All', value: '' },
      { name: 'Expired', value: 'true' },
      { name: 'Not Expired', value: 'false' }
    ]

    onMounted(() => {
      if (route.query.uq_id) {
        filters.value.uq_id = route.query.uq_id
      }
      getAll()
    })

    watch(query, (newQuery) => {
      usersQuestionnairesStore.getAll(route.params.id, {
        query: { ...newQuery, filters: filters.value }
      })
    })

    // We need to reset show paginator if it is disabled
    watch(usersQuestionnairesStore, (newUsersStore) => {
      if (!newUsersStore.loading) {
        showPaginator.value = true
      }

      if (newUsersStore.status === 'deleted') {
        usersQuestionnairesStore.getAll({
          query: { ...query, filters: filters.value }
        })
      }
    })

    function getAll() {
      usersQuestionnairesStore.getAll(route.params.id, {
        query: { filters: filters.value, pagination: { number: 1, size: 10 } }
      })
    }

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function applyFilters() {
      query.pagination.number = 1
      showPaginator.value = false // Reset the pagination

      usersQuestionnairesStore.getAll(route.params.id, {
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

      usersQuestionnairesStore.getAll(route.params.id, {
        query: { pagination: { number: 1, size: 10 } }
      })
    }

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
    }

    function toggleActionsMenu(event) {
      actionsMenuRef.value.toggle(event)
    }

    function shouldAlloweToResendNotiificaton(attempts, expiresAt) {
      const expired = moment(expiresAt).isBefore(moment())

      if (attempts === 0 && !expired) {
        return true
      }

      return false
    }

    function resendNotification(questionnaireId) {
      confirm.require({
        message: 'Do you want to re-send the notification',
        header: 'Resend Notification',
        icon: 'pi pi-info-circle',
        acceptClass: 'p-button-info',
        acceptLabel: 'Yes send',
        accept: () => {
          usersQuestionnairesStore.resendNotificatiion(route.params.id, questionnaireId)
        },
        reject: () => {}
      })
    }

    function revokeAccess(userQuestionnaireId) {
      confirm.require({
        message: 'Do you want to revoke the access to the questionnaire for the user ?',
        header: 'Revoke Access',
        icon: 'pi pi-info-circle',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Yes',
        accept: async () => {
          await usersQuestionnairesStore.revokeAccess(route.params.id, userQuestionnaireId)

          getAll()
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
      usersQuestionnairesStore,
      onPage,
      moment,
      query,
      filters,
      applyFilters,
      attemptsOptions,
      expiredOptions,
      showPaginator,
      reset,
      ROLES,
      columns,
      columnsMenuRef,
      toggleColumnsMenu,
      columnVisibility,
      lowercaseFirstLetter,
      snake,
      actionsMenuRef,
      actions,
      toggleActionsMenu,
      route,
      router,
      shouldAlloweToResendNotiificaton,
      resendNotification,
      revokeAccess,
      hideAllColumns,
      displayAllColumns
    }
  }
}
</script>
