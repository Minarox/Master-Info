<template>
  <div class="popup">
    <article>
      <header>
        <h2>Nouvel utilisateur</h2>
      </header>

      <section>
        <form @submit.prevent="addUser">
          <label for="email">Email :</label>
          <input type="email" name="email" id="email" v-model="email" required>

          <label for="first_name">Prénom :</label>
          <input type="text" name="first_name" id="first_name" v-model="first_name" required>

          <label for="last_name">Nom :</label>
          <input type="text" name="last_name" id="last_name" v-model="last_name" required>

          <label for="device">Appareil :</label>
          <input type="text" name="device" id="device" v-model="device">
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
  name: "AddUser",
  data() {
    return {
      email: '',
      first_name: '',
      last_name: '',
      device: ''
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    addUser: function() {
      API.addUser(this.email, this.first_name, this.last_name, this.device)
        .then(() => {
          this.$notify({
            group: "success",
            title: "Utilisateur ajouté",
            text: "L'utilisateur à été ajouté à l'application."
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
