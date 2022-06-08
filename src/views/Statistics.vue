<template>
  <div>
    <Header :current_user="current_user" @component="switchComponent" />
    <transition name="fade">
      <component
        v-if="component"
        :is="component"
        :current_user="current_user"
        @component="switchComponent"
      />
    </transition>

    <main>
      <div>
        <article id="main">
          <header>
            <h2>{{ $t("statistics") }}</h2>
            <div>
              <a href="" @click.prevent="refreshStats" class="svg-button">
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
            <article>
              <!-- Global statistics -->
              <h3>{{ $t("global-stats") }}</h3>
              <section>
                <table>
                  <tr>
                    <td>{{ $t("nb-users") }}</td>
                    <td>{{ stats["nb_users"] }}</td>
                  </tr>
                  <tr>
                    <td>{{ $t("nb-share") }}</td>
                    <td>{{ stats["nb_share"] }}</td>
                  </tr>
                  <tr>
                    <td>{{ $t("avg-usage-day") }}</td>
                    <td>{{ stats["avg_usage_day"] }}</td>
                  </tr>
                  <tr>
                    <td>{{ $t("avg-usage-7-days") }}</td>
                    <td>{{ stats["avg_usage_7_days"] }}</td>
                  </tr>
                  <tr>
                    <td>{{ $t("most-used-android-version") }}</td>
                    <td>{{ stats["most_used_android_version"] }}</td>
                  </tr>
                </table>
              </section>
            </article>
            <!-- Average usage per month -->
            <article>
              <h3>{{ $t("avg-usage-month") }}</h3>
              <div>
                <canvas id="monthly-sessions"></canvas>
              </div>
            </article>
            <!-- Average usage per day -->
            <article>
              <h3>{{ $t("avg-usage-day") }}</h3>
              <div>
                <canvas id="daily-sessions"></canvas>
              </div>
            </article>
            <!-- Average usage per hour -->
            <article>
              <h3>{{ $t("avg-usage-hour") }}</h3>
              <div>
                <canvas id="hourly-sessions"></canvas>
              </div>
            </article>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import Header from "../components/global/Header";
import UserInfo from "../components/session/UserInfo";
import EditPassword from "../components/session/EditPassword";
import Chart from "chart.js";
import { API } from "../assets/js/api";

export default {
  name: "Logs",
  components: {
    Header,
    UserInfo,
    EditPassword,
  },
  data() {
    return {
      current_user: JSON.parse(localStorage.getItem("user")),
      component: "",
      stats: {
        nb_users: 0,
        nb_share: 0,
        avg_usage_day: 0,
        avg_usage_7_days: 0,
        most_used_android_version: "",
      },
      avgUsageMonth: true,
      avgUsageDay: true,
      avgUsageHour: true,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: { display: false },
        scales: { yAxes: [{ ticks: { beginAtZero: true } }] },
      },
    };
  },
  mounted() {
    if (this.isSuperAdmin()) {
      this.refreshStats();
    }
  },
  methods: {
    refreshStats() {
      this.getGlobalStats();
      this.getAvgUsageMonth();
      this.getAvgUsageDay();
      this.getAvgUsageHour();
    },
    getGlobalStats() {
      API.getGlobalStats().then((response) => {
        this.stats = response;
      });
    },
    getAvgUsageMonth() {
      API.getAvgUsageMonth()
        .then((response) => {
          if (response) {
            this.diagramMonth(response);
            this.avgUsageMonth = true;
          }
        })
        .catch(() => {
          this.avgUsageMonth = false;
        });
    },
    getAvgUsageDay() {
      API.getAvgUsageDay()
        .then((response) => {
          if (response) {
            this.diagramDay(response);
            this.avgUsageDay = true;
          }
        })
        .catch(() => {
          this.avgUsageDay = false;
        });
    },
    getAvgUsageHour() {
      API.getAvgUsageHour()
        .then((response) => {
          if (response) {
            this.diagramHour(response);
            this.avgUsageHour = true;
          }
        })
        .catch(() => {
          this.avgUsageHour = false;
        });
    },
    colorGradient(canvas) {
      let colorGradient = canvas.createLinearGradient(0, 230, 0, 50);
      colorGradient.addColorStop(0, "rgb(20, 168, 159)");
      colorGradient.addColorStop(1, "rgb(35, 170, 225)");
      return colorGradient;
    },
    diagramMonth(dataset) {
      let canvas = document.getElementById("monthly-sessions").getContext("2d");

      new Chart(canvas, {
        type: "bar",
        data: {
          labels: dataset[0],
          datasets: [
            {
              label: "Usage",
              data: dataset[1],
              backgroundColor: this.colorGradient(canvas),
            },
          ],
        },
        options: this.options,
      });
    },
    diagramDay(dataset) {
      let canvas = document.getElementById("daily-sessions").getContext("2d");

      for (let i = 0; i < dataset[0].length; i++)
        dataset[0][i] = this.$t(dataset[0][i]);

      new Chart(canvas, {
        type: "bar",
        data: {
          labels: dataset[0],
          datasets: [
            {
              label: "Usage",
              data: dataset[1],
              backgroundColor: this.colorGradient(canvas),
            },
          ],
        },
        options: this.options,
      });
    },
    diagramHour(dataset) {
      let canvas = document.getElementById("hourly-sessions").getContext("2d");

      new Chart(canvas, {
        type: "line",
        data: {
          labels: dataset[0],
          datasets: [
            {
              label: "Usage",
              data: dataset[1],
              backgroundColor: "rgba(0, 0, 0, 0)",
              borderColor: this.colorGradient(canvas),
            },
          ],
        },
        options: this.options,
      });
    },
  },
};
</script>

<style scoped>
#main section {
  border-radius: unset !important;
  box-shadow: none !important;
}

#main > section {
  border-radius: unset !important;
  box-shadow: none !important;
  height: calc(100vh - 208px);
  display: flex;
  flex-flow: row wrap;
  justify-content: space-between;
  gap: 16px;
}

#main > section > article {
  flex: 1 1 calc(50% - 20px);
  height: calc(50% - 12px);
  border: 1px solid var(--bg-color-center);
  border-radius: 8px;
}

#main > section > article h3 {
  margin: 8px 8px 16px;
  font-size: 1.1em;
}

#main > section > article section table {
  width: 100%;
  height: 100%;
  border-spacing: 0;
}

#main > section > article section table td {
  padding: 8px;
}

#main > section > article section table tr:nth-of-type(odd) {
  background-color: rgba(0, 0, 0, 0.05);
}

#main > section > article > div {
  position: relative;
  padding: 0 6px 4px 2px;
  width: 100%;
  height: calc(100% - 50px);
  overflow: hidden;
}

#main > section > article > div > p {
  text-align: center;
  padding-top: 30px;
}

@media only screen and (max-width: 950px) {
  #main > section > article {
    flex: 1 1 100%;
  }
}

@media only screen and (max-width: 700px) {
  #main > section > article:first-of-type {
    height: max-content;
  }

  #main > section {
    height: calc(100vh - 172px);
  }
}
</style>
