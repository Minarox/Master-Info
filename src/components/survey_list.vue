<template>
  <article>
    <transition name="no-mode-fade" mode="out-in">
      <section v-if="target_list" key="Professionnel">
        <a href="/survey/1/0">
          <img src="@/assets/img/phishing.png" alt="Phishing">
          <p>Phishing</p>
        </a>
        <a href="/survey/2/0">
          <img src="@/assets/img/devices.png" alt="Matériel informatique">
          <p>Matériel informatique</p>
        </a>
        <a href="/survey/0/0">
          <img src="@/assets/img/password.png" alt="Mot de passe">
          <p>Mot de passe</p>
        </a>
        <a href="/survey/1/0">
          <img src="@/assets/img/phishing.png" alt="Phishing">
          <p>Phishing</p>
        </a>
        <a href="/survey/2/0">
          <img src="@/assets/img/devices.png" alt="Matériel informatique">
          <p>Matériel informatique</p>
        </a>
        <a href="/survey/0/0">
          <img src="@/assets/img/password.png" alt="Mot de passe">
          <p>Mot de passe</p>
        </a>
      </section>
      <section v-else key="Particulier">
        <a href="/survey/0/0">
          <img src="@/assets/img/password.png" alt="Mot de passe">
          <p>Mot de passe</p>
        </a>
        <a href="/survey/1/0">
          <img src="@/assets/img/phishing.png" alt="Phishing">
          <p>Phishing</p>
        </a>
        <a href="/survey/2/0">
          <img src="@/assets/img/devices.png" alt="Matériel informatique">
          <p>Matériel informatique</p>
        </a>
        <a href="/survey/0/0">
          <img src="@/assets/img/password.png" alt="Mot de passe">
          <p>Mot de passe</p>
        </a>
        <a href="/survey/1/0">
          <img src="@/assets/img/phishing.png" alt="Phishing">
          <p>Phishing</p>
        </a>
        <a href="/survey/2/0">
          <img src="@/assets/img/devices.png" alt="Matériel informatique">
          <p>Matériel informatique</p>
        </a>
      </section>
    </transition>
  </article>
</template>

<script>
export default {
  name: "survey_list",
  props: ['list'],
  data() {
    return {
      target_list: 0
    }
  },
  watch: {
    list: function () {
      if (this.list === 'particulier') {
        this.target_list = 0
      } else {
        this.target_list = 1
      }
    }
  }
}
</script>

<style scoped>
article {
  max-width: 480px;
  background-image: none !important;
  width: 100%;
}

section {
  display: flex;
  flex-flow: row wrap;
  align-items: center;
  justify-content: right;
  gap: 20px;
}

a {
  max-width: 200px;
  width: 100%;
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  gap: 0 8px;
  font-size: 1em;
  padding: 4px 12px;
  border-radius: 8px;
  color: var(--text) !important;
  text-decoration: none;
  background-color: white;
  border: 1px solid black;
  box-shadow: rgba(50, 50, 93, 0.25) 0 6px 12px -2px, rgba(0, 0, 0, 0.3) 0 3px 7px -3px;
  transform: scale(1);
  transition: background-color .15s, transform .15s;
}

a:hover {
  background-color: #DDDDDD;
  transform: scale(1.04);
  transition: background-color .15s, transform .15s;
}

img {
  object-fit: cover;
  max-width: 40px;
  width: auto;
  max-height: 40px;
  height: auto;
}

p {
  color: black;
  text-decoration: none;
  font-size: .96em;
}

.no-mode-fade-enter-active, .no-mode-fade-leave-active {
  transition: opacity .4s
}

.no-mode-fade-enter-from, .no-mode-fade-leave-to {
  opacity: 0
}
</style>