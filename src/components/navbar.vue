<template>
  <div id="navbar">
    <header>
      <article>
        <!-- Logo -->
        <a href="/" id="logo">
          <img alt="Logo" src="@/assets/logo.png">
        </a>
        <!-- Links for PC -->
        <nav>
          <a href="/">Accueil</a>
          <section class="dropdown" @mouseover="openDropdown('dropdown-cours')" @mouseleave="closeDropdown('dropdown-cours')">
            <p>
              Nos cours
              <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-down fa-w-10 fa-3x">
                <path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z" class="">
                </path>
              </svg>
            </p>
            <div id="dropdown-cours">
              <a href="/cours/cours_1">Cours 1</a>
              <a href="/cours/cours_2">Cours 2</a>
              <a href="/cours/cours_3">Cours 3</a>
            </div>
          </section>
          <section class="dropdown" @mouseover="openDropdown('dropdown-cat')" @mouseleave="closeDropdown('dropdown-cat')">
            <p>
              Questionnaires
              <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-down fa-w-10 fa-3x">
                <path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z" class="">
                </path>
              </svg>
            </p>
            <div id="dropdown-cat">
              <a v-for="i in Categories.length" :key="i" :href="'/survey/' + (i-1) + '/0'">{{ Categories[i-1] }}</a>
            </div>
          </section>
        </nav>
      </article>
      <article>
        <section id="dropdown-mobile" class="dropdown" @click="openDropdown('dropdown-nav')" @mouseleave="closeDropdown('dropdown-nav')">
          <p>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
              <path fill="#000" d="M16 132h416c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H16C7.163 60 0 67.163 0 76v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z">
              </path>
            </svg>
          </p>
          <div id="dropdown-nav">
            <a href="/">Accueil</a>
            <a href="/">Nos cours</a>
            <a href="/">Questionnaires</a>
            <a v-for="i in Categories.length" :key="i" :href="'/survey/' + (i-1) + '/0'">{{ Categories[i-1] }}</a>
            <hr>
            <a href="#">Profil</a>
            <a href="#">Déconnexion</a>
          </div>
        </section>
        <section id="profile" class="dropdown" @mouseover="openDropdown('dropdown-profile')" @mouseleave="closeDropdown('dropdown-profile')">
          <p>
            {{ Accounts[0]['name'] }}
            <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="caret-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" class="svg-inline--fa fa-caret-down fa-w-10 fa-3x">
              <path fill="currentColor" d="M31.3 192h257.3c17.8 0 26.7 21.5 14.1 34.1L174.1 354.8c-7.8 7.8-20.5 7.8-28.3 0L17.2 226.1C4.6 213.5 13.5 192 31.3 192z" class="">
              </path>
            </svg>
          </p>
          <div id="dropdown-profile">
            <a href="#">Profil</a>
            <a href="#">Deconnexion</a>
          </div>
        </section>
      </article>
    </header>
  </div>
</template>

<script>
import {mapState} from "vuex";

export default {
  name: "navbar",
  computed: {
    ...mapState(['Categories', 'Accounts'])
  },
  methods: {
    openDropdown(id) {
      let div = document.getElementById(id)
      div.style.display = "flex";
    },
    closeDropdown(id) {
      let div = document.getElementById(id)
      div.style.display = "none";
    }
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
  justify-content: space-between;
}

#navbar header article:first-of-type {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
}

#navbar header article:last-of-type a {
  color: var(--primary-text);
  text-decoration: none;
  padding: 18px;
}

#logo, #logo * {
  width: 46px;
  height: 46px;
  margin-right: 16px;
}

#mobile_menu svg {
  display: none;
  width: auto;
  height: 24px;
  filter: brightness(100%);
  transition: filter .15s;
}

#mobile_menu svg {
  filter: brightness(50%);
  transition: filter .15s;
}

#navbar nav a, .dropdown {
  padding: 18px;
  font-size: 1.1em;
  font-weight: 600;
  color: var(--primary-text);
  text-decoration: none;
  transition: color .15s;
}

.dropdown p {
  margin: 0;
  padding: 18px;
  cursor: pointer;
}

.dropdown p:hover {
  color: var(--secondary-text);
  transition: color .15s;
}

.dropdown {
  display: inline-block;
  position: relative;
  padding: 0;
}

#profile {
  font-weight: 400 !important;
}

.dropdown div {
  display: none;
  background-color: white;
  min-width: 130px;
  flex-flow: column nowrap;
  position: absolute;
}

#navbar article:last-of-type section div {
  right: 0;
}

.dropdown div a {
  font-size: .9em !important;
  padding: 14px;
  background-color: white;
  color: var(--primary-text);
  transition: background-color .15s;
}

.dropdown div a:hover {
  color: var(--primary-text);
  background-color: #DFDFDF;
  transition: background-color .15s;
}

svg {
  margin-left: 6px;
  width: auto;
  height: 18px;
}

#dropdown-nav {
  width: 100vw;
  right: -12px !important;
}

hr {
  display: block;
  height: 1px;
  border: 0;
  border-top: 1px solid #ccc;
  margin: 8px 0;
  padding: 0;
}

#dropdown-mobile {
  display: none;
}

@media screen and (max-width: 700px) {
  #navbar article:first-of-type nav, #navbar article:last-of-type section:last-of-type {
    display: none;
  }

  #mobile_menu svg, #dropdown-mobile {
    display: inline-block;
  }
}
</style>