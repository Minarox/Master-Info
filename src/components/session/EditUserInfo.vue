<template>
  <div class="popup">
    <article>
      <header>
        <h2>Modifier votre profil</h2>
      </header>

      <section>
        <form @submit.prevent="editUserInfo">
          <label for="email">Adresse email :</label>
          <input type="email" name="email" id="email" v-model="email" required />
          <label for="first_name">Prénom :</label>
          <input type="text" name="first_name" id="first_name" v-model="first_name" required />
          <label for="last_name">Nom :</label>
          <input type="text" name="last_name" id="last_name" v-model="last_name" required />
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
  props: ["current_user"],
  data() {
    return {
      email: this.current_user["email"],
      first_name: this.current_user["first_name"],
      last_name: this.current_user["last_name"]
    };
  },
  mounted() {
    this.addEvents("UserInfo", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("UserInfo", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editUserInfo() {
      API.editUserInfo(this.email, this.first_name, this.last_name)
        .then(() => {
          this.$router.go(0);
        })
    },
  },
};
</script>
