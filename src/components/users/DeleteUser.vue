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
          this.$emit('component', { name: '' });
          this.$emit('reload');
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
