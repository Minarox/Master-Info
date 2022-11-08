<template>
  <article class="content">
    <form @submit.prevent="selectCity">
      <input
        type="number"
        name="postal_code"
        id="postal_code"
        v-model="postal_code"
      />
      <select name="city" id="city" v-model="index" disabled></select>
      <button type="submit" id="submit" disabled>Confirmer</button>
    </form>
  </article>
</template>

<script>
import codes from "@/assets/json/codes_insee.json";

export default {
  name: "SelectCityComponent",
  data() {
    return {
      postal_code: "",
      index: 0,
      city: {},
      loading: false,
      dom: {
        input: {},
        select: {},
        button: {},
      },
    };
  },
  mounted() {
    this.dom.input = document.getElementById("postal_code");
    this.dom.select = document.getElementById("city");
    this.dom.button = document.getElementById("submit");
  },
  methods: {
    async selectCity() {
      // Disable DOM elemnts
      this.dom.input.disabled = true;
      this.dom.select.disabled = true;
      this.dom.button.disabled = true;
      this.loading = true;

      // Get selected INSEE code
      const insee_code = this.city[+this.index].code_insee.toLocaleString(
        "fr-FR",
        {
          minimumIntegerDigits: 5,
          useGrouping: false,
        }
      );

      // Get JSON data from Enedis API
      let request = await fetch(
        "https://data.enedis.fr/api/v2/catalog/datasets/consommation-electrique-par-secteur-dactivite-commune/exports/json?where=code_commune%20%3D%20" +
          insee_code +
          "%20AND%20nb_sites%20%3E%200&order_by=annee&limit=-1&offset=0&timezone=UTC"
      );
      request = await request.json();

      // Check if data is fetched
      if (request.length === 0) {
        // Enable DOM elements and show error message
        this.dom.input.disabled = false;
        this.dom.select.disabled = false;
        this.dom.button.disabled = false;
        this.loading = false;
        // TODO: Error message
      } else {
        // Cleaning data from Enedis
        const latest_data = request[request.length - 1];
        const information = {
          nom_commune: latest_data.nom_commune,
          nom_departement: latest_data.nom_departement,
          nom_region: latest_data.nom_region,
          code_insee: this.city[this.index].code_insee,
          code_postal: this.city[this.index].code_postal,
          code_departement: latest_data.code_departement,
          code_region: latest_data.code_region,
          centroid: latest_data.centroid,
        };
        request.filter((el) => {
          delete el.nom_commune;
          delete el.nom_departement;
          delete el.nom_region;
          delete el.code_commune;
          delete el.code_departement;
          delete el.code_region;
          delete el.code_epci;
          delete el.nom_epci;
          delete el.type_epci;
          delete el.centroid;
          delete el.geom;
        });

        // Merge selected city and data from request
        const dataset = {
          information: information,
          data: request,
        };
        localStorage.setItem("dataset", JSON.stringify(dataset));
        this.$router.push("/results");
      }
    },
  },
  watch: {
    postal_code() {
      // Get city with postal code
      this.city = codes.filter((el) => el.code_postal === this.postal_code);

      // Remove old option in select and prevent actions
      while (this.dom.select.firstChild) {
        this.dom.select.removeChild(this.dom.select.firstChild);
      }
      this.dom.select.disabled = true;
      this.dom.button.disabled = true;

      // Add option(s) in select
      if (this.city.length > 1) {
        for (let i = 0; i < this.city.length; i++) {
          const option = document.createElement("option");
          option.value = i;
          option.text = this.city[i].nom_commune;
          this.dom.select.add(option);
        }
        this.dom.select.disabled = false;
        this.dom.button.disabled = false;
      } else if (this.city.length === 1) {
        const option = document.createElement("option");
        option.value = 0;
        option.text = this.city[0].nom_commune;
        this.dom.select.add(option);
        this.dom.button.disabled = false;
      }
    },
  },
};
</script>

<style scoped lang="scss"></style>
