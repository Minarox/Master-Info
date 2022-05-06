<template>
  <div class="popup">
    <article>
      <header>
        <h2>Modification de l'utilisateur</h2>
      </header>

      <section>
        <form @submit.prevent="editUser">
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
  name: "EditUser",
  props: ["selected_user"],
  data() {
    return {
      email: this.selected_user["email"],
      first_name: this.selected_user["first_name"],
      last_name: this.selected_user["last_name"],
      device: this.selected_user["device"]
    }
  },
  mounted() {
    this.addEvents("", document.getElementsByClassName("popup")[0]);
  },
  beforeUnmount() {
    this.removeEvents("", document.getElementsByClassName("popup")[0]);
  },
  methods: {
    editUser: function() {
      API.editUser(this.selected_user["user_id"], this.email, this.first_name, this.last_name, this.device)
        .then(() => {
          this.$emit('component', { name: '' });
          this.$emit('reload');
        });
    }
  }
};
</script>