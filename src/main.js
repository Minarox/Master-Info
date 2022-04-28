import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { API } from "./assets/js/api";

const app = createApp(App);

app.mixin({
  data() {
    return {
      api_url: "https://mspr.minarox.fr/api",
    };
  },
  methods: {
    checkSessionExist() {
      if (localStorage.getItem("session")) {
        API.currentSession().then();
        router.push("/").then();
      }
    },
    isAdmin(routerPush = true) {
      let admin = JSON.parse(localStorage.getItem("session"))[
        "is_admin"
      ].toString();
      if (admin !== "1") {
        if (routerPush) this.$router.push("/").then();
        return false;
      }
      return true;
    },
    unauthorizedError(error) {
      if (error["response"] && error["response"]["status"] === 401) {
        API.logout()
          .then(() => {
            this.$router.push("/login").then();
          })
          .catch(() => {
            this.$router.push("/login").then();
          });
      }
    },
    switchComponent(payload) {
      this.component = payload.name;
    },
    addEvents(component, container, self = this) {
      window.addEventListener("click", function (e) {
        if (e.target === container) {
          self.$emit("component", { name: component });
        }
      });
      window.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          self.$emit("component", { name: component });
        }
      });
    },
    removeEvents(component, container, self = this) {
      window.removeEventListener("click", function (e) {
        if (e.target === container) {
          self.$emit("component", { name: component });
        }
      });
      window.removeEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          self.$emit("component", { name: component });
        }
      });
    },
  },
});

app.use(router).mount("#app");
