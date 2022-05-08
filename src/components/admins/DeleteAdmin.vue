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
          this.$emit('component', { name: '' });
          this.$emit('reload');
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
