<template>
  <div>
    <Header />
  </div>
</template>

<script>
import Header from "../components/global/Header";
import { API } from "../assets/js/api";

export default {
  name: "Logout",
  components: {
    Header,
  },
  mounted() {
    API.logout().then(() => {
      this.$router.push("/login");
    });
  },
};
</script>
