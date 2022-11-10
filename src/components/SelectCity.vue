<template>
  <form @submit.prevent="selectCity">
    <h3>Choisissez une commune</h3>
    <div>
      <label for="postal_code">Code postal</label>
      <input
        type="number"
        name="postal_code"
        id="postal_code"
        v-model="postal_code"
      />
    </div>
    <div>
      <label for="city">Commune</label>
      <select name="city" id="city" v-model="index" disabled></select>
    </div>
    <span v-if="loading">
      <button type="submit" id="submit" disabled>
        <font-awesome-icon icon="fa-solid fa-circle-notch" id="spin" />
      </button>
    </span>
    <span v-else>
      <button type="submit" id="submit" disabled>Valider</button>
    </span>
  </form>
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

        // Parse and rearrange data
        let parsed_data = [];
        let sectors = [];
        Object.keys(request).forEach((i) => {
          let registered = false;
          Object.keys(parsed_data).forEach((y) => {
            if (parsed_data[y].annee === request[i].annee) {
              registered = true;
            }
          });
          if (!registered) {
            parsed_data.push({
              annee: request[i].annee,
              records: [],
            });
          }

          registered = false;
          Object.keys(sectors).forEach((y) => {
            if (sectors[y] === request[i].code_grand_secteur) {
              registered = true;
            }
          });
          if (!registered) {
            sectors.push(request[i].code_grand_secteur);
          }
        });

        Object.keys(parsed_data).forEach((i) => {
          Object.keys(request).forEach((y) => {
            let index = false;
            if (request[y].annee === parsed_data[i].annee) {
              Object.keys(parsed_data[i].records).forEach((z) => {
                if (
                  request[y].code_grand_secteur ===
                  parsed_data[i].records[z].code_grand_secteur
                ) {
                  index = z;
                }
              });

              delete request[y].annee;
              delete request[y].code_categorie_consommation;
              delete request[y].code_secteur_naf2;

              if (index) {
                let skip = true;
                Object.keys(parsed_data[i].records[index]).forEach((z) => {
                  if (skip) {
                    skip = false;
                  } else {
                    parsed_data[i].records[index][z] =
                      parsed_data[i].records[index][z] + request[y][z];
                  }
                });
              } else {
                parsed_data[i].records.push(request[y]);
              }
            }
          });
        });

        // Fetch EcoWatt indicators from personal API
        let indicators = await fetch("https://api.minarox.fr/rte");
        indicators = await indicators.json();

        // Merge selected city and data from request
        const dataset = {
          information: information,
          sectors: sectors,
          data: parsed_data,
          ecowatt: indicators.ecowatt,
          conso: indicators.conso,
          updated_at: indicators.updated_at,
        };
        localStorage.setItem("dataset", JSON.stringify(dataset));
        if (this.$route.name === "results") {
          this.$router.go(0);
        } else {
          this.$router.push("/results");
        }
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

<style scoped lang="scss">
form {
  z-index: 1;
  padding: 2.5rem 3rem;
  background-color: var(--secondary-bg-color);
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
  border-radius: 16px;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);

  h3 {
    font-size: 1.4em;
    text-align: center;
  }

  div {
    display: flex;
    flex-flow: column nowrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;

    input,
    select {
      width: 500px;
      padding: 0.6rem;
      border-radius: 8px;
      outline: none;
      border: none;
      font-size: 1.1em;
    }
  }

  button {
    transition: all 250ms;
    padding: 0.5rem 2rem;
    border-radius: 8px;
    outline: none;
    border: none;
    font-size: 1.2em;
    background-color: var(--green-touch);
    font-weight: bold;
    cursor: pointer;
    color: var(--primary-bg-color);

    &:disabled {
      color: rgba(9, 8, 23, 0.5);
    }

    &:enabled:hover {
      transform: scale(1.05);
    }
  }
}

#spin {
  animation: spin 2.6s linear infinite;

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }
}
</style>
