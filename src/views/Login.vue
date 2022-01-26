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
            <div :class="errorBadLogin || errorMaxUsers ? 'error' : ''">
              <p v-if="errorBadLogin">Mot de passe incorrecte.</p>
              <p v-if="errorMaxUsers">
                Nombre maximal d'utilisateurs atteint. <br />
                Veuillez contacter l'administrateur.
              </p>
            </div>
            <form @submit.prevent="loginForm">
              <label for="login">Nom d'utilisateur</label>
              <input
                type="text"
                name="login"
                id="login"
                autofocus
                autocomplete="username"
                required
                v-model="username"
              />
              <label for="password">Mot de passe</label>
              <input
                type="text"
                name="password"
                id="password"
                autocomplete="password"
                required
                v-model="password"
              />
              <button v-if="loading" type="submit" disabled>
                <i class="fa fa-spinner fa-spin"></i>
              </button>
              <button v-else type="submit">Se connecter</button>
            </form>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import Header from "../components/global/Header";
import { API } from "../assets/js/api";

export default {
  name: "Login",
  components: {
    Header,
  },
  data() {
    return {
      username: "",
      password: "",
      loading: false,
      errorBadLogin: false,
      errorMaxUsers: false,
    };
  },
  mounted() {
    this.checkSessionExist();
  },
  methods: {
    loginForm() {
      this.loading = true;
      API.login(this.username, this.password)
        .then(() => {
          this.$router.push("/");
        })
        .catch((error) => {
          this.loading = false;
          if (error.response.status === 401) {
            this.errorBadLogin = true;
          } else if (error.response.status === 403) {
            this.errorMaxUsers = true;
          }
        });
    },
  },
  watch: {
    username: function () {
      this.errorBadLogin = false;
      this.errorMaxUsers = false;
    },
    password: function () {
      this.errorBadLogin = false;
      this.errorMaxUsers = false;
    },
  },
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
  background-color: #ff000026;
  border: 1px solid var(--popup-bg);
  text-align: center;
  transition: all 0.3s ease-in-out;
}

section > div p {
  color: #8b0000ff;
}

.error {
  max-height: 60px;
  border: 1px solid #ff000026;
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
