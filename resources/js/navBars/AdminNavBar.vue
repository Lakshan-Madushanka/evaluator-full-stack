<template>
  <nav
    class="py-4 px-4 flex justify-between text-[#495057] dark:text-white bg-white dark:bg-gray-800 border border-solid dark:border-black rounded items-center"
  >
    <div>
      <Breadcrumb :home="home" :model="getItems()" />
    </div>
    <div class="flex justify-center items-center gap-x-4"><AuthUserMenu /><DarkModeSwitch /></div>
  </nav>
</template>

<script setup>
import Breadcrumb from 'primevue/breadcrumb'
import AuthUserMenu from '@/components/AuthUserMenu.vue'
import { useRoute } from 'vue-router'
import { useRouter } from 'vue-router'

import DarkModeSwitch from '@/components/DarkModeSwitch.vue'

const route = useRoute()
const router = useRouter()

const home = {
  icon: 'pi pi-th-large',
  command: () => {
    router.push({
      name: 'admin.dashboard'
    })
  }
}

function getItems() {
  switch (route.name) {
    // Teams
    case 'admin.teams.index':
      return [
        {
          label: 'teams',
          command: () => {
            router.push({ name: 'admin.teams.index' })
          }
        }
      ]
    case 'admin.teams.create':
      return [
        {
          label: 'teams',
          command: () => {
            router.push({ name: 'admin.teams.index' })
          }
        },
        {
          label: 'create',
          command: () => {
            router.push({ name: 'admin.teams.create' })
          }
        }
      ]
    case 'admin.teams.edit':
      return [
        {
          label: 'teams',
          command: () => {
            router.push({ name: 'admin.teams.index' })
          }
        },
        {
          label: 'edit',
          command: () => {
            router.push({ name: 'admin.teams.edit' })
          }
        }
      ]
    //Users
    case 'admin.teams.users.index':
      return [
        {
          label: 'teams',
          command: () => {
            router.push({ name: 'admin.teams.index' })
          }
        },
        {
          label: 'users',
          command: () => {
            router.push({ name: 'admin.teams.users.index' })
          }
        }
      ]
    // End of Users

    //Questionnaires
    case 'admin.teams.questionnaires.index':
      return [
        {
          label: 'teams',
          command: () => {
            router.push({ name: 'admin.teams.index' })
          }
        },
        {
          label: 'questionnaires',
          command: () => {
            router.push({ name: 'admin.teams.questionnaires.index' })
          }
        }
      ]
    //Questionnaires -> Users
    case 'admin.teams.questionnaires.users.index':
      return [
        {
          label: 'teams',
          command: () => {
            router.push({ name: 'admin.teams.index' })
          }
        },
        {
          label: 'questionnaires',
          command: () => {
            router.back()
          }
        },
        {
          label: 'results',
          command: () => {
            router.push({ name: 'admin.teams.questionnaires.users.index' })
          }
        }
      ]
    // End of Teams

    // Users
    case 'admin.users.index':
      return [
        {
          label: 'users',
          command: () => {
            router.push({ name: 'admin.users.index' })
          }
        }
      ]
    case 'admin.users.create':
      return [
        {
          label: 'users',
          command: () => {
            router.push({ name: 'admin.users.index' })
          }
        },
        {
          label: 'create',
          command: () => {
            router.push({ name: 'admin.users.create' })
          }
        }
      ]
    case 'admin.users.edit':
      return [
        {
          label: 'users',
          command: () => {
            router.push({
              name: 'admin.users.index'
            })
          }
        },
        {
          label: 'edit',
          command: () => {
            router.push({
              name: 'admin.users.edit'
            })
          }
        }
      ]
    // Questionnaires
    case 'admin.users.questionnaires.index':
      return [
        {
          label: 'users',
          command: () => {
            router.push({
              name: 'admin.users.index'
            })
          }
        },
        {
          label: 'questionnaires',
          command: () => {
            router.push({
              name: 'admin.users.questionnaires.index'
            })
          }
        }
      ]
    // Teams
    case 'admin.users.teams.index':
      return [
        {
          label: 'users',
          command: () => {
            router.push({
              name: 'admin.users.index'
            })
          }
        },
        {
          label: 'teams',
          command: () => {
            router.push({
              name: 'admin.users.teams.index'
            })
          }
        }
      ]
    // End Users

    // Categories
    case 'admin.categories.index':
      return [
        {
          label: 'categories',
          command: () => {
            router.push({ name: 'admin.categories.index' })
          }
        }
      ]
    case 'admin.categories.create':
      return [
        {
          label: 'categories',
          command: () => {
            router.push({ name: 'admin.categories.index' })
          }
        },
        {
          label: 'create',
          command: () => {
            router.push({ name: 'admin.categories.create' })
          }
        }
      ]
    case 'admin.categories.edit':
      return [
        {
          label: 'categories',
          command: () => {
            router.push({ name: 'admin.categories.index' })
          }
        },
        {
          label: 'edit',
          command: () => {
            router.push({ name: 'admin.categories.edit' })
          }
        }
      ]
    // Questionnaires
    case 'admin.questionnaires.index':
      return [
        {
          label: 'questionnaires',
          command: () => {
            router.push({ name: 'admin.questionnaires.index' })
          }
        }
      ]
    case 'admin.questionnaires.create':
      return [
        {
          label: 'questionnaires',
          command: () => {
            router.push({ name: 'admin.questionnaires.index' })
          }
        },
        {
          label: 'create',
          command: () => {
            router.push({ name: 'admin.questionnaires.create' })
          }
        }
      ]
    case 'admin.questionnaires.edit':
      return [
        {
          label: 'questionnaires',
          command: () => {
            router.push({ name: 'admin.questionnaires.index' })
          }
        },
        {
          label: 'edit',
          command: () => {
            router.push({ name: 'admin.questionnaires.edit' })
          }
        }
      ]
    // Questions
    case 'admin.questionnaires.questions.index':
      return [
        {
          label: 'questionnaires',
          command: () => {
            router.push({ name: 'admin.questionnaires.index' })
          }
        },
        {
          label: 'questions',
          command: () => {
            router.push({
              name: 'admin.questionnaires.questions.index',
              query: { ...route.query }
            })
          }
        }
      ]
    case 'admin.questionnaires.questions.show':
      return [
        {
          label: 'questionnaires',
          command: () => {
            router.push({ name: 'admin.questionnaires.index' })
          }
        },
        {
          label: 'questions',
          command: () => {
            router.push({
              name: 'admin.questionnaires.questions.show',
              query: { ...route.query }
            })
          }
        }
      ]
    // End Questionnaires

    // Questions
    case 'admin.questions.index':
      return [
        {
          label: 'questions',
          command: () => {
            router.push({ name: 'admin.questions.index' })
          }
        }
      ]
    case 'admin.questions.create':
      return [
        {
          label: 'questions',
          command: () => {
            router.push({ name: 'admin.questions.index' })
          }
        },
        {
          label: 'create',
          command: () => {
            router.push({ name: 'admin.questions.create' })
          }
        }
      ]
    case 'admin.questions.edit':
      return [
        {
          label: 'questions',
          command: () => {
            router.push({ name: 'admin.questions.index' })
          }
        },
        {
          label: 'edit',
          command: () => {
            router.push({ name: 'admin.questions.edit' })
          }
        }
      ]
    // Answers
    case 'admin.questions.answers.index':
      return [
        {
          label: 'questions',
          command: () => {
            router.push({ name: 'admin.questions.index' })
          }
        },
        {
          label: 'answers',
          command: () => {
            router.push({
              name: 'admin.questions.answers.index',
              query: { ...route.query }
            })
          }
        }
      ]
    // End of Answers
    // End Questions

    // Answers
    case 'admin.answers.index':
      return [
        {
          label: 'answers',
          command: () => {
            router.push({ name: 'admin.answers.index' })
          }
        }
      ]
    case 'admin.answers.create':
      return [
        {
          label: 'answers',
          command: () => {
            router.push({ name: 'admin.answers.index' })
          }
        },
        {
          label: 'create',
          command: () => {
            router.push({ name: 'admin.answers.create' })
          }
        }
      ]
    case 'admin.answers.edit':
      return [
        {
          label: 'answers',
          command: () => {
            router.push({ name: 'admin.answers.index' })
          }
        },
        {
          label: 'edit',
          command: () => {
            router.push({ name: 'admin.answers.edit' })
          }
        }
      ]
    // End Answers

    // Evaluations
    case 'admin.evaluations.index':
      return [
        {
          label: 'evaluations',
          command: () => {
            router.push({ name: 'admin.evaluations.index' })
          }
        }
      ]
    case 'admin.evaluations.show':
      return [
        {
          label: 'evaluations',
          command: () => {
            router.push({ name: 'admin.evaluations.index' })
          }
        }
      ]
    // End of Evaluations

    // Image Manager
    case 'admin.images.manager':
      return [
        {
          label: 'questions',
          command: () => {
            router.push({ name: `admin.${route.params.type}.index` })
          }
        },
        {
          label: 'images',
          command: () => {
            router.push({
              name: 'admin.images.manager',
              query: { ...route.query }
            })
          }
        }
      ]
    // End of image manager
    default:
      return null
  }
}
</script>
