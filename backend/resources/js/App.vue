<template>
  <router-view></router-view>
  <GlobalModal />
  <GlobalToast />
</template>

<script setup>
import GlobalModal from './components/GlobalModal.vue'
import GlobalToast from './components/GlobalToast.vue'
</script>

<style>
</style>
