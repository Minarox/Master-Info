<template>
  <div class="popup">
    <article>
      <header>
        <h2>Suppression de l'utilisateur</h2>
      </header>

      <section>
        <form @submit.prevent="deleteUser">
          <p>Voulez vous vraiment supprimer l'utilisateur {{ full_name }} ?</p>
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
  name: "DeleteUser",
  props: ["selected_user"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteUser: function() {
      API.deleteUser(this.selected_user["user_id"])
        .then(() => {
          this.$notify({
            group: "success",
            title: "Utilisateur supprimé",
            text: "L'utilisateur à été supprimé de l'application."
          }, 3500);
          this.$emit('component', { name: '' });
          this.$emit('reload');
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de la suppression. L'utilisateur n'a pas été impacté."
          }, 3500);
          this.$emit('component', { name: '' });
        });
    }
  },
  computed: {
    full_name: function () {
      return this.selected_user["first_name"] + ' ' + this.selected_user["last_name"];
    }
  }
};
</script>

<style scoped>
form p {
  text-align: center;
}
</style>
