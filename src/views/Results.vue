<template>
  <main>
    <pre>{{ JSON.stringify(dataset, null, 4) }}</pre>
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
  mounted() {},
};
</script>

<style scoped lang="scss"></style>
