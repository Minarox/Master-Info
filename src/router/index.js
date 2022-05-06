import { createRouter, createWebHistory } from "vue-router";
import Users from "@/views/Users.vue";
import Admins from "@/views/Admins";
import Login from "@/views/Login.vue";
import Logout from "@/views/Logout.vue";
import { API } from "@/assets/js/api";

const routes = [
  { path: "/", name: "Users", component: Users },
  { path: "/admins", name: "Admins", component: Admins },
  { path: "/login", name: "Login", component: Login },
  { path: "/logout", name: "Logout", component: Logout },
  { path: "/:pathMatch(.*)*", redirect: "/" },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const publicPages = ["/login"];
  const authRequired = !publicPages.includes(to.path);
  const session = JSON.parse(localStorage.getItem("session"));

  if (authRequired && !session) {
    returnToLogin(to, from, next);
  } else if (session && Date.now() / 1000 > session["expires_at"]) {
    API.logout()
      .then(() => {
        returnToLogin(to, from, next);
      })
      .catch(() => {
        returnToLogin(to, from, next);
      });
  } else {
    next();
  }
});

function returnToLogin(to, from, next) {
  return next({
    path: "/login",
    query: { returnUrl: to.path },
  });
}

export default router;
