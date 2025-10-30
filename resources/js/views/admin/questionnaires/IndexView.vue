<template>
  <ConfirmDialog></ConfirmDialog>

  <AdminTableLayout>
    <template #table>
      <div>
        <DataTable
          :value="questionnairesStore.questionnaires && questionnairesStore.questionnaires.data"
          responsive-layout="scroll"
          :loading="questionnairesStore.loading"
          striped-rows
          data-key="id"
          filter-display="row"
        >
          <template #empty>
            <p
              v-if="!questionnairesStore.loading"
              class="p-4 text-center text-2xl bg-gray-800 text-white"
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
                  <PrimeButton
                    icon="pi pi-plus"
                    label="New Questionnaire"
                    class="!py-1 !mr-4"
                    icon-pos="right"
                    @click="() => router.push({ name: 'admin.questionnaires.create' })"
                  />
                </div>
              </div>
            </div>
          </template>

          <!--No-->
          <Column field="no" header="No">
            <template #body="slotProps"> {{ slotProps.index + 1 }}</template>
          </Column>

          <Column field="id" header="Id" :hidden="!columnVisibility.id">
            <template #body="slotProps">
              <div :id="slotProps.data.id" v-copy-to-clipboard="slotProps.data.id" class="mr-6">
                {{ slotProps.data.id }}
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
                    findRelations(
                      questionnairesStore.questionnaires.included,
                      category.id,
                      category.type
                    ).attributes.name
                  }}
                </Tag>
              </template>
            </template>
          </Column>

          <!-- Name -->
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

          <!-- Difficulty -->
          <Column
            field="difficulty"
            header="Difficulty"
            :show-filter-menu="false"
            :hidden="!columnVisibility.difficulty"
          >
            <template #filter>
              <Dropdown
                v-model="filters.difficulty"
                :options="difficultyFilterOptions"
                option-label="name"
              />
            </template>

            <template #body="slotProps">
              <Tag v-if="slotProps.data.attributes.difficulty === 'HARD'" severity="danger">{{
                slotProps.data.attributes.difficulty
              }}</Tag>
              <Tag v-else-if="slotProps.data.attributes.difficulty === 'MEDIUM'" severity="warn">{{
                slotProps.data.attributes.difficulty
              }}</Tag>
              <Tag v-else-if="slotProps.data.attributes.difficulty === 'EASY'" severity="info">{{
                slotProps.data.attributes.difficulty
              }}</Tag>
            </template>
          </Column>

          <!-- Completeness -->
          <Column
            field="completeness"
            header="Completeness"
            :show-filter-menu="false"
            :hidden="!columnVisibility.completeness"
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
            field="answersType"
            header="Answers Type"
            :show-filter-menu="false"
            :hidden="!columnVisibility.answers_type"
          >
            <template #filter>
              <Dropdown
                v-model="filters.single_answers_type"
                :options="answersTypeFilterOptions"
                option-label="name"
              />
            </template>

            <template #body="slotProps">
              <Tag v-if="slotProps.data.attributes.single_answers_type" severity="info">Single</Tag>
              <Tag v-else severity="info">Multiple</Tag>
            </template>
          </Column>

          <!-- Allocated Time -->
          <Column
            field="allocated_time"
            :show-filter-menu="false"
            :hidden="!columnVisibility.allocated_time"
            header="Allocated Time"
          >
            <template #filter>
              <div class="flex flex-col justify-center items-center">
                <span class="mb-2">{{ filters.allocated_time }}</span>
                <Slider
                  v-model="filters.allocated_time"
                  class="w-full"
                  :range="true"
                  :max="questionnaireMaxAllocatedTime"
                />
              </div>
            </template>
            <template #body="slotProps">
              <Tag severity="info">{{ slotProps.data.attributes.allocated_time }} (m)</Tag>
            </template>
          </Column>

          <!-- Total Questions -->
          <Column
            field="total_no_of_questions"
            :show-filter-menu="false"
            :hidden="!columnVisibility.no_of_total_questions"
            header="Total Questions"
          >
            <template #filter>
              <div class="flex flex-col justify-center items-center">
                <span class="mb-2">{{ filters.no_of_questions }}</span>
                <Slider
                  v-model="filters.no_of_questions"
                  class="w-full"
                  :range="true"
                  :max="totalQuestionsCount"
                />
              </div>
            </template>
            <template #body="slotProps">
              <Tag severity="info">{{ slotProps.data.attributes.no_of_questions }} </Tag>
            </template>
          </Column>

          <!-- No of EASY Questions -->
          <Column
            field="total_no_easy_questions"
            :show-filter-menu="false"
            :hidden="!columnVisibility.no_of_easy_questions"
            header="Easy Questions"
          >
            <template #filter>
              <div class="flex flex-col justify-center items-center">
                <span class="mb-2">{{ filters.no_of_easy_questions }}</span>
                <Slider
                  v-model="filters.no_of_easy_questions"
                  class="w-full"
                  :range="true"
                  :max="maxEasyQuestionsCount"
                />
              </div>
            </template>
            <template #body="slotProps">
              <Tag severity="info">{{ slotProps.data.attributes.no_of_easy_questions }} </Tag>
            </template>
          </Column>

          <!-- No of Medium Questions -->
          <Column
            field="total_no_medium_questions"
            :show-filter-menu="false"
            :hidden="!columnVisibility.no_of_medium_questions"
            header="Medium Questions"
          >
            <template #filter>
              <div class="flex flex-col justify-center items-center">
                <span class="mb-2">{{ filters.no_of_medium_questions }}</span>
                <Slider
                  v-model="filters.no_of_medium_questions"
                  class="w-full"
                  :range="true"
                  :max="maxMediumQuestionsCount"
                />
              </div>
            </template>
            <template #body="slotProps">
              <Tag severity="info">{{ slotProps.data.attributes.no_of_medium_questions }} </Tag>
            </template>
          </Column>

          <!-- No of Hard Questions -->
          <Column
            field="total_no_hard_questions"
            :show-filter-menu="false"
            :hidden="!columnVisibility.no_of_hard_questions"
            header="Hard Questions"
          >
            <template #filter>
              <div class="flex flex-col justify-center items-center">
                <span class="mb-2">{{ filters.no_of_hard_questions }}</span>
                <Slider
                  v-model="filters.no_of_hard_questions"
                  class="w-full"
                  :range="true"
                  :max="maxHardQuestionsCount"
                />
              </div>
            </template>
            <template #body="slotProps">
              <Tag severity="info">{{ slotProps.data.attributes.no_of_hard_questions }} </Tag>
            </template>
          </Column>

          <!-- No of Assigned Questions -->
          <Column
            field="assigned_questions"
            header="Assigned Questions"
            :hidden="!columnVisibility.no_of_assigned_questions"
          >
            <template #body="slotProps">
              <Tag severity="info">{{ slotProps.data.attributes.no_of_assigned_questions }}</Tag>
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
                  icon="pi pi-eye"
                  title="View questionnaire"
                  @click="showQuestionnaire(slotProps.data.id)"
                />
                <PrimeButton
                  class="p-button-sm"
                  icon="pi pi-question"
                  title="Manage questions"
                  @click="
                    () =>
                      router.push({
                        name: 'admin.questionnaires.questions.index',
                        params: { id: slotProps.data.id },
                        query: {
                          no_of_easy_questions: slotProps.data.attributes.no_of_easy_questions,
                          no_of_medium_questions: slotProps.data.attributes.no_of_medium_questions,
                          no_of_hard_questions: slotProps.data.attributes.no_of_hard_questions,
                          no_of_total_questions: slotProps.data.attributes.no_of_questions
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
                        name: 'admin.questionnaires.edit',
                        params: { id: slotProps.data.id }
                      })
                  "
                />
                <PrimeButton
                  class="p-button-danger p-button-sm"
                  icon="pi pi-trash "
                  title="Delete"
                  @click="deleteQuestionnaire(slotProps.data.id)"
                />
              </span>
            </template>
          </Column>

          <template #footer>
            <Paginator
              v-if="questionnairesStore.questionnaires && showPaginator"
              :rows="
                questionnairesStore.questionnaires &&
                questionnairesStore.questionnaires.meta.per_page
              "
              :total-records="
                questionnairesStore.questionnaires && questionnairesStore.questionnaires.meta.total
              "
              @page="onPage"
            >
            </Paginator>
            <p
              class="hidden sm:flex p-2 relative bottom-[-20px] w-full justify-center lg:justify-end"
            >
              {{
                questionnairesStore.questionnaires
                  ? questionnairesStore.questionnaires.meta.total
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
import { onMounted, reactive, ref, watch } from 'vue'

import { useQuestionnairesStore } from '@/stores/questionnaires/index'
import { useDashboardStore } from '@/stores/dashboard'
import { useCategoriesStore } from '@/stores/categories/index'

import { useRouter } from 'vue-router'

import moment from 'moment/moment'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import PrimeButton from 'primevue/button'
import MenuComponent from 'primevue/menu'
import InputText from 'primevue/inputtext'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import ConfirmDialog from 'primevue/confirmdialog'
import Slider from 'primevue/slider'
import Tag from 'primevue/tag'
import Dropdown from 'primevue/dropdown'
import { useConfirm } from 'primevue/useconfirm'

import SortComponent from '@/components/SortComponent.vue'
import Paginator from '@/components/PaginatorComponent.vue'

import { findRelations, lowercaseFirstLetter, snake } from '@/helpers'

export default {
  components: {
    AdminTableLayout,
    Avatar,
    PrimeButton,
    DataTable,
    Dropdown,
    Column,
    SortComponent,
    InputText,
    MenuComponent,
    ConfirmDialog,
    Paginator,
    Slider,
    Tag,
    IconField,
    InputIcon
  },
  setup() {
    const confirm = useConfirm()

    // Stores
    const questionnairesStore = useQuestionnairesStore()
    const dashboardStore = useDashboardStore()
    const categoriesStore = useCategoriesStore()

    const router = useRouter()

    // Questionnaire data
    const questionnaireMaxAllocatedTime = ref(0)
    const maxEasyQuestionsCount = ref(0)
    const maxMediumQuestionsCount = ref(0)
    const maxHardQuestionsCount = ref(0)
    const totalQuestionsCount = ref(0)

    // Filter dropdown options
    const categoriesFilterOptions = reactive([{ name: 'All', value: null }])
    const completenessFilterOptions = reactive([
      { name: 'All', value: null },
      { name: 'Completed', value: true },
      { name: 'Incompleted', value: false }
    ])
    const difficultyFilterOptions = reactive([
      { name: 'All', value: null },
      { name: 'Easy', value: 'EASY' },
      { name: 'Medium', value: 'MEDIUM' },
      { name: 'Hard', value: 'HARD' }
    ])
    const answersTypeFilterOptions = reactive([
      { name: 'All', value: null },
      { name: 'Single', value: true },
      { name: 'Multiple', value: false }
    ])

    const showPaginator = ref(true)

    const initialFilters = {
      ['categories.name']: categoriesFilterOptions[0],
      completed: completenessFilterOptions[0],
      difficulty: difficultyFilterOptions[0],
      single_answers_type: answersTypeFilterOptions[0],
      allocated_time: [0, 0],
      no_of_questions: [0, 0],
      no_of_easy_questions: [0, 0],
      no_of_medium_questions: [0, 0],
      no_of_hard_questions: [0, 0]
    }

    const initialQuery = { sort: {}, pagination: { number: 1, size: 10 } }

    // Query
    const filters = reactive({ ...initialFilters })
    const query = reactive({
      sort: {},
      pagination: { number: 1, size: 10 }
    })
    const includes = ['categories']

    // Column visibility
    const columnsMenuRef = ref()
    const columnVisibility = reactive({
      id: true,
      name: true,
      category: true,
      difficulty: true,
      completeness: true,
      answers_type: true,
      allocated_time: true,
      no_of_total_questions: true,
      no_of_easy_questions: true,
      no_of_medium_questions: true,
      no_of_hard_questions: true,
      no_of_assigned_questions: true,
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
        label: 'Category',
        command: () => {
          columnVisibility.category = !columnVisibility.category
        }
      },
      {
        label: 'Difficulty',
        command: () => {
          columnVisibility.difficulty = !columnVisibility.difficulty
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
        label: 'Allocated Time',
        command: () => {
          columnVisibility.allocated_time = !columnVisibility.allocated_time
        }
      },
      {
        label: 'No of total questions',
        command: () => {
          columnVisibility.no_of_total_questions = !columnVisibility.no_of_total_questions
        }
      },
      {
        label: 'No of easy questions',
        command: () => {
          columnVisibility.no_of_easy_questions = !columnVisibility.no_of_easy_questions
        }
      },
      {
        label: 'No of medium questions',
        command: () => {
          columnVisibility.no_of_medium_questions = !columnVisibility.no_of_medium_questions
        }
      },
      {
        label: 'No of hard questions',
        command: () => {
          columnVisibility.no_of_hard_questions = !columnVisibility.no_of_hard_questions
        }
      },
      {
        label: 'No of assigned questions',
        command: () => {
          columnVisibility.no_of_assigned_questions = !columnVisibility.no_of_assigned_questions
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
        label: 'New Questionnaire',
        icon: 'pi pi-plus',
        command: () => router.push({ name: 'admin.questionnaires.create' })
      }
    ])

    //-----------------------------------------------------------------------------------------------------------------------------------------------

    onMounted(() => {
      initQuery()
    })

    watch(questionnairesStore, (newUsersStore) => {
      if (!questionnairesStore.loading) {
        showPaginator.value = true
      }
      if (newUsersStore.status === 'deleted') {
        questionnairesStore.getAll({
          query: { ...query, filters: filters, includes }
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

    watch(
      () => dashboardStore.data.questionnaires,
      (newQuestionnairesData) => {
        if (Object.keys(newQuestionnairesData).length > 0) {
          const maxAllocatedTime = newQuestionnairesData.max_allocated_time_per_questionnaire
          const easyQuestionsCount =
            newQuestionnairesData.max_no_of_easy_questions_per_questionnaire
          const mediumQuestionsCount =
            newQuestionnairesData.max_no_of_medium_questions_per_questionnaire
          const hardQuestionsCount =
            newQuestionnairesData.max_no_of_hard_questions_per_questionnaire
          const allQuestionsCount =
            newQuestionnairesData.max_no_of_total_questions_per_questionnaire

          filters.allocated_time[1] = maxAllocatedTime
          questionnaireMaxAllocatedTime.value = maxAllocatedTime

          filters.no_of_questions[1] = allQuestionsCount
          totalQuestionsCount.value = allQuestionsCount

          filters.no_of_easy_questions[1] = easyQuestionsCount
          maxEasyQuestionsCount.value = easyQuestionsCount

          filters.no_of_medium_questions[1] = mediumQuestionsCount
          maxMediumQuestionsCount.value = mediumQuestionsCount

          filters.no_of_hard_questions[1] = hardQuestionsCount
          maxHardQuestionsCount.value = hardQuestionsCount
        }
      }
    )

    watch(query, (newQuery) => {
      questionnairesStore.getAll({
        query: { ...newQuery, filters, includes }
      })
    })

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

    function initQuery() {
      questionnairesStore.getAll({
        query: { ...query, includes }
      })
      categoriesStore.getAll()
      dashboardStore.getQuestionnairesData()
    }

    function applyFilters() {
      showPaginator.value = false // Reset the pagination
      query.pagination.number = 1

      questionnairesStore.getAll({
        query: { filters: filters, ...query, includes }
      })
    }

    function onPage(event) {
      query.pagination.number = event.page + 1
      query.pagination.size = event.rows
    }

    function reset() {
      //Reset filters
      Object.assign(filters, { ...initialFilters })

      //Reset query
      Object.assign(query, { ...initialQuery })

      //Reset paginator
      showPaginator.value = false

      initQuery()
    }

    function toggleActionsMenu(event) {
      actionsMenuRef.value.toggle(event)
    }

    function toggleColumnsMenu(event) {
      columnsMenuRef.value.toggle(event)
    }

    function deleteQuestionnaire(id) {
      confirm.require({
        message: 'Do you want to delete this record? [This action cannot be undone !]',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        iconClass: 'bg-red-500',
        acceptClass: 'p-button-danger',
        acceptLabel: 'Yes Delete',
        accept: () => {
          questionnairesStore.deleteQuestionnaire(id)
        },
        reject: () => {}
      })
    }
    function showQuestionnaire(id) {
      const routeData = router.resolve({
        name: 'admin.questionnaires.questions.show',
        params: { id }
      })
      window.open(routeData.href, '_blank')
    }

    return {
      questionnairesStore,
      categoriesStore,
      dashboardStore,
      categoriesFilterOptions,
      completenessFilterOptions,
      difficultyFilterOptions,
      answersTypeFilterOptions,
      questionnaireMaxAllocatedTime,
      totalQuestionsCount,
      maxEasyQuestionsCount,
      maxMediumQuestionsCount,
      maxHardQuestionsCount,
      moment,
      query,
      filters,
      applyFilters,
      showPaginator,
      reset,
      deleteQuestionnaire,
      columns,
      columnsMenuRef,
      toggleColumnsMenu,
      columnVisibility,
      displayAllColumns,
      hideAllColumns,
      lowercaseFirstLetter,
      snake,
      router,
      actions,
      actionsMenuRef,
      toggleActionsMenu,
      onPage,
      findRelations,
      showQuestionnaire
    }
  }
}
</script>
