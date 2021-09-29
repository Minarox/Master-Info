<template>
  <div id="navbar">
    <header>
      <article>
        <!-- Logo -->
        <a href="/" id="logo">
          <img alt="Logo" src="@/assets/img/logo.png">
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
            <svg id="profile-img" xmlns="http://www.w3.org/2000/svg" width="21" height="24" viewBox="0 0 21 24">
              <path id="Tracé_1" data-name="Tracé 1" d="M10.5,12a6,6,0,1,0-6-6A6,6,0,0,0,10.5,12Zm4.2,1.5h-.783a8.16,8.16,0,0,1-6.834,0H6.3A6.3,6.3,0,0,0,0,19.8v1.95A2.251,2.251,0,0,0,2.25,24h16.5A2.251,2.251,0,0,0,21,21.75V19.8A6.3,6.3,0,0,0,14.7,13.5Z"/>
            </svg>
            Utilisateur
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
      div.style.visibility = "visible";
      div.style.opacity = "1";
    },
    closeDropdown(id) {
      let div = document.getElementById(id)
      div.style.visibility = "hidden";
      div.style.opacity = "0";
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
  background-color: white;
  box-shadow: rgba(0, 0, 0, 0.07) 0 1px 1px, rgba(0, 0, 0, 0.07) 0 2px 2px, rgba(0, 0, 0, 0.07) 0 4px 4px, rgba(0, 0, 0, 0.07) 0 8px 8px, rgba(0, 0, 0, 0.07) 0 16px 16px;
}

#navbar header {
  margin: 0 auto;
  padding: 0 12px;
  max-width: 1200px;
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
  color: var(--text);
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
  color: var(--text);
  text-decoration: none;
  transition: color .15s;
}

.dropdown p {
  margin: 0;
  padding: 18px;
  cursor: pointer;
}

.dropdown p:hover {
  color: var(--text);
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

#profile-img {
  margin-right: 8px;
}

.dropdown div {
  display: flex;
  visibility: hidden;
  opacity: 0;
  background-color: white;
  border-radius: 6px;
  border: 1px solid black;
  min-width: 140px;
  flex-flow: column nowrap;
  position: absolute;
  top: 56px;
  transition: visibility .2s, opacity .2s;
}


#navbar article:last-of-type section div {
  right: 0;
}

.dropdown div a {
  font-size: .94em !important;
  color: var(--text);
  border-radius: inherit;
  transition: background-color .15s !important;
  padding: 12px !important;
}

.dropdown div a:hover {
  background-color: #DFDFDF;
  transition: background-color .15s !important;
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
  border-top: 1px solid black;
  margin: 4px 0;
  padding: 0;
}

#dropdown-mobile {
  display: none;
  font-weight: 500 !important;
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