<template>
  <div class="popup">
    <article>
      <header>
        <h2>Votre profil</h2>
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
            <button
              type="submit"
              class="button"
            >
              Modifier
            </button>
            <button
                type="button"
                class="button btn-warning"
                @click="$emit('component', { name: 'EditPassword' })"
            >
              Changer le mot de passe
            </button>
            <button
              type="button"
              class="button btn-back"
              @click="$emit('component', { name: '' })"
            >
              Fermer
            </button>
          </div>
        </form>
      </section>
    </article>
  </div>
</template>

<script>
import {API} from "@/assets/js/api";

export default {
  name: "UserInfo",
  props: ["current_user"],
  data() {
    return {
      email: this.current_user["email"],
      first_name: this.current_user["first_name"],
      last_name: this.current_user["last_name"]
    };
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editUserInfo() {
      API.editUserInfo(this.email, this.first_name, this.last_name)
          .then(() => {
            this.$notify({
              group: "success",
              title: "Profil modifié",
              text: "Les modifications ont été prises en compte."
            }, 3500);
            this.$router.go(0);
          })
          .catch(() => {
            this.$notify({
              group: "error",
              title: "Erreur",
              text: "Une erreur est survenue lors de la modification des informations. Veuillez vérifier les champs avant de réessayer."
            }, 3500);
        });
    },
  },
};
</script>