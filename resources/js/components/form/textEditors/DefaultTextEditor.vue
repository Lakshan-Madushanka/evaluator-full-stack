<template>
  <Editor>
    <template #toolbar>
      <div class="flex justify-between flex-wrap">
        <span class="ql-formats">
          <select class="ql-header"></select>
          <select class="ql-size"></select>
        </span>
        <span class="ql-formats">
          <button v-tooltip.bottom="'Bold'" class="ql-bold"></button>
          <button v-tooltip.bottom="'Italic'" class="ql-italic"></button>
          <button v-tooltip.bottom="'Strike line'" class="ql-strike"></button>
          <button v-tooltip.bottom="'Underline'" class="ql-underline"></button>
          <select v-tooltip.bottom="'Text color'" class="ql-color"></select>
          <button v-tooltip.bottom="'Block Quote'" class="ql-blockquote"></button>
          <button v-tooltip.bottom="'Sub'" class="ql-script" value="sub"></button>
          <button v-tooltip.bottom="'Super'" class="ql-script" value="super"></button>
        </span>
        <span class="ql-formats">
          <button v-tooltip.bottom="'Ordered list'" class="ql-list" value="ordered"></button>
          <button v-tooltip.bottom="'Bullet list'" class="ql-list" value="bullet"></button>
          <select v-tooltip.bottom="'Text align'" class="ql-align"></select>
        </span>

        <span class="ql-formats">
          <button v-tooltip.bottom="'Code'" class="ql-code"></button>
          <button v-tooltip.bottom="'Code block'" class="ql-code-block"></button>
        </span>

        <span class="ql-formats">
          <button v-tooltip.bottom="'Reset text'" class="ql-clean"></button>
        </span>
      </div>
    </template>
  </Editor>
</template>

<script>
import Editor from 'primevue/editor'

export default {
  components: { Editor },
  setup() {}
}
</script>
