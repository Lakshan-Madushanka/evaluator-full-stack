<template>
  <ConfirmDialog />
  <div class="mb-4">
    <h1 class="text-2xl font-bold uppercase mb-2 text-center md:text-left">
      Manage images of {{ route.params.type }} id
      <span class="lowercase text-green-500">{{ route.query.pretty_id }}</span>
    </h1>
  </div>
  <div class="p-4 space-y-8 pb-4">
    <div class="bg-white dark:bg-inherit shadow p-8">
      <p class="text-xl text-bold text-center mb-4">All uploaded images</p>
      <Skeleton v-if="imagesStore.loading" class="mb-2 !h-72"></Skeleton>
      <PrimeGalleria
        v-if="!imagesStore.loading && imagesStore.images.length > 0"
        container-class="m-auto"
        :value="imagesStore.images"
        :num-visible="5"
        container-style="width: 640px; padding: 4px"
      >
        <template #item="slotProps">
          <img
            :src="slotProps.item.attributes.original_url"
            :alt="slotProps.item.attributes.alt"
            :title="slotProps.item.attributes.name"
            style="max-width: 100%"
            class="object-fill"
          />
        </template>
        <template #thumbnail="slotProps">
          <img
            :src="slotProps.item.attributes.original_url"
            :alt="slotProps.item.attributes.alt"
            :title="slotProps.item.attributes.name"
            class="max-w-[6rem]"
          />
        </template>
      </PrimeGalleria>
      <p
        v-if="!imagesStore.loading && imagesStore.images.length === 0"
        class="text-center text-blue-600"
      >
        0 images has been uploaded
      </p>
    </div>
    <div class="bg-white dark:bg-black shadow p-8">
      <p class="text-xl font-bold mb-4">Upload image</p>
      <FileUpload
        name="images[]"
        :url="getImageUploadRoute()"
        :multiple="true"
        accept="image/*"
        :max-file-size="5000000"
        :with-credentials="true"
        mode="advanced"
        @before-send="beforeSend"
        @upload="onUploadCompleted"
      >
        <template #empty>
          <p>Drag and drop files to here to upload.</p>
        </template>
      </FileUpload>
    </div>
    <div class="bg-white dark:bg-black shadow p-8">
      <p class="text-xl font-bold mb-4">Change order</p>
      <Skeleton v-if="imagesStore.loading" class="mb-2 !h-72"></Skeleton>
      <div v-if="!imagesStore.loading && imagesStore.images.length > 0">
        <OrderList
          v-model="imagesStore.images"
          v-model:selection="selectedImages"
          list-style="height:auto"
          data-key="id"
        >
          <template #header> List of Images </template>

          <template #item="slotProps">
            <div class="flex flex-wrap justify-between space-x-4 items-center">
              <div class="">
                <img
                  class="w-8 h-8"
                  :src="slotProps.item.attributes.original_url"
                  :alt="slotProps.item.attributes.name"
                />
              </div>
              <div class="">
                <p>{{ slotProps.item.attributes.file_name }}</p>
                <p>
                  {{
                    moment(slotProps.item.attributes.created_at).format('ddd, MMM D, yyyy, h:mm a')
                  }}
                </p>
              </div>
            </div>
          </template>
        </OrderList>
        <div class="mt-4 flex justify-center space-x-4">
          <PrimeButton
            :label="imagesStore.status === 'changing' ? 'Changing order' : 'Change order'"
            :loading="imagesStore.status === 'changing'"
            @click="changeOrder"
          />
          <PrimeButton
            v-if="selectedImages.length > 0"
            :label="imagesStore.status === 'removing' ? 'Removing' : 'Remove'"
            class="p-button-danger"
            :loading="imagesStore.status === 'removing'"
            @click="removeAll"
          />
        </div>
      </div>
      <p
        v-if="!imagesStore.loading && imagesStore.images.length === 0"
        class="text-center text-blue-600"
      >
        0 images has been uploaded
      </p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'

import { useRoute } from 'vue-router'

import { useImagesStore } from '@/stores/images/manager'

import ConfirmDialog from 'primevue/confirmdialog'
import PrimeGalleria from 'primevue/galleria'
import PrimeButton from 'primevue/button'
import Skeleton from 'primevue/skeleton'
import FileUpload from 'primevue/fileupload'
import OrderList from 'primevue/orderlist'
import { useConfirm } from 'primevue/useconfirm'

import { get_route_to_upload_images as imageUploadRoute } from '@/api/routes/images/manager'

import { getCookie } from '@/helpers'

import moment from 'moment/moment'

export default {
  components: {
    ConfirmDialog,
    FileUpload,
    OrderList,
    PrimeGalleria,
    PrimeButton,
    Skeleton
  },
  setup() {
    const route = useRoute()

    const confirm = useConfirm()

    const imagesStore = useImagesStore()

    const selectedImages = ref([])

    onMounted(() => {
      getImages()
    })

    watch(
      () => imagesStore.status,
      (newStatus) => {
        if (newStatus === 'removed') {
          getImages()
          selectedImages.value = []
        }
      }
    )

    function getImages() {
      imagesStore.getImages(route.params.id, route.params.type)
    }

    function getImageUploadRoute() {
      return imageUploadRoute(route.params.id, route.params.type)
    }

    function beforeSend(request) {
      request.xhr.setRequestHeader('Accept', 'application/json')
      request.xhr.setRequestHeader('X-XSRF-TOKEN', getCookie('XSRF-TOKEN'))
    }

    function prepareDataToChangeOrder() {
      let order = {}
      imagesStore.images.forEach((image, index) => {
        order[image['id']] = index + 1
      })

      return order
    }

    function getIdsToDelete() {
      let ids = []
      selectedImages.value.forEach((image) => {
        ids.push(image['id'])
      })

      return ids
    }

    function changeOrder() {
      imagesStore.chageOrderOfImages(route.params.type, {
        order: prepareDataToChangeOrder()
      })
    }

    function onUploadCompleted() {
      getImages()
    }

    function removeAll() {
      confirm.require({
        message: 'This will remove all selected images ?',
        header: 'Delete Confirmation',
        icon: 'pi pi-info-circle',
        acceptClass: 'p-button-danger',

        accept: () => {
          imagesStore.removeImages(route.params.type, {
            ids: getIdsToDelete()
          })
        },
        reject: () => {}
      })
    }

    return {
      imagesStore,
      route,
      selectedImages,
      getImageUploadRoute,
      beforeSend,
      changeOrder,
      removeAll,
      onUploadCompleted,
      moment
    }
  }
}
</script>
