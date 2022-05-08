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
