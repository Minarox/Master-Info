<template>
  <main>
  </main>
</template>

<script>
import { API } from "@/assets/js/api";

export default {
  name: "Logout",
  mounted() {
    API.logout().then(() => {
      this.$router.push("/login").then();
    }).catch(() => {
      this.$router.push("/login").then();
    });
  },
};
</script>
