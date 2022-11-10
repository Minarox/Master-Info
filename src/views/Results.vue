<template>
  <main>
    <TransitionGroup name="fade">
      <article id="popup" v-if="switch_city">
        <SelectCity />
        <font-awesome-icon
          icon="fa-solid fa-xmark"
          @click="switch_city = false"
        />
        <span @click="switch_city = false"></span>
      </article>
    </TransitionGroup>
    <article id="city" class="content">
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
            @click="switch_city = true"
          />
        </h2>
        <p>
          Données mises à jour le :
          {{ new Date(dataset.updated_at * 1000).toLocaleString("fr-FR") }}
        </p>
      </section>
      <section>
        <label for="sector">Secteur sélectionné</label>
        <select name="sector" id="sector" v-model="sector"></select>
      </section>
    </article>
    <h3 class="content">Tous les voyants sont au vert !</h3>
    <article id="conso" class="content">
      <section>
        <h4>Consommation moyenne (MW/h)</h4>
        <div>
          <Chart
            chart_id="avg_conso"
            type="line"
            :labels="chart.avg_conso.labels"
            :data="chart.avg_conso.data"
            :colors="[
              [2, 240, 198],
              [220, 220, 220],
            ]"
            :multi="true"
          />
        </div>
      </section>
      <section>
        <h4>Consommation totale (MW/h)</h4>
        <div>
          <Chart
            chart_id="total_conso"
            type="line"
            :labels="chart.total_conso.labels"
            :data="chart.total_conso.data"
            :colors="[
              [2, 240, 198],
              [220, 220, 220],
            ]"
            :multi="true"
          />
        </div>
      </section>
    </article>
    <article
      id="usages"
      class="content"
      :class="sector === 'RESIDENTIEL' ? '' : 'hidden'"
    >
      <section>
        <h4>Consommation moyenne des usages (MW/h)</h4>
        <div>
          <Chart
            chart_id="avg_usage"
            type="bar"
            :labels="chart.avg_usage.labels"
            :data="chart.avg_usage.data"
            :colors="[
              [242, 121, 15],
              [2, 141, 240],
            ]"
          />
        </div>
      </section>
      <section>
        <h4>Consommation totale des usages (MW/h)</h4>
        <div>
          <Chart
            chart_id="total_usage"
            type="bar"
            :labels="chart.total_usage.labels"
            :data="chart.total_usage.data"
            :colors="[
              [242, 121, 15],
              [2, 141, 240],
            ]"
          />
        </div>
      </section>
      <section>
        <h4>
          Superficie des logements<br />
          (m²)
        </h4>
        <div>
          <Chart
            chart_id="area"
            type="bar"
            :labels="chart.area.labels"
            :data="chart.area.data"
            :stacked="true"
            :colors="[
              [230, 57, 70],
              [242, 151, 15],
              [140, 242, 15],
              [2, 240, 198],
              [2, 141, 240],
              [151, 15, 242],
            ]"
          />
        </div>
      </section>
    </article>
    <hr class="content" />
    <article id="ecowatt" class="content">
      <h4>Signaux EcoWatt</h4>
      <div>
        <section>
          <div
            v-for="signal of dataset.ecowatt[0].values"
            :key="signal.pas"
            :class="color(signal.hvalue)"
          ></div>
        </section>
        <section>
          <p>0h</p>
          <p>2h</p>
          <p>4h</p>
          <p>6h</p>
          <p>8h</p>
          <p>10h</p>
          <p>12h</p>
          <p>14h</p>
          <p>16h</p>
          <p>18h</p>
          <p>20h</p>
          <p>22h</p>
          <p>24h</p>
        </section>
      </div>
      <section>
        <div
          v-for="date of dataset.ecowatt"
          :key="date.jour"
          :class="color(date.dvalue)"
        >
          <h4>
            {{
              new Intl.DateTimeFormat("fr-FR", { weekday: "long" }).format(
                new Date(date.jour)
              )
            }}
          </h4>
          <p>
            {{
              new Intl.DateTimeFormat("fr-FR", {
                day: "numeric",
                month: "long",
              }).format(new Date(date.jour))
            }}
          </p>
        </div>
      </section>
    </article>
    <article id="realtime" class="content">
      <section>
        <h4>Consommation réelle et prévue en France (MW)</h4>
        <div>
          <Chart
            chart_id="realtime_conso"
            type="line"
            :labels="chart.conso.labels"
            :data="chart.conso.data"
            :colors="[
              [2, 240, 198],
              [120, 120, 120],
              [242, 151, 15],
              [230, 57, 70],
            ]"
          />
        </div>
      </section>
    </article>
    <!--<pre class="content">{{ JSON.stringify(dataset, null, 4) }}</pre>-->
  </main>
</template>

<script>
import SelectCity from "@/components/SelectCity";
import Chart from "@/components/Chart";

export default {
  name: "ResultsView",
  components: {
    SelectCity,
    Chart,
  },
  data() {
    return {
      switch_city: false,
      dataset: JSON.parse(localStorage.getItem("dataset")),
      selection: null,
      sector: "",
      chart: {
        avg_conso: {
          labels: [],
          data: [],
        },
        total_conso: {
          labels: [],
          data: [],
        },
        avg_usage: {
          labels: [],
          data: [],
        },
        total_usage: {
          labels: [],
          data: [],
        },
        area: {
          labels: [],
          data: [],
        },
        conso: {
          labels: [],
          data: [],
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
    this.chart.avg_conso.labels = this.chart.total_conso.labels = labels;

    let date = [];
    let realized = [];
    let guest = [];
    let d_1 = [];
    let d_2 = [];
    Object.keys(this.dataset.conso[0].values).forEach((i) => {
      realized.push(this.dataset.conso[0].values[i].value);
    });
    Object.keys(this.dataset.conso[1].values).forEach((i) => {
      guest.push(this.dataset.conso[1].values[i].value);
      d_1.push(null);
      d_2.push(null);
      date.push(
        new Date(this.dataset.conso[1].values[i].start_date).toLocaleString(
          "fr-FR"
        )
      );
    });
    Object.keys(this.dataset.conso[2].values).forEach((i) => {
      d_1.push(this.dataset.conso[2].values[i].value);
      d_2.push(null);
      date.push(
        new Date(this.dataset.conso[2].values[i].start_date).toLocaleString(
          "fr-FR"
        )
      );
    });
    Object.keys(this.dataset.conso[3].values).forEach((i) => {
      d_2.push(this.dataset.conso[3].values[i].value);
      date.push(
        new Date(this.dataset.conso[3].values[i].start_date).toLocaleString(
          "fr-FR"
        )
      );
    });
    this.chart.conso.labels = date;
    this.chart.conso.data = [
      {
        label: "Consommation réalisée",
        data: realized,
      },
      {
        label: "Consommation prévue",
        data: guest,
      },
      {
        label: "Consommation prévue (J+1)",
        data: d_1,
      },
      {
        label: "Consommation prévue (J+2)",
        data: d_2,
      },
    ];
  },
  methods: {
    color(signal) {
      switch (signal) {
        case 1:
          return "green";
        case 2:
          return "yellow";
        default:
          return "red";
      }
    },
  },
  watch: {
    switch_city() {
      if (this.switch_city) {
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "overlay";
      }
    },
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
      let avg_conso = [];
      let total_usage_thermo = [];
      let total_usage_non_thermo = [];
      let avg_usage_thermo = [];
      let avg_usage_non_thermo = [];
      let superficie_30 = [];
      let superficie_30_40 = [];
      let superficie_40_60 = [];
      let superficie_60_80 = [];
      let superficie_80_100 = [];
      let superficie_100 = [];
      let year_1 = [];
      let year_2 = [];
      let nb_sites = [];
      Object.keys(this.selection).forEach((i) => {
        if (this.selection[i].records[0]) {
          total_conso.push(this.selection[i].records[0].conso_totale_mwh);
          avg_conso.push(this.selection[i].records[0].conso_moyenne_mwh);
          nb_sites.push(this.selection[i].records[0].nb_sites);
          if (
            this.sector === "RESIDENTIEL" &&
            this.selection[i].records[0]
              .conso_totale_usages_thermosensibles_mwh !== null
          ) {
            year_1.push(this.selection[i].annee);
            total_usage_thermo.push(
              this.selection[i].records[0]
                .conso_totale_usages_thermosensibles_mwh
            );
            total_usage_non_thermo.push(
              this.selection[i].records[0]
                .conso_totale_usages_non_thermosensibles_mwh
            );
            avg_usage_thermo.push(
              this.selection[i].records[0]
                .conso_moyenne_usages_thermosensibles_mwh
            );
            avg_usage_non_thermo.push(
              this.selection[i].records[0]
                .conso_moyenne_usages_non_thermosensibles_mwh
            );
            if (
              this.sector === "RESIDENTIEL" &&
              this.selection[i].records[0].superficie_des_logements_30_m2 !==
                null
            ) {
              year_2.push(this.selection[i].annee);
              superficie_30.push(
                this.selection[i].records[0].superficie_des_logements_30_m2
              );
              superficie_30_40.push(
                this.selection[i].records[0].superficie_des_logements_30_a_40_m2
              );
              superficie_40_60.push(
                this.selection[i].records[0].superficie_des_logements_40_a_60_m2
              );
              superficie_60_80.push(
                this.selection[i].records[0].superficie_des_logements_60_a_80_m2
              );
              superficie_80_100.push(
                this.selection[i].records[0]
                  .superficie_des_logements_80_a_100_m2
              );
              superficie_100.push(
                this.selection[i].records[0].superficie_des_logements_100_m2
              );
            }
          }
        } else {
          total_conso.push(null);
          avg_conso.push(null);
          nb_sites.push(null);
        }
      });
      this.chart.total_conso.data = [
        {
          label: "Consommation totale",
          data: total_conso,
          yAxisID: "y",
        },
        {
          label: "Nombre de sites",
          data: nb_sites,
          yAxisID: "y1",
        },
      ];
      this.chart.avg_conso.data = [
        {
          label: "Consommation moyenne",
          data: avg_conso,
          yAxisID: "y",
        },
        {
          label: "Nombre de sites",
          data: nb_sites,
          yAxisID: "y1",
        },
      ];
      this.chart.total_usage.labels = year_1;
      this.chart.total_usage.data = [
        {
          label: "Usages thermosensibles",
          data: total_usage_thermo,
        },
        {
          label: "Usages non thermosensibles",
          data: total_usage_non_thermo,
        },
      ];
      this.chart.avg_usage.labels = year_1;
      this.chart.avg_usage.data = [
        {
          label: "Usages thermosensibles",
          data: avg_usage_thermo,
        },
        {
          label: "Usages non thermosensibles",
          data: avg_usage_non_thermo,
        },
      ];
      this.chart.area.labels = year_2;
      this.chart.area.data = [
        {
          label: "Moins de 30m²",
          data: superficie_30,
        },
        {
          label: "De 30 à 40m²",
          data: superficie_30_40,
        },
        {
          label: "De 40 à 60m²",
          data: superficie_40_60,
        },
        {
          label: "De 60 à 80m²",
          data: superficie_60_80,
        },
        {
          label: "De 80 à 100m²",
          data: superficie_80_100,
        },
        {
          label: "Plus de 100m²",
          data: superficie_100,
        },
      ];
    },
  },
};
</script>

<style scoped lang="scss">
#popup {
  z-index: 100;
  transition: all 300ms;
  position: absolute;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  top: 80px;
  left: 0;
  bottom: 0;
  right: 0;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;

  span {
    z-index: 0;
    position: absolute;
    display: block;
    width: 100%;
    height: 100%;
  }

  svg {
    padding: 0.6rem;
    z-index: 2;
    position: absolute;
    top: 31.6%;
    right: 34.9%;
    font-size: 1.1em;
    cursor: pointer;
  }
}

#city {
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
  position: relative;

  &::after {
    opacity: 0.15;
    z-index: 0;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    content: "";
    border-radius: 16px;
    background-image: url("@/assets/svg/separator_1.svg");
    background-position-y: 10px;
    background-size: cover;
  }

  section:first-of-type {
    z-index: 1;
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
    z-index: 1;
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

#conso {
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

    div {
      height: 400px;
    }
  }
}

#usages {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2.6rem;
  margin: 2.6rem auto;

  section {
    padding: 1rem;
    background-color: var(--secondary-bg-color);
    border-radius: 16px;
    position: relative;

    h4 {
      font-size: 1.2em;
      margin-bottom: 0.7rem;
    }

    div {
      height: 280px;
    }
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: all 400ms ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

hr {
  margin: 4rem auto;
  width: 60%;
  border-radius: 2px;
  border: 1px solid white;
}

#ecowatt {
  margin-bottom: 4rem;

  h4 {
    font-size: 1.5em;
    text-align: center;
    margin-bottom: 2rem;
  }

  .green {
    background-color: var(--green-touch);
  }

  .yellow {
    background-color: var(--yellow-touch);
  }

  .red {
    background-color: var(--red-touch);
  }

  > div {
    section {
      display: flex;
      flex-flow: row nowrap;
      align-items: center;
      justify-content: center;
      gap: 1px;

      div {
        display: block;
        width: 28px;
        height: 20px;

        &:first-of-type {
          border-radius: 60px 0 0 60px;
        }

        &:last-of-type {
          border-radius: 0 60px 60px 0;
        }
      }

      &:last-of-type {
        margin-top: 0.4rem;
        gap: 1.7rem;

        p {
          text-align: center;
          width: 30px;
          font-size: 0.8em;
        }
      }
    }
  }

  > section {
    margin-top: 2rem;
    display: flex;
    flex-flow: row nowrap;
    align-items: center;
    justify-content: center;
    gap: 2rem;

    div {
      border-radius: 16px;
      width: 200px;
      height: 120px;
      display: flex;
      flex-flow: column nowrap;
      align-items: center;
      justify-content: center;
      gap: 0.8rem;
      color: var(--primary-bg-color);
      position: relative;

      &::after {
        opacity: 0.4;
        z-index: 0;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        content: "";
        border-radius: 16px;
        background-image: url("@/assets/svg/separator_1.svg");
        background-size: cover;
        background-position-x: -80px;
        background-position-y: 20px;
        background-repeat: no-repeat;
      }

      h4 {
        z-index: 1;
        text-transform: capitalize;
        margin-bottom: 0;
      }

      p {
        font-size: 1.1em;
        z-index: 1;
      }
    }
  }
}

.hidden {
  margin: 0;
  padding: 0;
  max-width: 0;
  max-height: 0;
  display: none;
  opacity: 0;
  user-select: none;
  user-focus: none;
  overflow: hidden;
}

#realtime {
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

    div {
      height: 500px;
    }
  }
}
</style>
