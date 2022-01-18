<template>
  <header id="header">
    <article>
      <section>
        <router-link id="logo" to="/">
          <img src="@/assets/img/cloudbees-core_logo.png" alt="Logo">
          <p>Intégration continue</p>
        </router-link>
      </section>

      <section v-if="username" id="normal_menu" @mouseleave="opened = false">
        <a class="button" @click="first_open = true; opened = !opened">
          <img src="@/assets/img/user_logo.png" alt="User logo">
          {{ username }}
          <i class="fas fa-sort-down"></i>
        </a>
        <ul :class="(opened) ? 'dropdown open' : 'dropdown'" @mouseleave="opened = false">
          <li>
            <!-- Logout button -->
            <router-link to="/logout" @click="opened = false">Déconnexion</router-link>
          </li>
        </ul>
      </section>
    </article>
  </header>
</template>

<script>
export default {
  name: "Header",
  data() {
    return {
      opened: false,
      first_open: false,
      username: null
    }
  },
  mounted() {
    if (localStorage.getItem("session")) {
      const session = JSON.parse(localStorage.getItem("session"));
      this.username = session["username"];
    }
  },
  watch: {
    first_open: function () {
      let dropdowns = document.getElementsByClassName("dropdown");
      for (const element of dropdowns) element.style.animationDuration = ".2s";
    }
  }
};
</script>

<style scoped>
#header {
  position: sticky;
  top: 0;
  height: 56px;
  padding: 0 20px;
  display: flex;
  align-items: center;
  background-color: var(--popup-bg);
  box-shadow: rgba(0, 0, 0, 0) 0 0 0 0,
    rgba(0, 0, 0, 0) 0 0 0 0,
    rgba(0, 0, 0, 0.1) 0 10px 15px -3px,
    rgba(0, 0, 0, 0.05) 0 4px 6px -2px;
}

#header article {
  width: 100%;
  max-width: 1500px;
  margin: 0 auto;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
}

#header #logo {
  padding: 8px;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  gap: 16px;
  text-decoration: none;
  font-size: 1.4em;
}

#header #logo, #header #logo img {
  height: 34px;
}

#header #normal_menu {
  position: relative;
  cursor: pointer;
}

#header #normal_menu a {
  padding: 8px;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  gap: 8px;
  text-transform: initial;
  border-radius: 4px;
  transition: background-color 100ms;
  background-color: var(--popup-bg);
}

#header #normal_menu a:hover {
  transition: background-color 100ms;
  background-color: var(--global-bg);
}

#header #normal_menu a img {
  object-fit: cover;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  padding: 2px;
  border: 1px solid var(--text);
}

#header #normal_menu a i {
  font-size: 1.2em;
  transform: translateY(-3px);
}

#header #normal_menu ul {
  position: absolute;
  background-color: var(--popup-bg);
  border: 1px solid rgba(0, 0, 0, .05);
  right: 0;
  min-width: 120px;
  list-style-type: none;
  padding: 6px 0;
  border-radius: 4px;
  display: flex;
  flex-flow: column nowrap;
  gap: 6px;
  box-shadow:
      0 4px 6px 0 rgba(50, 50, 93, 0.11),
      0 1px 3px 0 rgba(0, 0, 0, 0.08);
}

#header #normal_menu ul li a {
  border-radius: 0;
  padding: 6px;
  text-decoration: none;
  font-size: .9em;
}

#header .dropdown {
  display: none;
  pointer-events: none;
  opacity: 0;
  animation: dropdown-disappear 0s alternate;
}

#header .open {
  display: flex;
  pointer-events: auto;
  opacity: 1;
  animation: dropdown-appear .2s alternate;
}

@keyframes dropdown-appear {
  from {
    display: flex;
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes dropdown-disappear {
  0% {
    display: flex;
    opacity: 1;
    transform: translateY(0);
  }
  99% {
    display: flex;
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    display: none;
  }
}
</style>
