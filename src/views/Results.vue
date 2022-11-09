<template>
  <main>
    <article class="content">
      <section>
        <p>
          {{ dataset.information.nom_departement }} ({{
            dataset.information.code_departement
          }}) - {{ dataset.information.nom_region }}
        </p>
        <h2>
          {{ dataset.information.nom_commune }} ({{
            dataset.information.code_postal
          }})
          <font-awesome-icon
            icon="fa-solid fa-arrow-up-right-from-square"
            @click="changeCity"
          />
        </h2>
        <p>
          Données mises à jour le :
          {{
            new Date(dataset.ecowatt.updated_at * 1000).toLocaleString("fr-FR")
          }}
        </p>
      </section>
      <section>
        <label for="sector">Secteur sélectionné</label>
        <select name="sector" id="sector" v-model="sector"></select>
      </section>
    </article>
    <h3 class="content">Tous les voyants sont au vert !</h3>
    <article class="content">
      <section>
        <h4>Consommation totale</h4>
        <Bar :chart-options="chart.options" :chart-data="chart.total" />
      </section>
    </article>
    <pre class="content">{{ JSON.stringify(dataset, null, 4) }}</pre>
  </main>
</template>

<script>
import { Bar } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
);

export default {
  name: "ResultsView",
  components: { Bar },
  data() {
    return {
      dataset: JSON.parse(localStorage.getItem("dataset")),
      selection: null,
      sector: "",
      chart: {
        total: {
          labels: [],
          datasets: [
            {
              data: [],
            },
          ],
        },
        options: {
          responsive: true,
        },
      },
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

    let labels = [];
    Object.keys(this.dataset.data).forEach((i) => {
      labels.push(this.dataset.data[i].annee);
    });
    this.chart.total.labels = labels;
  },
  methods: {
    changeCity() {
      alert("changement de commune");
    },
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

      let total_conso = [];
      let total_sites = [];
      Object.keys(this.selection).forEach((i) => {
        if (this.selection[i].records[0]) {
          total_conso.push(this.selection[i].records[0].conso_totale_mwh);
          total_sites.push(this.selection[i].records[0].nb_sites);
        } else {
          total_conso.push(null);
          total_sites.push(null);
        }
      });
      this.chart.total.datasets = [
        {
          label: "Consommation totale",
          data: total_conso,
          backgroundColor: "rgb(29,140,248)",
        },
        {
          label: "Nombre de sites",
          data: total_sites,
          backgroundColor: "000",
        },
      ];
    },
  },
};
</script>

<style scoped lang="scss">
article:first-of-type {
  background-color: var(--secondary-bg-color);
  border-radius: 16px;
  margin: 2.6rem auto;
  padding: 1.6rem;
  border: 4px solid var(--green-touch);
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
  gap: 3rem;

  section:first-of-type {
    display: flex;
    flex-flow: column nowrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.3rem;

    p:first-of-type {
      font-size: 1.1em;
    }

    h2 {
      display: flex;
      align-items: center;
      gap: 0.6rem;

      svg {
        cursor: pointer;
      }
    }

    p:last-of-type {
      font-size: 0.8em;
      color: darkgrey;
    }
  }

  section:last-of-type {
    display: flex;
    flex-flow: column nowrap;
    align-items: flex-end;
    justify-content: space-between;
    gap: 0.4rem;

    select {
      width: 300px;
      padding: 0.5rem;
      border-radius: 8px;
      outline: none;
      border: none;
      font-size: 1em;
    }
  }
}

h3 {
  font-size: 1.5em;
  text-align: center;
}

article:nth-of-type(2) {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2.6rem;
  margin: 2.6rem auto;

  section {
    border-top: 4px solid var(--green-touch);
    padding: 1rem;
    background-color: var(--secondary-bg-color);
    border-radius: 16px;

    h4 {
      font-size: 1.2em;
      margin-bottom: 0.7rem;
    }
  }
}
</style>
