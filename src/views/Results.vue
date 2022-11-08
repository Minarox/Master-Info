<template>
  <main>
    <form>
      <select name="sector" id="sector" v-model="sector"></select>
    </form>
    <pre>{{ JSON.stringify(selection, null, 4) }}</pre>
  </main>
</template>

<script>
export default {
  name: "ResultsView",
  data() {
    return {
      dataset: JSON.parse(localStorage.getItem("dataset")),
      selection: null,
      sector: "",
    };
  },
  beforeCreate() {
    if (!localStorage.getItem("dataset")) {
      this.$router.push("/");
    }
  },
  mounted() {
    let select = document.getElementById("sector");
    for (let i = 0; i < this.dataset.sectors.length; i++) {
      const option = document.createElement("option");
      option.value = option.text = this.dataset.sectors[i];
      select.add(option);
    }
    this.sector = this.dataset.sectors[0];
  },
  watch: {
    sector() {
      this.selection = Object.assign(
        {},
        JSON.parse(localStorage.getItem("dataset")).data
      );
      Object.keys(this.selection).forEach((i) => {
        Object.keys(this.selection[i].records).forEach((y) => {
          if (this.selection[i].records[y].code_grand_secteur !== this.sector) {
            delete this.selection[i].records[y];
          }
        });
        this.selection[i].records = this.selection[i].records.filter(
          (el) => el !== null
        );
      });
    },
  },
};
</script>

<style scoped lang="scss"></style>
