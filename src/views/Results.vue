<template>
  <main>
    <article>{{ JSON.stringify(dataset) }}</article>
  </main>
</template>

<script>
export default {
  name: "ResultsView",
  data() {
    return {
      dataset: JSON.parse(localStorage.getItem("dataset")),
    };
  },
  beforeCreate() {
    if (!localStorage.getItem("dataset")) {
      this.$router.push("/");
    }
  },
};
</script>

<style scoped lang="scss"></style>
