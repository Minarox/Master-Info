<template>
  <div class="popup">
    <article>
      <header>
        <h2>Nouvel administrateur</h2>
      </header>

      <section>
        <form @submit.prevent="addAdmin">
          <label for="email">Email :</label>
          <input type="email" name="email" id="email" v-model="email" required>

          <label for="password">Mot de passe :</label>
          <input type="password" name="password" id="password" v-model="password" required>

          <label for="confirm_password">Confirmation du mot de passe :</label>
          <input type="password" name="v" id="confirm_password" v-model="confirm_password" required>

          <label for="first_name">Prénom :</label>
          <input type="text" name="first_name" id="first_name" v-model="first_name" required>

          <label for="last_name">Nom :</label>
          <input type="text" name="last_name" id="last_name" v-model="last_name" required>

          <label for="scope">Type :</label>
          <select name="scope" id="scope" v-model="scope" required>
            <option value=""></option>
            <option value="admin">Admin</option>
            <option value="super_admin">Super admin</option>
          </select>

          <div>
            <button
                type="submit"
                class="button"
            >
              Ajouter
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
  name: "AddAdmin",
  data() {
    return {
      email: '',
      password: '',
      confirm_password: '',
      first_name: '',
      last_name: '',
      scope: '',
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    addAdmin: function() {
      API.addAdmin(this.email, this.password, this.confirm_password, this.first_name, this.last_name, this.scope)
        .then(() => {
          this.$notify({
            group: "success",
            title: "Administrateur ajouté",
            text: "L'administrateur à été ajouté à l'application."
          }, 3500);
          this.$emit('component', { name: '' });
          this.$emit('reload');
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de l'ajout. Veuillez vérifier les champs avant de réessayer."
          }, 3500);
        });
    }
  }
};
</script>
