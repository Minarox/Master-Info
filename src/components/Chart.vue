<template>
  <canvas :id="chart_id" />
</template>

<script>
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

export default {
  name: "ChartComponent",
  props: ["chart_id", "type", "labels", "data", "colors", "multi", "stacked"],
  data() {
    return {
      chart: null,
    };
  },
  watch: {
    data() {
      if (!this.chart) {
        this.generateChar();
      } else {
        this.chart.destroy();
        this.generateChar();
      }
    },
  },
  methods: {
    generateChar() {
      Chart.defaults.color = "white";
      const ctx = document.getElementById(this.chart_id).getContext("2d");

      let data = this.data;
      Object.keys(data).forEach((i) => {
        data[i].backgroundColor = this.generateGradient(ctx, i);
        data[i].borderColor = this.generateRGBA(i);
        data[i].borderWidth = 2;
        data[i].pointBackgroundColor = this.generateRGBA(i);
        data[i].fill = true;
        data[i].borderRadius = 2;
        data[i].cubicInterpolationMode = "monotone";
      });

      this.chart = new Chart(ctx, {
        type: this.type,
        data: {
          labels: this.labels,
          datasets: data,
        },
        options: {
          maintainAspectRatio: false,
          scales: {
            x: {
              stacked: !!this.stacked,
              grid: {
                color: "rgba(255, 255, 255, 0.25)",
              },
            },
            y: {
              stacked: !!this.stacked,
              type: "linear",
              display: true,
              position: "left",
              beginAtZero: true,
              grid: {
                color: "rgba(255, 255, 255, 0.25)",
              },
            },
            y1: {
              type: "linear",
              display: !!this.multi,
              position: "right",
              grid: {
                drawOnChartArea: false,
              },
            },
          },
        },
      });
    },
    generateGradient(ctx, color_id) {
      let gradient = ctx.createLinearGradient(0, 0, 0, 300);
      if (this.colors && this.data) {
        gradient.addColorStop(
          0,
          `rgba(${this.colors[color_id][0]}, ${this.colors[color_id][1]}, ${this.colors[color_id][2]}, 0.75)`
        );
        gradient.addColorStop(
          0.5,
          `rgba(${this.colors[color_id][0]}, ${this.colors[color_id][1]}, ${this.colors[color_id][2]}, 0.5)`
        );
        gradient.addColorStop(
          1,
          `rgba(${this.colors[color_id][0]}, ${this.colors[color_id][1]}, ${this.colors[color_id][2]}, 0.25)`
        );
      } else {
        gradient.addColorStop(0, "rgba(4, 240, 198, 0.75)");
        gradient.addColorStop(0.5, "rgba(4, 240, 198, 0.5)");
        gradient.addColorStop(1, "rgba(4, 240, 198, 0.25)");
      }
      return gradient;
    },
    generateRGBA(color_id) {
      if (this.colors && this.data) {
        return `rgba(${this.colors[color_id][0]}, ${this.colors[color_id][1]}, ${this.colors[color_id][2]}, 1)`;
      } else {
        return "rgba(4, 240, 198, 1)";
      }
    },
  },
};
</script>
