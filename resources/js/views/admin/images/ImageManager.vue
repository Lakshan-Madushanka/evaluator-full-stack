<template>
  <ConfirmDialog />

  <Dialog
    v-model:visible="showCropImageDialog"
    modal
    header="Crop Image"
    :style="{ width: '50rem' }"
    :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
  >
    <Cropper
      ref="cropper"
      class="cropper"
      :src="fileUpload.files[activeCropImageIndex]['objectURL']"
    />

    <template #footer>
      <div class="flex gap-4 flex-wrap mt-4">
        <PrimeButton @click="showCropImageDialog = false" severity="secondary" label="Cancel" />
        <PrimeButton @click="crop" label="Crop" :loading="isCropping" />
      </div>
    </template>
  </Dialog>

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
        ref="fileUpload"
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
        <template #content="{ files, uploadedFiles, removeUploadedFileCallback, messages }">
          <div class="flex flex-col gap-8 pt-4">
            <Message
              v-for="message of messages"
              :key="message"
              :class="{ 'mb-8': !files.length && !uploadedFiles.length }"
              severity="error"
            >
              {{ message }}
            </Message>

            <div v-if="files.length > 0">
              <h5>Pending</h5>
              <div class="flex flex-wrap gap-4">
                <div
                  v-for="(file, index) of fileUpload.files"
                  :key="file.name + file.type + file.size"
                  class="p-8 rounded-border flex flex-col border border-surface items-center gap-4"
                >
                  <div>
                    <Image
                      :src="file.objectURL"
                      alt="Image"
                      imageClass="!max-w-[200px] !max-h-[100px]"
                      preview
                    />
                  </div>
                  <span
                    class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden"
                    >{{ file.name }}</span
                  >
                  <div>{{ formatFileSize(file.size) }}</div>
                  <Badge value="Pending" severity="warn" />
                  <div class="flex gap-2 flex-wrap">
                    <PrimeButton
                      @click="setShowCropImageDialog(index)"
                      icon="pi pi-pencil"
                      severity="info"
                      variant="outlined"
                      rounded
                    ></PrimeButton>
                    <PrimeButton
                      icon="pi pi-times"
                      @click="removeUploadedImage(index)"
                      variant="outlined"
                      rounded
                      severity="danger"
                    />
                  </div>
                </div>
              </div>
            </div>

            <div v-if="uploadedFiles.length > 0">
              <h5>Completed</h5>
              <div class="flex flex-wrap gap-4">
                <div
                  v-for="(file, index) of uploadedFiles"
                  :key="file.name + file.type + file.size"
                  class="p-8 rounded-border flex flex-col border border-surface items-center gap-4"
                >
                  <div>
                    <img
                      role="presentation"
                      :alt="file.name"
                      :src="file.objectURL"
                      width="100"
                      height="50"
                    />
                  </div>
                  <span
                    class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden"
                    >{{ file.name }}</span
                  >
                  <div>{{ formatFileSize(file.size) }}</div>
                  <Badge value="Completed" class="mt-4" severity="success" />
                  <PrimeButton
                    icon="pi pi-times"
                    @click="removeUploadedFileCallback(index)"
                    variant="outlined"
                    rounded
                    severity="danger"
                  />
                </div>
              </div>
            </div>
          </div>
        </template>
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
import { onMounted, ref, watch } from 'vue'

import { useRoute } from 'vue-router'

import { useImagesStore } from '@/stores/images/manager'

import Badge from 'primevue/badge'
import ConfirmDialog from 'primevue/confirmdialog'
import Dialog from 'primevue/dialog'
import PrimeGalleria from 'primevue/galleria'
import PrimeButton from 'primevue/button'
import Skeleton from 'primevue/skeleton'
import FileUpload from 'primevue/fileupload'
import Image from 'primevue/image'
import Message from 'primevue/message'
import OrderList from 'primevue/orderlist'
import { useConfirm } from 'primevue/useconfirm'

import { Cropper } from 'vue-advanced-cropper'

import { get_route_to_upload_images as imageUploadRoute } from '@/api/routes/images/manager'

import { formatFileSize, getCookie } from '@/helpers'

import moment from 'moment/moment'

import 'vue-advanced-cropper/dist/style.css'

export default {
  components: {
    Cropper,
    Badge,
    ConfirmDialog,
    Dialog,
    FileUpload,
    Image,
    OrderList,
    PrimeGalleria,
    PrimeButton,
    Message,
    Skeleton
  },
  setup() {
    const route = useRoute()

    const confirm = useConfirm()

    const imagesStore = useImagesStore()

    const fileUpload = ref({ files: [] })

    const cropper = ref()
    const isCropping = ref(false)
    const activeCropImageIndex = ref()
    const showCropImageDialog = ref(false)

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

    watch(
      () => fileUpload.value.files,
      (files) => {
        files.forEach((file, index) => {
          showCropImgViewFor[index] = false
        })
      }
    )

    function getImages() {
      imagesStore.getImages(route.params.id, route.params.type)
    }

    function getImageUploadRoute() {
      return imageUploadRoute(route.params.id, route.params.type)
    }

    function setShowCropImageDialog(index) {
      showCropImageDialog.value = true
      activeCropImageIndex.value = index
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

    function removeUploadedImage(index) {
      fileUpload.value.files.splice(index, 1)
    }

    async function crop(data) {
      isCropping.value = true

      const canvas = cropper.value.getResult().canvas
      if (!canvas) return

      // Convert canvas to Blob
      canvas.toBlob(
        (blob) => {
          if (blob) {
            const croppedFile = new File(
              [blob],
              fileUpload.value.files[activeCropImageIndex.value].name,
              {
                type: fileUpload.value.files[activeCropImageIndex.value].type
              }
            )

            croppedFile.objectURL = URL.createObjectURL(croppedFile)
            fileUpload.value.files[activeCropImageIndex.value] = croppedFile

            isCropping.value = false
            showCropImageDialog.value = false
          }
        },
        fileUpload.value.files[activeCropImageIndex.value].type,
        0.9
      )
    }

    return {
      imagesStore,
      route,
      fileUpload,
      cropper,
      isCropping,
      activeCropImageIndex,
      showCropImageDialog,
      selectedImages,
      getImageUploadRoute,
      setShowCropImageDialog,
      beforeSend,
      changeOrder,
      removeAll,
      onUploadCompleted,
      moment,
      removeUploadedImage,
      crop,
      formatFileSize
    }
  }
}
</script>
