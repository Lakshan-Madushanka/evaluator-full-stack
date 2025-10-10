<template>
  <PrimeDialog
    v-model:visible="showSearchDialog"
    modal
    header="Search Evaluation "
    class="xl:w-1/2 lg:w-[60%] md:w-[85%] w-[95%]"
  >
    <div class="m-1 space-y-4">
      <div class="flex items-center">
        <label for="user_id" class="mr-4 mb-2 w-[20%]">User Id</label>
        <InputText
          id="user_id"
          v-model="searchData[0]"
          placeholder="User Id"
          class="w-[75%]"
          @keyup.enter="search"
        />
      </div>
      <div class="flex items-center">
        <label for="user_id" class="mr-4 mb-2 w-[20%]">Questionnaire Id</label>
        <InputText
          id="user_id"
          v-model="searchData[1]"
          placeholder="User Id"
          class="w-[75%]"
          @keyup.enter="search"
        />
      </div>
    </div>

    <p v-if="!validateSearchData()" class="text-red-600 mt-2 text-center">
      User id or questionnaire id required
    </p>

    <div class="!mt-4 flex justify-center">
      <PrimeButton label="Search" icon="pi pi-search" icon-pos="right" @click="search" />
    </div>
  </PrimeDialog>

  <AdminTableLayout>
    <template #table>
      <DataTable
        :value="evaluationsStore.evaluations && evaluationsStore.evaluations.data"
        responsive-layout="scroll"
        :loading="evaluationsStore.loading"
        striped-rows
        data-key="id"
        filter-display="row"
      >
        <template #empty>
          <p v-if="!evaluationsStore.loading" class="p-4 text-center text-2xl bg-blue-200">
            No records found.
          </p>
        </template>

        <template #header>
          <div class="flex justify-between items-center text-2xl uppercase">
            <div class="flex">
              <p class="mr-2">Evaluations</p>
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
                  :disabled="Object.keys(query.filters).length === 0"
                  @click="applyFilters"
                />
                <PrimeButton
                  icon="pi pi-search"
                  class="!mr-4 !py-1"
                  icon-pos="right"
                  label="Search"
                  @click="showSearchDialog = !showSearchDialog"
                />
              </div>
            </div>
          </div>
        </template>

        <!--No-->
        <Column field="no" header="No" :hidden="!columnVisibility.no">
          <template #body="slotProps"> {{ slotProps.index + 1 }}</template>
        </Column>

        <!--Id-->
        <Column field="id" header="Id" :hidden="!columnVisibility.id">
          <template #body="slotProps"> {{ slotProps.data.id }}</template>
        </Column>

        <!-- User Id -->
        <Column
          field="user_id"
          header="User Id"
          :show-filter-menu="false"
          :hidden="!columnVisibility.user_id"
        >
          <template #filter>
            <span>
              <IconField>
                <InputIcon class="pi pi-search" />
                <InputText
                  v-model="query.filters.user"
                  type="text"
                  placeholder="Search"
                  @keyup.enter="applyFilters"
                  class="!min-w-[8rem]"
                />
              </IconField>
            </span>
          </template>
          <template #body="slotProps">
            <div
              :id="slotProps.data.attributes.user_id"
              v-copy-to-clipboard="slotProps.data.attributes.user_id"
              class="w-24"
            >
              {{ slotProps.data.attributes.user_id }}
            </div>
          </template>
        </Column>

        <!-- Questionnaire Id -->
        <Column
          field="questionnaire_id"
          header="Questionnaire Id"
          :show-filter-menu="false"
          :hidden="!columnVisibility.questionnaire_id"
        >
          <template #filter>
            <span>
              <IconField>
                <InputIcon class="pi pi-search" />
                <InputText
                  v-model="query.filters.questionnaire"
                  type="text"
                  placeholder="Search"
                  @keyup.enter="applyFilters"
                  class="!min-w-[8rem]"
                />
              </IconField>
            </span>
          </template>
          <template #body="slotProps">
            <div
              :id="slotProps.data.attributes.questionnaire_id"
              v-copy-to-clipboard="slotProps.data.attributes.questionnaire_id"
              class="w-24"
            >
              {{ slotProps.data.attributes.questionnaire_id }}
            </div>
          </template>
        </Column>

        <!-- Marks Percentage -->
        <Column
          field="marks percentage"
          :show-filter-menu="false"
          :show-clear-button="false"
          :hidden="!columnVisibility.marks_percentage"
          class="!text-center"
        >
          <template #header>
            <div class="flex justify-between items-center w-full">
              <p class="mr-4">Marks Percentage (%)</p>
              <SortComponent
                :dir="query.sort.marks_percentage"
                @direction-change="query.sort.marks_percentage = $event"
              />
            </div>
          </template>

          <template #filter>
            <div class="flex flex-col justify-center items-center">
              <span class="mb-2">{{ query.filters.marks_percentage }}</span>
              <Slider
                v-model="query.filters.marks_percentage"
                class="w-full"
                :range="true"
                :max="100"
              />
            </div>
          </template>
          <template #body="slotProps">
            <Tag severity="info">{{ slotProps.data.attributes.marks_percentage }} </Tag>
          </template>
        </Column>

        <!-- Time taken -->
        <Column
          field="time taken"
          :show-filter-menu="false"
          :hidden="!columnVisibility.time_taken"
          class="!text-center"
        >
          <template #header>
            <div class="flex justify-between items-center w-full">
              <p class="mr-4">Time taken (H:M)</p>
              <SortComponent
                :dir="query.sort.time_taken"
                @direction-change="query.sort.time_taken = $event"
              />
            </div>
          </template>

          <template #body="slotProps">
            <Tag severity="info">
              {{
                slotProps.data.attributes.time_taken < 1
                  ? '<1min'
                  : formatMinutes(slotProps.data.attributes.time_taken, true)
              }}
            </Tag>
          </template>
        </Column>

        <!-- No of correct answers -->
        <Column
          field="no of correct answers"
          :show-filter-menu="false"
          :hidden="!columnVisibility.no_of_correct_answers"
          class="!text-center"
        >
          <template #header>
            <div class="flex justify-between items-center w-full">
              <p class="mr-4">No of correct answers</p>
              <SortComponent
                :dir="query.sort.no_of_correct_answers"
                @direction-change="query.sort.no_of_correct_answers = $event"
              />
            </div>
          </template>

          <template #body="slotProps">
            <Tag severity="info">{{ slotProps.data.attributes.no_of_correct_answers }}</Tag>
          </template>
        </Column>

        <!-- Total points earned -->
        <Column
          field="total points earned"
          :show-filter-menu="false"
          :hidden="!columnVisibility.total_points_earned"
          class="!text-center"
        >
          <template #header>
            <div class="flex justify-between items-center w-full">
              <p class="mr-4">Total points earned</p>
              <SortComponent
                :dir="query.sort.total_points_earned"
                @direction-change="query.sort.total_points_earned = $event"
              />
            </div>
          </template>

          <template #body="slotProps">
            <Tag severity="info">{{ slotProps.data.attributes.total_points_earned }}</Tag>
          </template>
        </Column>

        <!-- Total points allocated -->
        <Column
          field="total points allocated"
          :show-filter-menu="false"
          :hidden="!columnVisibility.total_points_allocated"
          class="!text-center"
        >
          <template #header>
            <div class="flex justify-between items-center w-full">
              <p class="mr-4">Total points allocated</p>
              <SortComponent
                :dir="query.sort.total_points_allocated"
                @direction-change="query.sort.total_points_allocated = $event"
              />
            </div>
          </template>

          <template #body="slotProps">
            <Tag severity="info">{{ slotProps.data.attributes.total_points_allocated }}</Tag>
          </template>
        </Column>

        <!-- Show Evaluation -->
        <Column
          field="showEvaluation"
          header="Show Evaluation"
          :hidden="!columnVisibility.show_evaluation"
        >
          <template #body="slotProps">
            <span class="p-buttonset space-x-1 flex justify-center">
              <PrimeButton
                class="p-button-sm"
                icon="pi pi-eye"
                title="Show evaluation"
                severity="contrast"
                @click="
                  showEvaluation(slotProps.data.id, slotProps.data.attributes.questionnaire_id)
                "
              />
            </span>
          </template>
        </Column>

        <!-- Created at -->
        <Column field="created at" :show-filter-menu="false" :hidden="!columnVisibility.created_at">
          <template #header>
            <div class="flex justify-between items-center w-full">
              <p class="mr-4">Created at</p>
              <SortComponent
                :dir="query.sort.created_at"
                @direction-change="query.sort.created_at = $event"
              />
            </div>
          </template>

          <template #body="slotProps">
            {{ moment(slotProps.data.attributes.created_at).format('ddd, MMM D, yyyy, h:mm a') }}
          </template>
        </Column>

        <template #footer>
          <Paginator
            v-if="evaluationsStore.evaluations && showPaginator"
            :rows="evaluationsStore.evaluations && evaluationsStore.evaluations.meta.per_page"
            :total-records="evaluationsStore.evaluations && evaluationsStore.evaluations.meta.total"
            @page="onPaginatorChange"
          >
          </Paginator>
          <p
            class="hidden sm:flex p-2 relative bottom-[-20px] w-full justify-center lg:justify-end"
          >
            {{ evaluationsStore.evaluations ? evaluationsStore.evaluations.meta.total : 0 }}
            records found.
          </p>
        </template>
      </DataTable>
    </template>
  </AdminTableLayout>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue'

import { useRouter, useRoute } from 'vue-router'

import { useEvaluationsStore } from '@/stores/evaluations'

import AdminTableLayout from '@/views/layouts/AdminTableLayout.vue'
import Paginator from '@/components/PaginatorComponent.vue'
import SortComponent from '@/components/SortComponent.vue'

import Avatar from 'primevue/avatar'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import PrimeDialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import MenuComponent from 'primevue/menu'
import PrimeButton from 'primevue/button'
import Slider from 'primevue/slider'
import Tag from 'primevue/tag'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'

import moment from 'moment/moment'

import { lowercaseFirstLetter, snake, formatMinutes } from '@/helpers'

export default {
  components: {
    AdminTableLayout,
    Paginator,
    Avatar,
    Column,
    DataTable,
    PrimeDialog,
    InputText,
    MenuComponent,
    PrimeButton,
    Slider,
    SortComponent,
    Tag,
    IconField,
    InputIcon
  },
  setup() {
    const router = useRouter()
    const route = useRoute()

    const evaluationsStore = useEvaluationsStore()

    const resetting = ref(false)

    const showSearchDialog = ref(false)
    const searchData = ref(['', ''])
    const searchButtonClicked = ref(false)

    //Paginator
    const showPaginator = ref(true)

    //query
    const initialFilters = { marks_percentage: [0, 100] }
    const initialSort = {}
    const initialPagination = { number: 1, size: 10 }

    const query = reactive({
      filters: { ...initialFilters },
      sort: {},
      pagination: { ...initialPagination }
    })

    // Set coulmns visibility
    const columnsMenuRef = ref()
    const columnVisibility = reactive({
      no: true,
      id: true,
      user_id: true,
      questionnaire_id: true,
      marks_percentage: true,
      time_taken: true,
      no_of_correct_answers: true,
      total_points_earned: true,
      total_points_allocated: true,
      no_of_answered_questions: true,
      created_at: true,
      show_evaluation: true
    })
    const columns = ref([
      {
        label: 'Bulk Controllers',
        command: () => {}
      },
      {
        label: 'No',
        command: () => {
          columnVisibility.no = !columnVisibility.no
        }
      },
      {
        label: 'Id',
        command: () => {
          columnVisibility.id = !columnVisibility.id
        }
      },
      {
        label: 'User Id',
        command: () => {
          columnVisibility.user_id = !columnVisibility.user_id
        }
      },
      {
        label: 'Questionnaire Id',
        command: () => {
          columnVisibility.questionnaire_id = !columnVisibility.questionnaire_id
        }
      },
      {
        label: 'Marks Percentage',
        command: () => {
          columnVisibility.marks_percentage = !columnVisibility.marks_percentage
        }
      },
      {
        label: 'Time taken',
        command: () => {
          columnVisibility.time_taken = !columnVisibility.time_taken
        }
      },
      {
        label: 'No of correct answers',
        command: () => {
          columnVisibility.no_of_correct_answers = !columnVisibility.no_of_correct_answers
        }
      },
      {
        label: 'Total points earned',
        command: () => {
          columnVisibility.total_points_earned = !columnVisibility.total_points_earned
        }
      },
      {
        label: 'Total points allocated',
        command: () => {
          columnVisibility.total_points_allocated = !columnVisibility.total_points_allocated
        }
      },
      {
        label: 'No of answered questions',
        command: () => {
          columnVisibility.no_of_answered_questions = !columnVisibility.no_of_answered_questions
        }
      },
      {
        label: 'Show Evaluation',
        command: () => {
          columnVisibility.show_evaluation = !columnVisibility.show_evaluation
        }
      },
      {
        label: 'Created at',
        command: () => {
          columnVisibility.created_at = !columnVisibility.created_at
        }
      }
    ])

    //Actions menu
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
        label: 'Search',
        icon: 'pi pi-search',
        command: () => {
          showSearchDialog.value = true
        }
      }
    ])

    onMounted(() => {
      if (route.query.uq_id) {
        query.filters.uq_id = route.query.uq_id
        loadData(true)
      } else {
        loadData()
      }
    })

    watch(evaluationsStore, function (newStore) {
      if (!newStore.loading) {
        resetting.value = false
        showPaginator.value = true
      }
    })

    watch(
      [() => query.sort, () => query.pagination],
      () => {
        if (resetting.value === true) {
          return
        }

        evaluationsStore.getAll({
          query: { ...query }
        })
      },
      { deep: true }
    )

    function loadData(withFilters = false) {
      if (!withFilters) {
        evaluationsStore.getAll({
          query: { pagination: initialPagination }
        })
      } else {
        evaluationsStore.getAll({
          query: { filters: query.filters, pagination: initialPagination }
        })
      }
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

    function onPaginatorChange(event) {
      const pagination = {}
      pagination.number = event.page + 1
      pagination.size = event.rows

      query.pagination = pagination
    }

    function applyFilters() {
      evaluationsStore.getAll({ query: { ...query } })
    }

    function reset() {
      resetting.value = true
      showPaginator.value = false

      query.pagination = { ...initialPagination }
      query.sort = { ...initialSort }
      query.filters = { ...initialFilters }

      loadData()
    }

    function search() {
      searchButtonClicked.value = true

      if (!validateSearchData()) {
        return
      }

      showSearchDialog.value = false

      evaluationsStore.getAll({
        query: { filters: { user_questionnaire: searchData } }
      })
    }

    function validateSearchData() {
      if (searchData.value[0] === '' && searchData.value[1] === '' && searchButtonClicked.value) {
        return false
      }

      return true
    }

    function showEvaluation(evaluationId, questionnaireId) {
      const routeData = router.resolve({
        name: 'admin.evaluations.show',
        params: { evaluationId, questionnaireId }
      })
      window.open(routeData.href, '_blank')
    }

    return {
      showSearchDialog,
      searchData,
      searchButtonClicked,
      query,
      evaluationsStore,
      showPaginator,
      columnsMenuRef,
      columnVisibility,
      columns,
      actionsMenuRef,
      actions,
      toggleColumnsMenu,
      toggleActionsMenu,
      displayAllColumns,
      hideAllColumns,
      onPaginatorChange,
      applyFilters,
      reset,
      search,
      validateSearchData,
      snake,
      lowercaseFirstLetter,
      moment,
      showEvaluation,
      formatMinutes
    }
  }
}
</script>
