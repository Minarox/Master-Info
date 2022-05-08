<template>
  <div class="popup">
    <article>
      <header>
        <h2>Suppression de l'email</h2>
      </header>

      <section>
        <form @submit.prevent="deleteEmail">
          <p>Voulez vous vraiment supprimer l'email {{ selected_email["title"] }} ?</p>
          <div>
            <button
                type="submit"
                class="button btn-warning"
            >
              Supprimer
            </button>
            <button
              type="button"
              class="button btn-back"
              @click="$emit('component', { name: '' })"
            >
              Annuler
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
  name: "DeleteEmail",
  props: ["selected_email"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteEmail: function() {
      API.deleteEmail(this.selected_email["email_id"])
        .then(() => {
          this.$notify({
            group: "success",
            title: "Modèle d'email supprimé",
            text: "Le modèle à été supprimé de l'application."
          }, 3500);
          this.$emit('component', { name: '' });
          this.$emit('reload');
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de la suppression. Le modèle n'a pas été impacté."
          }, 3500);
          this.$emit('component', { name: '' });
        });
    }
  }
};
</script>

<style scoped>
form p {
  text-align: center;
}
</style>
