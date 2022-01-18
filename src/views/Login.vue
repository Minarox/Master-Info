<template>
  <div>
    <Header />

    <main>
      <div>
        <article>
          <header>
            <h1>Connexion</h1>
          </header>

          <section>
            <form @submit.prevent="loginForm">
              <label for="login">Nom d'utilisateur</label>
              <input type="text" name="login" id="login" autofocus autocomplete="username" required v-model="username">
              <button type="submit">Se connecter</button>
            </form>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import Header from "../components/Header";
import { API } from "../assets/js/api";

export default {
  name: "Login",
  components: {
    Header
  },
  data() {
    return {
      username: ''
    }
  },
  mounted() {
    this.checkSessionExist();
  },
  methods: {
    loginForm() {
      API.login(this.username).then(response => {
        if (response) this.$router.push('/');
      })
    }
  }
};
</script>

<style scoped>
main > div {
  padding-top: 50px;
}

article {
  width: 100%;
  max-width: 500px;
  margin: 0 auto;
  border-radius: 6px;
  background-color: var(--popup-bg);
  box-shadow: rgba(149, 157, 165, 0.2) 0 8px 24px 0;
}

article > header {
  background-color: var(--hover-bg);
  padding: 16px;
  text-align: center;
  border-radius: 6px 6px 0 0;
  font-size: 1.3em;
  font-weight: 500;
}

section {
  padding: 20px;
}

form label {
  display: block;
  margin: 0 0 6px 4px;
  color: var(--text-label);
}

form input {
  display: block;
  width: 100%;
  outline: none;
  padding: 6px;
  border: none;
  border-bottom: 1px solid var(--text-label);
  margin-bottom: 30px;
}

form button {
  display: block;
  margin: 0 auto;
  outline: none;
  cursor: pointer;
  border-radius: 3px;
  font-size: 14px;
  font-weight: 500;
  line-height: 16px;
  padding: 10px 24px;
  border: none;
  color: #fff;
  background-color: var(--btn);
  transition: background-color .17s ease,color .17s ease;
}

form button:hover {
  background-color: var(--btn-hover);
}
</style>
