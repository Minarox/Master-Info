import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
  faLeaf,
  faArrowUpRightFromSquare,
  faCircleNotch,
} from "@fortawesome/free-solid-svg-icons";

library.add(faLeaf, faArrowUpRightFromSquare, faCircleNotch);

createApp(App)
  .use(router)
  .component("font-awesome-icon", FontAwesomeIcon)
  .mount("#app");
