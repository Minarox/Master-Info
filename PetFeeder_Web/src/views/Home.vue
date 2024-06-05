<template>
  <div>
    <Header />

    <main>
      <div>
        <article id="main">
          <header>
            <h2>{{ $t("passage-dog") }}</h2>
            <div>
              <a href="" @click.prevent="getPassages" class="svg-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                  <!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. -->
                  <path
                    d="M496 48V192c0 17.69-14.31 32-32 32H320c-17.69 0-32-14.31-32-32s14.31-32 32-32h63.39c-29.97-39.7-77.25-63.78-127.6-63.78C167.7 96.22 96 167.9 96 256s71.69 159.8 159.8 159.8c34.88 0 68.03-11.03 95.88-31.94c14.22-10.53 34.22-7.75 44.81 6.375c10.59 14.16 7.75 34.22-6.375 44.81c-39.03 29.28-85.36 44.86-134.2 44.86C132.5 479.9 32 379.4 32 256s100.5-223.9 223.9-223.9c69.15 0 134 32.47 176.1 86.12V48c0-17.69 14.31-32 32-32S496 30.31 496 48z"
                  />
                </svg>
              </a>
            </div>
          </header>
          <section>
            <div v-if="passages" class="canvas-container">
              <canvas id="passages-canvas"></canvas>
            </div>
            <div v-else class="canvas-container">
              <p>{{ $t("nothing-found") }}</p>
            </div>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import Header from "../components/global/Header";
import { API } from "../assets/js/api";
import Chart from "chart.js";

export default {
  name: "Users",
  components: {
    Header,
  },
  data() {
    return {
      passages: [],
      food: [],
    };
  },
  mounted() {
    this.getPassages();
    this.getFoods();
  },
  methods: {
    getPassages() {
      API.getPassages()
        .then((response) => {
          this.passages = response;
          this.diagram(response);
        })
        .catch(() => {
          this.passages = null;
        });
    },
    getFoods() {
      API.getFoods().then((response) => {
        this.food = response[0];
      });
    },
    parseDate(date) {
      date = new Date(date).toLocaleString().split(" ");
      let time = date[1].split(":");
      time = time[0] + "h" + time[1].slice(0, 1) + "0";
      return date[0] + " " + time;
    },
    diagram(data) {
      let canvas = document.getElementById("passages-canvas").getContext("2d");

      let colorGradient = canvas.createLinearGradient(0, 230, 0, 50);
      colorGradient.addColorStop(0, "rgb(20, 168, 159)");
      colorGradient.addColorStop(1, "rgb(35, 170, 225)");

      let dataset = [[], []];

      for (let i = 0; i < 18; i++) {
        let date = this.parseDate(new Date().getTime() - i * 10 * 60000);
        dataset[0][i] = date.split(" ")[1];
        dataset[1][i] = 0;

        for (let y = 0; y < data.length; y++) {
          if (this.parseDate(data[y]["seen_at"]) === date) {
            dataset[1][i] += 1;
          }
        }
      }

      new Chart(canvas, {
        type: "line",
        data: {
          labels: dataset[0].reverse(),
          datasets: [
            {
              label: this.$t("sessions"),
              data: dataset[1].reverse(),
              backgroundColor: colorGradient,
            },
          ],
        },
        options: {
          maintainAspectRatio: false,
          legend: { display: false },
          scales: { yAxes: [{ ticks: { beginAtZero: true } }] },
        },
      });
    },
  },
};
</script>

<style scoped>
#main > section {
  height: calc(100vh - 212px);
}

.canvas-container {
  position: relative;
  padding: 0 6px 4px 2px;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.canvas-container > p {
  text-align: center;
  padding-top: 30px;
}
</style>
