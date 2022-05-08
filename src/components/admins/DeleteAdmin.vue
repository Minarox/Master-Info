<template>
  <div class="popup">
    <article>
      <header>
        <h2>Suppression de l'administrateur</h2>
      </header>

      <section>
        <form @submit.prevent="deleteAdmin">
          <p>Voulez vous vraiment supprimer l'administrateur {{ full_name }} ?</p>
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
  name: "DeleteAdmin",
  props: ["selected_admin"],
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    deleteAdmin: function() {
      API.deleteAdmin(this.selected_admin["admin_id"])
        .then(() => {
          this.$notify({
            group: "success",
            title: "Administrateur supprimé",
            text: "L'administrateur à été supprimé de l'application."
          }, 3500);
          this.$emit('component', { name: '' });
          this.$emit('reload');
        })
        .catch(() => {
          this.$notify({
            group: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de la suppression. L'administrateur n'a pas été impacté."
          }, 3500);
          this.$emit('component', { name: '' });
        });
    }
  },
  computed: {
    full_name: function () {
      return this.selected_admin["first_name"] + ' ' + this.selected_admin["last_name"];
    }
  }
};
</script>

<style scoped>
form p {
  text-align: center;
}
</style>
