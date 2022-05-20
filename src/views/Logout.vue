<template>
  <Header />
</template>

<script>
import { API } from "@/assets/js/api";
import Header from "@/components/global/Header";

export default {
  name: "Logout",
  components: {
    Header
  },
  mounted() {
    API.logout().then(() => {
      this.$router.push("/login").then();
    }).catch(() => {
      this.$router.push("/login").then();
    });
  },
};
</script>
