<template>
  <Paginator
    :template="{
      '640px': 'PrevPageLink CurrentPageReport NextPageLink',
      '960px': 'FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink',
      '1300px':
        'JumpToPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink  RowsPerPageDropdown',
      default:
        'JumpToPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink  RowsPerPageDropdown'
    }"
    :rows="props.rows"
    :total-records="props.totalRecords"
    :rows-per-page-options="[10, 20, 30]"
  />
</template>

<script>
import Paginator from 'primevue/paginator'

export default {
  components: { Paginator },
  props: {
    rows: { type: Number, default: null },
    totalRecords: { type: Number, default: null }
  },
  setup(props) {
    return { props }
  }
}
</script>
