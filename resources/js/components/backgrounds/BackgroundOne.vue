<template>
  <section class="awsom-login-section">
    <span v-for="n in 1000" :key="n"></span>
  </section>
</template>

<style scoped>
section {
  position: absolute;
  height: 100vh;
  width: 100vw;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 2px;
  overflow: hidden;
  background: #000;
}

section::before {
  position: absolute;
  content: '';
  width: 100%;
  height: 100%;
  background: linear-gradient(#000, #0f0, #000);
  animation: animate-background 3.5s linear infinite;
}

section span {
  position: relative;
  display: block;
  background: #181818;
  width: calc(6.25vw - 2px);
  height: calc(6.25vw - 2px);
  transition: 1.5s;
}

section span:hover {
  background: #0f0;
  transition: 0s;
}

@media only screen and (max-width: 900px) {
  section span {
    position: relative;
    display: block;
    width: calc(10vw - 2px);
    height: calc(10vw - 2px);
  }
}

@media only screen and (max-width: 600px) {
  section span {
    position: relative;
    display: block;
    width: calc(20vw - 2px);
    height: calc(20vw - 2px);
  }
}

@keyframes animate-background {
  0% {
    transform: translateY(-100%);
  }

  100% {
    transform: translateY(100%);
  }
}
</style>
