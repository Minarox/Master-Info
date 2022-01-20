import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

const app = createApp(App);

app.mixin({
    data() {
        return {
            api_url: "https://ic.minarox.fr/api"
        }
    },
    methods: {
        checkSessionExist() {
            if (localStorage.getItem("session")) router.push('/');
        },
        isAdmin(routerPush = true) {
            let admin = JSON.parse(localStorage.getItem("session"))["is_admin"].toString();
            if (admin !== '1') {
                if (routerPush) this.$router.push('/');
                return false;
            }
            return true;
        }
    }
});

app.use(router).mount("#app");
