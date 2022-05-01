<template>
  <main>
      <div>
        <article>
          <header>
            <h1>Connexion</h1>
          </header>

          <section>
            <div :class="error ? 'error' : ''">
              <p v-if="error">Adresse email ou mot de passe incorrecte.</p>
            </div>
            <form @submit.prevent="loginForm">
              <label for="login">Adresse email</label>
              <input
                type="email"
                name="login"
                id="login"
                autofocus
                autocomplete="username"
                required
                v-model="email"
              />
              <label for="password">Mot de passe</label>
              <input
                type="password"
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
</template>

<script>
import { API } from "@/assets/js/api";

export default {
  name: "Login",
  data() {
    return {
      email: "",
      password: "",
      loading: false,
      error: false
    };
  },
  mounted() {
    this.checkSessionExist();
  },
  methods: {
    loginForm() {
      this.loading = true;
      API.login(this.email, this.password)
        .then(() => {
          API.userInfo()
          .then(() => {
            this.$router.push("/");
          });
        })
        .catch((error) => {
          this.loading = false;
          if (error.response.status === 400 || error.response.status === 401) {
            this.error = true;
          }
        });
    },
  },
  watch: {
    username: function () {
      this.error = false;
    },
    password: function () {
      this.error = false;
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
