<template>
  <div class="popup">
    <article>
      <header>
        <h2>Modification de l'administrateur</h2>
      </header>

      <section>
        <form @submit.prevent="editAdmin">
          <label for="email">Email :</label>
          <input type="email" name="email" id="email" v-model="email" required>

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

          <label for="active">Actif :</label>
          <select name="active" id="active" v-model="active" required>
            <option value=""></option>
            <option value="1">✅</option>
            <option value="0">❌</option>
          </select>

          <div>
            <button
                type="submit"
                class="button"
            >
              Modifier
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
  name: "EditAdmin",
  props: ["selected_admin"],
  data() {
    return {
      email: this.selected_admin["email"],
      first_name: this.selected_admin["first_name"],
      last_name: this.selected_admin["last_name"],
      scope: this.selected_admin["scope"],
      active: this.selected_admin["active"]
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editAdmin: function() {
      API.editAdmin(this.selected_admin["admin_id"], this.email, this.first_name, this.last_name, this.scope, this.active)
        .then(() => {
          this.$emit('component', { name: '' });
          this.$emit('reload');
        });
    }
  }
};
</script>
