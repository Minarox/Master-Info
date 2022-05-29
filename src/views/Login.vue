<template>
  <div>
    <Header />

    <main class="popup">
      <div>
        <article>
          <header>
            <h1>{{ $t("connection") }}</h1>
          </header>

          <section>
            <div :class="error ? 'error' : ''">
              <p>{{ $t("incorrect-login") }}</p>
            </div>
            <form @submit.prevent="loginForm">
              <label for="login">{{ $t("email-address") }}</label>
              <input
                type="email"
                name="login"
                id="login"
                autofocus
                autocomplete="username"
                required
                v-model="email"
              />
              <label for="password">{{ $t("password") }}</label>
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
              <button v-else type="submit">{{ $t("login") }}</button>
            </form>
          </section>
        </article>
      </div>
    </main>
  </div>
</template>

<script>
import { API } from "@/assets/js/api";
import Header from "@/components/global/Header";

export default {
  name: "Login",
  components: { Header },
  data() {
    return {
      email: "",
      password: "",
      loading: false,
      error: false,
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
          API.userInfo().then(() => {
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
    email: function () {
      this.error = false;
    },
    password: function () {
      this.error = false;
    },
  },
};
</script>

<style scoped>
.popup {
  background-color: rgba(0, 0, 0, 0);
  backdrop-filter: blur(0);
}

section > div {
  opacity: 0;
  border-radius: 8px;
  max-height: 0;
  overflow: hidden;
  background-color: #ff000026;
  border: 1px solid #ff000026;
  text-align: center;
  transition: all 0.3s ease-in-out;
}

section > div p {
  color: #8b0000ff;
}

.error {
  opacity: 1;
  max-height: 60px;
  margin-bottom: 20px;
  padding: 10px;
}
</style>
