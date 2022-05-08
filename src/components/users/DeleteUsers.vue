<template>
  <div class="popup">
    <article>
      <header>
        <h2>Suppression des utilisateurs</h2>
      </header>

      <section>
        <form @submit.prevent="deleteUsers">
          <p>Voulez vous vraiment supprimer les utilisateurs sélectionnés ?</p>
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
  name: "DeleteUsers",
  props: ["selected_users"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteUsers: function() {
      API.deleteUsers(Object.keys(this.selected_users).map((key) => this.selected_users[key]))
        .then(() => {
          this.$notify({
            group: "success",
            title: "Utilisateurs supprimés",
            text: "Les utilisateurs sélectionnés ont été supprimés."
          }, 3500);
          this.$emit('component', { name: '' });
          this.$emit('reload');
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de la suppression. Aucun utilisateur n'a été impacté."
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
