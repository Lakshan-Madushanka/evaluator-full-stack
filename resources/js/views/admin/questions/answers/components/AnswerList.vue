<template>
  <AdminTableLayout>
    <template #table>
      <DataTable
        v-model:selection="selectedAnswers"
        :value="answersStore.answers && answersStore.answers.data"
        responsive-layout="scroll"
        :loading="answersStore.loading"
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
              <span class="font-bold">{{ selectedAnswers.length }}</span>
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

        <template #header>
          <div class="flex justify-between items-center text-2xl uppercase">
            <div class="flex">
              <p class="mr-2">Answers</p>
              <i
                class="pi pi-eye text-blue-600 hover:cursor-pointer"
                style="font-size: 2rem"
                @click="toggleColumnsMenu"
              ></i>
              <MenuComponent ref="columnsMenuRef" :model="columns" :popup="true">
                <template #item="slotProps">
                  <div class="flex items-center p-2 hover:cursor-pointer w-full">
                    <div
                      v-if="slotProps['item']['label'] === 'Bulk Controllers'"
                      class="flex justify-between w-full text-sm text-blue-400"
                    >
                      <span class="hover:text-blue-800" @click="displayAllColumns">Dsplay All</span>
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
                <PrimeButton
                  icon="pi pi-plus"
                  label="New Answer"
                  class="!py-1 !mr-4"
                  icon-pos="right"
                  @click="() => router.push({ name: 'admin.answers.create' })"
                />
              </div>
            </div>
          </div>
        </template>

        <!-- No -->
        <Column field="no" header="No">
          <template #body="slotProps"> {{ slotProps.index + 1 }}</template>
        </Column>

        <!-- id -->
        <Column field="id" header="Id" :hidden="!columnVisibility.id" :show-filter-menu="false">
          <template #body="slotProps">
            <div class="relative">
              <div :id="slotProps.data.attributes.pretty_id" class="mr-6">
                {{ slotProps.data.attributes.pretty_id }}
              </div>
              <div
                v-copy-to-clipboard="slotProps.data.attributes.pretty_id"
                class="absolute bottom-[20px] right-[74px]"
              />
            </div>
          </template>
          <template #filter>
            <span>
              <IconField>
                <InputIcon class="pi pi-search" />
                <InputText
                  v-model="filters.pretty_id"
                  type="text"
                  placeholder="Search"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </span>
          </template>
        </Column>

        <!-- Content -->
        <Column
          field="content"
          header="Content"
          :show-filter-menu="false"
          :hidden="!columnVisibility.content"
        >
          <template #filter>
            <span>
              <IconField class="!w-full">
                <InputIcon class="pi pi-search" />
                <InputText
                  v-model="filters.text"
                  class="!w-full"
                  type="text"
                  placeholder="Search"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </span>
          </template>
          <template #body="slotProps">
            <div class="flex justify-between items-center relative">
              <p v-html="formatText(slotProps.data.attributes.text, 100)"></p>
              <div class="group">
                <i class="pi pi-eye !block hover:text-blue-600 hover:cursor-pointer !text-xl">
                  <p
                    class="p-4 bg-black text-white hidden group-hover:block absolute left-0 top-[-1rem] whitespace-normal z-10"
                    v-html="slotProps.data.attributes.text"
                  ></p>
                </i>
              </div>
            </div>
          </template>
        </Column>

        <!-- No of Assigned Images -->
        <Column
          field="no_of_images"
          header="No of Images"
          :show-filter-menu="false"
          :hidden="!columnVisibility.no_of_images"
          body-class="!text-center"
        >
          <template #body="slotProps">
            <Tag severity="info">{{ slotProps.data.attributes.images_count }}</Tag>
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

        <!-- Actions -->
        <Column field="Actions" header="Actions" :hidden="!columnVisibility.actions">
          <template #body="slotProps">
            <span class="p-buttonset space-x-1">
              <PrimeButton
                class="p-button-primary p-button-sm"
                icon="pi pi-plus"
                title="Add"
                @click="add(slotProps.data)"
              />
            </span>
          </template>
        </Column>

        <template #empty>
          <p v-if="!answersStore.loading" class="p-4 text-center text-2xl bg-gray-800 text-white">
            No records found.
          </p>
        </template>

        <template #footer>
          <Paginator
            v-if="answersStore.answers && showPaginator"
            :rows="answersStore.answers && answersStore.answers.meta.per_page"
            :total-records="answersStore.answers && answersStore.answers.meta.total"
            @page="onPage"
          >
          </Paginator>
          <p
            class="hidden sm:flex p-2 relative bottom-[-20px] w-full justify-center lg:justify-end"
          >
            {{ answersStore.answers ? answersStore.answers.meta.total : 0 }}
            records found.
          </p>
        </template>
      </DataTable>
    </template>
  </AdminTableLayout>
</template>

<script>
import { onMounted, reactive, ref, watch } from 'vue'

import { useRouter } from 'vue-router'

import { useAnswersStore } from '@/stores/answers'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import MenuComponent from 'primevue/menu'
import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'
import Tag from 'primevue/tag'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'

import moment from 'moment/moment'

import SortComponent from '@/components/SortComponent.vue'
import Paginator from '@/components/PaginatorComponent.vue'

import { findRelations, formatText, lowercaseFirstLetter, snake } from '@/helpers'

export default {
  components: {
    AdminTableLayout,
    Column,
    DataTable,
    InputText,
    MenuComponent,
    Paginator,
    PrimeButton,
    SortComponent,
    Tag,
    IconField,
    InputIcon
  },
  props: {
    questionnaireId: { type: String, required: true },
    refresh: { type: Boolean, default: false }
  },
  emits: ['selection-change', 'add'],
  setup(props, { emit }) {
    const router = useRouter()

    const answersStore = useAnswersStore()

    const showPaginator = ref(true)
    const resetButtonClicked = ref(false)

    const selectedAnswers = ref()

    // Bulk Actions
    const showBulkActions = ref(false)
    const bulkActionMenu = ref()
    const displayBulkDeleteComponent = ref(false)
    const bulkDeleteValue = ref('')
    const bulkActions = [
      {
        label: 'Sync Questions',
        icon: 'pi pi-sync',
        command: () => {
          let value = ''
          if (selectedAnswers.value.length === 1) {
            value = selectedAnswers.value[0]['attributes']['pretty_id']
          } else {
            value = selectedAnswers.value.length + ' ' + 'records'
          }

          bulkDeleteValue.value = value
          displayBulkDeleteComponent.value = true

          emit('selection-change', selectedAnswers.value)
        }
      }
    ]

    // Query
    const filters = ref({})
    const initialQuery = { sort: {}, pagination: { number: 1, size: 10 } }
    const query = reactive(JSON.parse(JSON.stringify(initialQuery)))
    const includes = []

    // Column visibility
    const columnsMenuRef = ref()
    const columnVisibility = reactive({
      id: true,
      content: true,
      no_of_images: true,
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
        label: 'Content',
        command: () => {
          columnVisibility.content = !columnVisibility.content
        }
      },

      {
        label: 'No of Images',
        command: () => {
          columnVisibility.no_of_images = !columnVisibility.no_of_images
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

    // Actions (display for mobile devices)
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
        label: 'New Answer',
        icon: 'pi pi-plus',
        command: () => router.push({ name: 'admin.answers.create' })
      }
    ])

    //------------------------------------------------------------------------------------------------------------------------------------------------

    onMounted(() => {
      initQuery()
    })

    watch(
      () => props.refresh,
      (shouldRefresh) => {
        if (shouldRefresh) {
          reset()
        }
      }
    )

    watch(answersStore, (newAnswersStore) => {
      if (!newAnswersStore.loading) {
        showPaginator.value = true
        resetButtonClicked.value = false
      }
      if (newAnswersStore.status === 'deleted') {
        displayBulkDeleteComponent.value = false
        selectedAnswers.value = []
        newAnswersStore.getAll({
          query: { ...query, filters: filters.value, includes }
        })
      }
    })

    watch(selectedAnswers, (newSelectedAnswers) => {
      if (newSelectedAnswers && newSelectedAnswers.length > 0) {
        showBulkActions.value = true
        return
      }
      showBulkActions.value = false
    })

    watch(query, (newQuery) => {
      if (resetButtonClicked.value) {
        return
      }
      answersStore.getAll({
        query: { ...newQuery, filters: filters.value, includes }
      })
    })

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
    }

    function applyFilters() {
      query.pagination.number = 1
      showPaginator.value = false

      answersStore.getAll({
        query: { filters: filters.value, ...query, includes }
      })
    }

    function initQuery() {
      answersStore.getAll({ query: { ...query, includes } })
    }

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function deleteAnswer(id) {
      confirm.require({
        message: 'Do you want to delete this record? [This action cannot be undone !]',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        iconClass: 'bg-red-500',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Yes Delete',
        accept: () => {
          answersStore.deleteAnswer(id)
        },
        reject: () => {}
      })
    }
    function toggleBulkActions(event) {
      bulkActionMenu.value.toggle(event)
    }

    function getSelectedQuestonsIds() {
      let ids = []

      selectedAnswers.value.forEach((element) => {
        ids.push(element.id)
      })

      return ids
    }

    function onBulkDeleteConfirmed() {
      getSelectedQuestonsIds()
      answersStore.bulkDeleteAnswers({ ids: getSelectedQuestonsIds() })
    }

    function reset() {
      resetButtonClicked.value = true
      //Reset filters
      filters.value = {}

      //Reset query
      Object.assign(query, JSON.parse(JSON.stringify(initialQuery)))

      //Reset paginator
      showPaginator.value = false

      initQuery()
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

    function toggleActionsMenu(event) {
      actionsMenuRef.value.toggle(event)
    }

    function add(answer) {
      emit('add', answer)
    }

    return {
      router,
      selectedAnswers,
      answersStore,
      filters,
      query,
      columnsMenuRef,
      columnVisibility,
      actionsMenuRef,
      actions,
      columns,
      bulkActions,
      toggleColumnsMenu,
      lowercaseFirstLetter,
      snake,
      findRelations,
      formatText,
      applyFilters,
      displayAllColumns,
      hideAllColumns,
      reset,
      moment,
      showPaginator,
      resetButtonClicked,
      onPage,
      deleteAnswer,
      showBulkActions,
      bulkActionMenu,
      displayBulkDeleteComponent,
      bulkDeleteValue,
      toggleBulkActions,
      onBulkDeleteConfirmed,
      toggleActionsMenu,
      add
    }
  }
}
</script>
