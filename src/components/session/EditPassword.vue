<template>
  <div class="popup">
    <article>
      <header>
        <h2>Changement du mot de passe</h2>
      </header>

      <section>
        <form @submit.prevent="editPassword">
          <label for="old_password">Ancien mot de passe :</label>
          <input type="email" name="old_password" id="old_password" v-model="old_password" required />
          <label for="new_password">Nouveau mot de passe :</label>
          <input type="text" name="new_password" id="new_password" v-model="new_password" required />
          <label for="confirm_new_password">Confirmation du nouveau mot de passe :</label>
          <input type="text" name="confirm_new_password" id="confirm_new_password" v-model="confirm_new_password" required />
          <div>
            <button type="submit">
              Valider
            </button>
            <button
                type="button"
                class="button btn-back"
                @click="$emit('component', { name: 'UserInfo' })"
            >
              Retour
            </button>
          </div>
        </form>
      </section>
    </article>
  </div>
</template>

<script>
import { API } from "@/assets/js/api";

export default {
  name: "EditUserInfo",
  data() {
    return {
      old_password: '',
      new_password: '',
      confirm_new_password: ''
    };
  },
  mounted() {
    this.addEvents("UserInfo", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("UserInfo", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editPassword() {
      API.editPassword(this.old_password, this.new_password, this.confirm_new_password)
          .then(() => {
            this.$router.push("/logout");
          });
    },
  },
};
</script>

<style scoped>
.popup {
  animation: fade-in 0.4s alternate;
}

.popup article section form input {
  margin-bottom: 24px;
}

.popup article section form div {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: center;
  gap: 12px 40px;
}

.popup article section form div button {
  margin: 0;
}

@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@media only screen and (max-width: 500px) {
  .popup article section form div {
    flex-flow: column nowrap;
    justify-content: center;
  }

  .popup article section form div button {
    width: 100%;
  }
}
</style>
