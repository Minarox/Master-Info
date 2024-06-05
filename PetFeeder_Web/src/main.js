import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import notifications from "notiwind";
import { createI18n } from "vue-i18n";

const app = createApp(App);

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
