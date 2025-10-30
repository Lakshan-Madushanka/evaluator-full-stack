<template>
  <ConfirmDialog />
  <BulkDeleteComponent
    :display-component="displayBulkDeleteComponent"
    :value="bulkDeleteValue"
    :status="questionsStore.status"
    @on-dialog-hidden="displayBulkDeleteComponent = $event"
    @bulk-delete-confirmed="onBulkDeleteConfirmed"
  />

  <AdminTableLayout>
    <template #table>
      <DataTable
        v-model:selection="selectedQuestions"
        :value="questionsStore.questions && questionsStore.questions.data"
        responsive-layout="scroll"
        :loading="questionsStore.loading"
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
              <span class="font-bold">{{ selectedQuestions.length }}</span>
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
              <p class="mr-2">Questions</p>
              <Avatar class="hover:cursor-pointer" icon="pi pi-eye" @click="toggleColumnsMenu" />
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
                  label="New Question"
                  class="!py-1 !mr-4"
                  icon-pos="right"
                  @click="() => router.push({ name: 'admin.questions.create' })"
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
        <Column field="id" header="Id" :hidden="!columnVisibility.id">
          <template #body="slotProps">
            <div
              :id="slotProps.data.attributes.pretty_id"
              v-copy-to-clipboard="slotProps.data.attributes.pretty_id"
              class="mr-6"
            >
              {{ slotProps.data.attributes.pretty_id }}
            </div>
          </template>
        </Column>

        <!-- Category -->
        <Column header="Category" :show-filter-menu="false" :hidden="!columnVisibility.category">
          <template #filter>
            <Dropdown
              v-model="filters['categories.name']"
              :options="categoriesFilterOptions"
              option-label="name"
              :loading="categoriesStore.loading"
            />
          </template>
          <template #body="slotProps">
            <template v-if="slotProps.data.relationships.categories.data.length > 0">
              <Tag
                v-for="category in slotProps.data.relationships.categories.data"
                :key="category.id"
                severity="info"
                class="mr-1"
              >
                {{
                  findRelations(questionsStore.questions.included, category.id, category.type)
                    .attributes.name
                }}
              </Tag>
            </template>
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
              <IconField>
                <InputIcon class="pi pi-search" />
                <InputText
                  v-model="filters.content"
                  type="text"
                  placeholder="Search"
                  @keyup.enter="applyFilters"
                />
              </IconField>
            </span>
          </template>
          <template #body="slotProps">
            <div class="flex justify-between items-center relative">
              <p v-html="formatText(slotProps.data.attributes.content, 100)"></p>
              <div class="group">
                <i class="pi pi-eye !block hover:text-blue-600 hover:cursor-pointer !text-xl">
                  <p
                    class="p-4 bg-black text-white hidden group-hover:block absolute left-0 top-[-1rem] whitespace-normal z-10"
                    v-html="slotProps.data.attributes.content"
                  ></p>
                </i>
              </div>
            </div>
          </template>
        </Column>

        <!-- Completeness -->
        <Column
          field="completeness"
          header="Completeness"
          :show-filter-menu="false"
          :hidden="!columnVisibility.completeness"
          body-class="!text-center"
        >
          <template #filter>
            <Dropdown
              v-model="filters.completed"
              :options="completenessFilterOptions"
              option-label="name"
            />
          </template>

          <template #body="slotProps">
            <i
              v-if="slotProps.data.attributes.completed"
              class="pi pi-check-circle !text-2xl text-green-500"
            ></i>
            <i v-else class="pi pi-times-circle !text-2xl text-red-500"></i>
          </template>
        </Column>

        <!-- Answers Type -->
        <Column
          field="answers_type"
          header="Answers Type"
          :show-filter-menu="false"
          :hidden="!columnVisibility.answers_type"
        >
          <template #filter>
            <Dropdown
              v-model="filters.answers_type_single"
              :options="answersTypeFilterOptions"
              option-label="name"
            />
          </template>

          <template #body="slotProps">
            <Tag v-if="slotProps.data.attributes.answers_type_single" severity="info">Single</Tag>
            <Tag v-else severity="info">Multiple</Tag>
          </template>
        </Column>

        <!-- Difficulty -->
        <Column
          field="difficulty"
          header="Difficulty"
          :show-filter-menu="false"
          :hidden="!columnVisibility.difficulty"
        >
          <template #filter>
            <Dropdown
              v-model="filters.hardness"
              :options="difficultyFilterOptions"
              option-label="name"
            />
          </template>

          <template #body="slotProps">
            <Tag v-if="slotProps.data.attributes.hardness === 'HARD'" severity="danger">{{
              slotProps.data.attributes.hardness
            }}</Tag>
            <Tag v-else-if="slotProps.data.attributes.hardness === 'MEDIUM'" severity="warning">{{
              slotProps.data.attributes.hardness
            }}</Tag>
            <Tag v-else-if="slotProps.data.attributes.hardness === 'EASY'" severity="info">{{
              slotProps.data.attributes.hardness
            }}</Tag>
          </template>
        </Column>

        <!-- No of Answers -->
        <Column
          field="no_of_answers"
          header="No of Aswers"
          :show-filter-menu="false"
          :hidden="!columnVisibility.no_of_answers"
          body-class="!text-center"
        >
          <template #body="slotProps">
            <Tag severity="info">{{ slotProps.data.attributes.no_of_answers }}</Tag>
          </template>
        </Column>

        <!-- No of Assigned Answers -->
        <Column
          field="no_of_assigned_answers"
          header="No of Assigned Aswers"
          :show-filter-menu="false"
          :hidden="!columnVisibility.no_of_assigned_answers"
          body-class="!text-center"
        >
          <template #body="slotProps">
            <Tag severity="info">{{ slotProps.data.attributes.no_of_assigned_answers }}</Tag>
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
                class="p-button-sm"
                icon="pi pi-slack"
                title="Manage Images"
                @click="
                  () =>
                    router.push({
                      name: 'admin.images.manager',
                      params: { id: slotProps.data.id, type: 'questions' },
                      query: { pretty_id: slotProps.data.attributes.pretty_id }
                    })
                "
              />
              <PrimeButton
                class="p-button-sm"
                icon="pi pi-language"
                title="Manage Answers"
                @click="
                  () =>
                    router.push({
                      name: 'admin.questions.answers.index',
                      params: { id: slotProps.data.id },
                      query: {
                        pretty_id: slotProps.data.attributes.pretty_id,
                        total_answers: slotProps.data.attributes.no_of_answers
                      }
                    })
                "
              />
              <PrimeButton
                class="p-button-sm"
                icon="pi pi-file-edit"
                title="Edit"
                @click="
                  () =>
                    router.push({
                      name: 'admin.questions.edit',
                      params: { id: slotProps.data.id }
                    })
                "
              />
              <PrimeButton
                class="p-button-danger p-button-sm"
                icon="pi pi-trash "
                title="Delete"
                @click="deleteQuestion(slotProps.data.id)"
              />
            </span>
          </template>
        </Column>

        <template #empty>
          <p v-if="!questionsStore.loading" class="p-4 text-center text-2xl bg-gray-800 text-white">
            No records found.
          </p>
        </template>

        <template #footer>
          <Paginator
            v-if="questionsStore.questions && showPaginator"
            :rows="questionsStore.questions && questionsStore.questions.meta.per_page"
            :total-records="questionsStore.questions && questionsStore.questions.meta.total"
            @page="onPage"
          >
          </Paginator>
          <p
            class="hidden sm:flex p-2 relative bottom-[-20px] w-full justify-center lg:justify-end"
          >
            {{ questionsStore.questions ? questionsStore.questions.meta.total : 0 }}
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

import { useQuestionsStore } from '@/stores/questions'
import { useCategoriesStore } from '@/stores/categories/index'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import ConfirmDialog from 'primevue/confirmdialog'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import Dropdown from 'primevue/dropdown'
import MenuComponent from 'primevue/menu'
import PrimeButton from 'primevue/button'
import InputText from 'primevue/inputtext'
import Tag from 'primevue/tag'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import { useConfirm } from 'primevue/useconfirm'

import moment from 'moment/moment'

import SortComponent from '@/components/SortComponent.vue'
import BulkDeleteComponent from '@/components/BulkDeleteComponent.vue'
import Paginator from '@/components/PaginatorComponent.vue'

import { findRelations, formatText, lowercaseFirstLetter, snake } from '@/helpers'

export default {
  components: {
    AdminTableLayout,
    Avatar,
    BulkDeleteComponent,
    ConfirmDialog,
    Column,
    DataTable,
    Dropdown,
    InputText,
    MenuComponent,
    Paginator,
    PrimeButton,
    SortComponent,
    Tag,
    IconField,
    InputIcon
  },
  setup() {
    const confirm = useConfirm()

    const router = useRouter()

    const questionsStore = useQuestionsStore()
    const categoriesStore = useCategoriesStore()

    const showPaginator = ref(true)
    const resetButtonClicked = ref(false)

    const selectedQuestions = ref()

    // Bulk Actions
    const showBulkActions = ref(false)
    const bulkActionMenu = ref()
    const displayBulkDeleteComponent = ref(false)
    const bulkDeleteValue = ref('')
    const bulkActions = [
      {
        label: 'Delete selected',
        icon: 'pi pi-trash',
        command: () => {
          let value = ''
          if (selectedQuestions.value.length === 1) {
            value = selectedQuestions.value[0]['attributes']['pretty_id']
          } else {
            value = selectedQuestions.value.length + ' ' + 'records'
          }

          bulkDeleteValue.value = value
          displayBulkDeleteComponent.value = true
        }
      }
    ]

    // Filter dropdown options
    const categoriesFilterOptions = reactive([{ name: 'All', value: null }])
    const completenessFilterOptions = reactive([
      { name: 'All', value: null },
      { name: 'Completed', value: true },
      { name: 'Incompleted', value: false }
    ])

    const answersTypeFilterOptions = reactive([
      { name: 'All', value: null },
      { name: 'Single', value: true },
      { name: 'Multiple', value: false }
    ])
    const difficultyFilterOptions = reactive([
      { name: 'All', value: null },
      { name: 'Easy', value: 'EASY' },
      { name: 'Medium', value: 'MEDIUM' },
      { name: 'Hard', value: 'HARD' }
    ])

    // Query
    const filters = ref({ ['categories.name']: categoriesFilterOptions[0] })
    const initialQuery = { sort: {}, pagination: { number: 1, size: 10 } }
    const query = reactive(JSON.parse(JSON.stringify(initialQuery)))
    const includes = ['categories']

    // Column visibility
    const columnsMenuRef = ref()
    const columnVisibility = reactive({
      id: true,
      category: true,
      content: true,
      completeness: true,
      answers_type: true,
      difficulty: true,
      no_of_answers: true,
      no_of_assigned_answers: true,
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
        label: 'Category',
        command: () => {
          columnVisibility.category = !columnVisibility.category
        }
      },
      {
        label: 'Content',
        command: () => {
          columnVisibility.content = !columnVisibility.content
        }
      },
      {
        label: 'Completeness',
        command: () => {
          columnVisibility.completeness = !columnVisibility.completeness
        }
      },
      {
        label: 'Answers Type',
        command: () => {
          columnVisibility.answers_type = !columnVisibility.answers_type
        }
      },
      {
        label: 'Difficulty',
        command: () => {
          columnVisibility.difficulty = !columnVisibility.difficulty
        }
      },
      {
        label: 'No of Answers',
        command: () => {
          columnVisibility.no_of_answers = !columnVisibility.no_of_answers
        }
      },
      {
        label: 'No of Assigned Answers',
        command: () => {
          columnVisibility.no_of_assigned_answers = !columnVisibility.no_of_assigned_answers
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
        label: 'New Question',
        icon: 'pi pi-plus',
        command: () => router.push({ name: 'admin.questions.create' })
      }
    ])

    //------------------------------------------------------------------------------------------------------------------------------------------------

    onMounted(() => {
      initQuery()
    })

    watch(questionsStore, (newQuestionsStore) => {
      if (!newQuestionsStore.loading) {
        showPaginator.value = true
        resetButtonClicked.value = false
      }
      if (newQuestionsStore.status === 'deleted') {
        displayBulkDeleteComponent.value = false
        selectedQuestions.value = []
        newQuestionsStore.getAll({
          query: { ...query, filters: filters.value, includes }
        })
      }
    })

    watch(
      () => categoriesStore.categories,
      (newCategories) => {
        if (newCategories) {
          newCategories.data.forEach((category) => {
            categoriesFilterOptions.push({
              name: category.attributes.name,
              value: category.attributes.name
            })
          })
        }
      }
    )

    watch(selectedQuestions, (newSelectedQuestions) => {
      if (newSelectedQuestions && newSelectedQuestions.length > 0) {
        showBulkActions.value = true
        return
      }
      showBulkActions.value = false
    })

    watch(query, (newQuery) => {
      if (resetButtonClicked.value) {
        return
      }
      questionsStore.getAll({
        query: { ...newQuery, filters: filters.value, includes }
      })
    })

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
    }

    function applyFilters() {
      query.pagination.number = 1
      showPaginator.value = false

      questionsStore.getAll({
        query: { filters: filters.value, ...query, includes }
      })
    }

    function initQuery() {
      questionsStore.getAll({ query: { ...query, includes } })
      categoriesStore.getAll()
    }

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function deleteQuestion(id) {
      confirm.require({
        message: 'Do you want to delete this record? [This action cannot be undone !]',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        iconClass: 'bg-red-500',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Yes Delete',
        accept: () => {
          questionsStore.deleteQuestion(id)
        },
        reject: () => {}
      })
    }
    function toggleBulkActions(event) {
      bulkActionMenu.value.toggle(event)
    }

    function getSelectedQuestonsIds() {
      let ids = []

      selectedQuestions.value.forEach((element) => {
        ids.push(element.id)
      })

      return ids
    }

    function onBulkDeleteConfirmed() {
      getSelectedQuestonsIds()
      questionsStore.bulkDeleteQuestions({ ids: getSelectedQuestonsIds() })
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

    return {
      router,
      selectedQuestions,
      questionsStore,
      categoriesStore,
      filters,
      query,
      columnsMenuRef,
      columnVisibility,
      actionsMenuRef,
      actions,
      columns,
      completenessFilterOptions,
      answersTypeFilterOptions,
      difficultyFilterOptions,
      categoriesFilterOptions,
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
      deleteQuestion,
      showBulkActions,
      bulkActionMenu,
      displayBulkDeleteComponent,
      bulkDeleteValue,
      toggleBulkActions,
      onBulkDeleteConfirmed,
      toggleActionsMenu
    }
  }
}
</script>
