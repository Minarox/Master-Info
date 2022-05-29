import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import notifications from "notiwind";
import { API } from "./assets/js/api";
import { createI18n } from "vue-i18n";

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
        API.userInfo().then();
        router.push("/").then();
      }
    },
    isSuperAdmin(routerPush = true) {
      let session = JSON.parse(localStorage.getItem("session"));
      if (session["scope"] === "super_admin") {
        return true;
      }
      if (routerPush) this.$router.push("/").then();
      return false;
    },
    switchComponent(payload) {
      this.component = payload.name;
    },
    addEvents(component, container, self = this) {
      this.$el.addEventListener("click", function (e) {
        if (e.target === container) {
          self.$emit("component", { name: component });
        }
      });
      this.$el.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          self.$emit("component", { name: component });
        }
      });
    },
    removeEvents(component, container, self = this) {
      this.$el.removeEventListener("click", function (e) {
        if (e.target === container) {
          self.$emit("component", { name: component });
        }
      });
      this.$el.removeEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          self.$emit("component", { name: component });
        }
      });
    },
  },
});

function loadLocaleMessages() {
  const locales = require.context(
    "./locales",
    true,
    /[A-Za-z0-9-_,\s]+\.json$/i
  );
  const messages = {};
  locales.keys().forEach((key) => {
    const matched = key.match(/([A-Za-z0-9-_]+)\./i);
    if (matched && matched.length > 1) {
      const locale = matched[1];
      messages[locale] = locales(key);
    }
  });
  return messages;
}

let language = "";
switch (navigator.language) {
  case "fr":
    language = "fr";
    break;
  case "fr-FR":
    language = "fr";
    break;
  default:
    language = "en-US";
    break;
}

const i18n = createI18n({
  locale: language || "en",
  fallbackLocale: "en",
  messages: loadLocaleMessages(),
  silentTranslationWarn: true,
  missingWarn: false,
  silentFallbackWarn: true,
  fallbackWarn: false,
});

app.use(router).use(notifications).use(i18n).mount("#app");
