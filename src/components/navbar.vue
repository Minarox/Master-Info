<template>
  <div id="navbar">
    <header>
      <!-- Logo -->
      <a href="/" @click.prevent="$router.go('/')" id="logo">
        <img alt="Logo" src="@/assets/logo.png">
      </a>
      <!-- Links for Phone -->
      <a href="#" id="mobile_menu">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
          <path fill="#fff" d="M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z">
          </path>
        </svg>
      </a>
      <!-- Links for PC -->
      <nav>
        <router-link to="/">Accueil</router-link>
        <router-link v-for="i in Categories.length" :key="i" :to="'/survey/' + (i-1) + '/0'">{{ Categories[i-1] }}</router-link>
      </nav>
    </header>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "navbar",
  computed: {
    ...mapState(['Categories'])
  }
}
</script>

<style scoped>
#navbar {
  z-index: 100000;
  height: 64px;
  position: sticky;
  top: 0;
  left: 0;
  background-color: #DFDFDF;
  border-bottom: 1px solid grey;
}

#navbar header {
  margin: 0 auto;
  padding: 0 12px;
  max-width: 1420px;
  height: 100%;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
}

#logo, #logo * {
  width: 46px;
  height: 46px;
  margin-right: 16px;
}

#navbar a svg {
  display: none;
  padding: 18px;
  width: auto;
  height: 26px;
  color: var(--primary-text);
  filter: brightness(100%);
  transition: filter .15s;
}

#navbar nav a {
  padding: 18px;
  font-size: 1.1em;
  font-weight: 600;
  color: var(--primary-text);
  text-decoration: none;
  transition: color .15s;
}

#navbar nav a:hover {
  color: var(--secondary-text);
  transition: color .15s;
}
</style>