import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Login from "../views/Login.vue";
import Logout from "../views/Logout.vue";

const routes = [
  {path: "/", name: "Home", component: Home},
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
  const loggedIn = JSON.parse(localStorage.getItem("session"));

  if (authRequired && !loggedIn) {
    return next({
      path: "/login",
      query: {returnUrl: to.path}
    });
  } else if (loggedIn && loggedIn.expires_in < (Date.now() / 1000 | 0)) {
    /*apiService.logout().then(() => {
      return next({
        path: "/login",
        query: {returnUrl: to.path}
      });
    });*/
  }

  next();
});

export default router;
