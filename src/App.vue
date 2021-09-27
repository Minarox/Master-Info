<template>
  <navbar />
  <router-view />
</template>

<script>
import navbar from "@/components/navbar";

export default {
  name: 'App',
  components: {
    navbar
  }
}
</script>
