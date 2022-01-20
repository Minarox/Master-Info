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
            <div :class="(error) ? 'error' : ''">
              <p>Nombre maximal d'utilisateurs atteint. <br>
                Veuillez contacter l'administrateur.</p>
            </div>
            <form @submit.prevent="loginForm">
              <label for="login">Nom d'utilisateur</label>
              <input type="text" name="login" id="login" autofocus autocomplete="username" required v-model="username">
              <button v-if="loading" type="submit" disabled><i class="fa fa-spinner fa-spin"></i></button>
              <button v-else type="submit">Se connecter</button>
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
      username: '',
      loading: false,
      error: false
    }
  },
  mounted() {
    this.checkSessionExist();
  },
  methods: {
    loginForm() {
      this.loading = true;
      API.login(this.username).then(response => {
        if (response && response["group_id"]) {
          API.getCurrentGroup().then(() => {
            this.$router.push('/');
          })
        } else {
          this.$router.push('/');
        }
      }).catch(() => {
        this.loading = false;
        this.error = true;
      })
    }
  },
  watch: {
    username: function () {
      this.error = false;
    },
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

section > div {
  max-height: 0;
  overflow: hidden;
  background-color: #FF000026;
  border: 1px solid var(--popup-bg);
  text-align: center;
  transition: all .3s ease-in-out;
}

section > div p {
  color: #8B0000FF;
}

.error {
  max-height: 60px;
  border: 1px solid #FF000026;
  margin-bottom: 20px;
  padding: 10px;
}

form input {
  margin-bottom: 30px;
}

form button {
  width: 150px;
}
</style>
