import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Login from "../views/Login.vue";
import Logout from "../views/Logout.vue";
import Admin from "../views/Admin";
import {API} from "../assets/js/api";

const routes = [
  {path: "/", name: "Home", component: Home},
  {path: "/admin", name: "Admin", component: Admin},
  {path: "/login", name: "Login", component: Login},
  {path: "/logout", name: "Logout", component: Logout},
  { path: "/:pathMatch(.*)*", redirect: '/' }
/*  {
    path: "/about",
    name: "About",
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () =>
      import(/!* webpackChunkName: "about" *!/ "../views/About.vue"),
  },*/
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

router.beforeEach((to, from, next) => {
  // redirect to login page if not logged in and trying to access a restricted page
  const publicPages = ["/login"];
  const authRequired = !publicPages.includes(to.path);
  const session = JSON.parse(localStorage.getItem("session"));

  if (authRequired && !session) {
    returnToLogin(to, from, next);
    next();
  } else if (session && Date.parse(session.expire) < Date.now()) {
    API.logout().then(() => {
      returnToLogin(to, from, next);
      next();
    }).catch(() => {
      returnToLogin(to, from, next);
      next();
    });
  } else {
    next();
  }
});

function returnToLogin(to, from, next) {
  return next({
    path: "/login",
    query: {returnUrl: to.path}
  });
}

export default router;
