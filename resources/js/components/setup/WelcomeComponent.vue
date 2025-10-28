<template>
  <div>
    <div class="flex flex-col items-center gap-8">
      <img :src="Logo" class="w-16 text-center" alt="logo" />
      <h2 class="text-2xl font-bold">Welcome to the Evaluator 🙏</h2>
      <p class="max-w-4xl text-lg">
        Evaluator is your all-in-one questionnaire management system for creating, delivering, and
        evaluating multiple-choice questionnaires with ease. Designed with a clean, modern interface
        and built-in security, it gives you everything you need to run assessments seamlessly.
      </p>
      <div class="flex gap-4">
        <PrimeButton
          as="a"
          label="Site"
          href="https://lakm.gitbook.io/evaluator/"
          target="_blank"
          rel="noopener"
          severity="info"
        />
        <PrimeButton
          as="a"
          label="Documentation"
          href="https://lakm.gitbook.io/evaluator/"
          target="_blank"
          rel="noopener"
          severity="info"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import Logo from '@/assets/logo.png'
import PrimeButton from 'primevue/button'
</script>
