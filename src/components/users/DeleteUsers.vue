<template>
  <div class="popup">
    <article>
      <header>
        <h2>Suppression des utilisateurs</h2>
      </header>

      <section>
        <form @submit.prevent="deleteUser">
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
              Retour
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
          this.$emit('component', { name: '' });
          this.$emit('reload');
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
