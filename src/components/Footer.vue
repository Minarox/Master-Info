<template>
  <footer>
    © {{ new Date().getFullYear() }} |&nbsp;
    <router-link to="/">Localelec.fr</router-link>
  </footer>
</template>

<script>
export default {
  name: "FooterComponent",
};
</script>

<style scoped lang="scss">
footer {
  height: 70px;
  background-color: var(--primary-bg-color);
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  color: darkgray;
  font-size: 0.9em;

  a {
    text-decoration: none;
    color: silver;
  }
}
</style>
